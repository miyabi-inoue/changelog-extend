<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン -  管理ページ - 送信処理クラス
 * @author 雅
 */
class Admin_Submit {

	/** コンストラクタ */
	public function __construct() {
		// 送信時の処理を追加する
		add_action( 'admin_init', array( &$this, 'save' ) );
	}

	/** 保存処理を行う */
	public function save() {
		if ( ! current_user_can( 'manage_options' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			wp_die( __( 'You are not allowed to access this part of the site', 'changelog-extend' ) );
		}

		// 設定フォームからの保存処理を行う
		if ( ! empty( $_REQUEST[ Constants::CREDENTIAL_NAME_SETTING_FORM ] ) && check_admin_referer( Constants::CREDENTIAL_ACTION_SETTING_FORM, Constants::CREDENTIAL_NAME_SETTING_FORM ) ) {
			$this->save_from_setting_form();
		// 更新履歴一覧編集フォームからの保存処理を行う
		} elseif ( ! empty( $_REQUEST[ Constants::CREDENTIAL_NAME_CHANGELOG_LIST_EDIT_FORM ] ) && check_admin_referer( Constants::CREDENTIAL_ACTION_CHANGELOG_LIST_EDIT_FORM, Constants::CREDENTIAL_NAME_CHANGELOG_LIST_EDIT_FORM ) ) {
			$this->save_from_changelog_edit_from();
		}
	}

	/** 保存処理を行う - 設定フォーム */
	private function save_from_setting_form() {
		// 設定を取得する
		$settings = Utils::get_settings();
		$settings[ Constants::SETTING_NAME_POST_TYPE ] = 0;
		if ( ! empty( $_REQUEST[ Constants::SETTING_NAME_POST_TYPE . '_' . Constants::POST_TYPE_POST ] ) ) {
			$settings[ Constants::SETTING_NAME_POST_TYPE ] |= 0x01;
		}
		if ( ! empty( $_REQUEST[ Constants::SETTING_NAME_POST_TYPE . '_' . Constants::POST_TYPE_PAGE ] ) ) {
			$settings[ Constants::SETTING_NAME_POST_TYPE ] |= 0x02;
		}
		$textarea_fields = array(
				Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS,
				Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS,
		);
		foreach ( array_keys( ML_CE_SETTING_DEFAULT_VALUES ) as $name ) {
			if ( ! array_key_exists( $name, $_REQUEST ) ) {
				continue;
			}
			if ( empty( $_REQUEST[ $name ] ) ) {
				unset( $settings[ $name ] );
				continue;
			}
			if ( in_array( $name, Constants::SETTING_HTML_VALUES ) ) {
				$settings[ $name ] = esc_attr( wp_kses_post( wp_unslash( $_REQUEST[ $name ] ) ) );
			} elseif ( in_array( $name, $textarea_fields ) ) {
				$settings[ $name ] = array_values( array_filter( preg_split( "/\r\n|\r|\n/", sanitize_textarea_field( wp_unslash( $_REQUEST[ $name ] ) ) ) ) );
			} else {
				$settings[ $name ] = sanitize_text_field( wp_unslash( $_REQUEST[ $name ] ) );
			}
		}
		$settings = wp_parse_args( array_filter( $settings ), ML_CE_SETTING_DEFAULT_VALUES );
		update_option( Constants::SETTING_NAME, $settings );

		// メッセージを設定する
		add_settings_error( Constants::SLUG_SETTINGS_ERROR, Constants::SETTINGS_CODE, __( '保存しました。', 'changelog-extend' ), 'success' );
	}

	/** 保存処理を行う - 更新履歴編集フォーム */
	private function save_from_changelog_edit_from() {
		if ( ! empty( $_REQUEST[ 'type' ] ) && ! in_array( $_REQUEST[ 'type' ], array( Constants::POST_TYPE_POST, Constants::POST_TYPE_PAGE ) ) ) {
			if ( ! array_key_exists( 'title', $_REQUEST ) || ! array_key_exists( 'date_time', $_REQUEST ) ) {
				add_settings_error( Constants::SLUG_SETTINGS_ERROR, Constants::SETTINGS_CODE, __( 'パラメータが正しくありません。', 'changelog-extend' ) );
				return;
			}
			$title = '';
			if ( ! empty( $_REQUEST[ 'title' ] ) ) {
				$title = sanitize_text_field( wp_unslash( $_REQUEST[ 'title' ] ) );
			}
			$date_time = '';
			if ( ! empty( $_REQUEST[ 'date_time' ] ) ) {
				$date_time = sanitize_text_field( wp_unslash( $_REQUEST[ 'date_time' ] ) );
			}
			if ( empty( $_REQUEST[ 'id' ] ) ) {
				$post_id = wp_insert_post( array(
						'post_type'   => sanitize_text_field( wp_unslash( $_REQUEST[ 'type' ] ) ),
						'post_status' => 'publish',
						'post_title'  => $title,
						'post_date'   => $date_time,
				) );
			} else {
				$post_id = wp_update_post( array(
						'ID'          => (int)$_REQUEST[ 'id' ],
						'post_status' => 'publish',
						'post_title'  => $title,
						'post_date'   => $date_time,
				) );
			}
		} else {
			if ( empty( $_REQUEST[ 'id' ] ) ) {
				add_settings_error( Constants::SLUG_SETTINGS_ERROR, Constants::SETTINGS_CODE, __( 'パラメータが正しくありません。', 'changelog-extend' ) );
				return;
			}
			$post_id = sanitize_text_field( wp_unslash( $_REQUEST[ 'id' ] ) );
		}

		$meta_value = Utils::get_changelog_meta_value( $post_id );
		$meta_value[ Constants::POST_META_VALUE_KEY_HIDDEN ] = 0;
		if ( ! empty( $_REQUEST[ 'hidden' ] ) ) {
			$meta_value[ Constants::POST_META_VALUE_KEY_HIDDEN ] = sanitize_text_field( wp_unslash( $_REQUEST[ 'hidden' ] ) );
		}
		if ( ! empty( $_REQUEST[ 'prefix' ] ) ) {
			$meta_value[ Constants::POST_META_VALUE_KEY_PREFIX ] = sanitize_text_field( wp_unslash( $_REQUEST[ 'prefix' ] ) );
		}
		if ( ! empty( $_REQUEST[ 'suffix' ] ) ) {
			$meta_value[ Constants::POST_META_VALUE_KEY_SUFFIX ] = sanitize_text_field( wp_unslash( $_REQUEST[ 'suffix' ] ) );
		}
		if ( ! empty( $_REQUEST[ 'comment' ] ) ) {
			$meta_value[ Constants::POST_META_VALUE_KEY_COMMENT ] = sanitize_textarea_field( wp_unslash( $_REQUEST[ 'comment' ] ) );
		}
		update_post_meta( $post_id, Constants::POST_META_KEY, $meta_value );

		// メッセージを設定する
		add_settings_error( Constants::SLUG_SETTINGS_ERROR, Constants::SETTINGS_CODE, __( '登録しました。', 'changelog-extend' ), 'success' );
	}
}
?>
