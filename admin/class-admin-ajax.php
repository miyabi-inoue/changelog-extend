<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - 管理ページ - AJAX通信クラス
 * @author 雅
 */
class Admin_Ajax {

	/**
	 * コンストラクタ
	 * @param string $action アクション
	 */
	public function __construct( $action ) {
		// ajax通信の処理を追加する
		add_action( 'wp_ajax_' . $action, array( &$this, 'response') );
		add_action( 'wp_ajax_nopriv_' . $action, array( &$this, 'response' ) );
	}

	/** AJAX通信の応答を行う */
	public function response() {
		if ( ! array_key_exists( 'command', $_REQUEST ) ) {
			wp_die( __( 'パラメータが正しくありません。', 'changelog-extend' ), 403 );
		}
		if ( check_ajax_referer( Constants::AJAX_NONCE_SETTING, 'nonce', false ) ) {
			$command = sanitize_text_field( wp_unslash( $_REQUEST[ 'command' ] ) );
			if ( $command == Constants::AJAX_COMMAND_SETTING_INIT_SETTING) {
				$this->init_settings();
			}
		} elseif ( check_ajax_referer( Constants::AJAX_NONCE_CHANGELOG_LIST, 'nonce', false ) ) {
			$command = sanitize_text_field( wp_unslash( $_REQUEST[ 'command' ] ) );
			if ( $command == Constants::AJAX_COMMAND_CHANGELOG_LIST_UPDATE_HIDDEN_STATUS ) {
				$this->update_hidden_status();
			} elseif ( $command == Constants::AJAX_COMMAND_CHANGELOG_LIST_UPDATE_PREFIX ) {
				$this->update_prefix();
			} elseif ( $command == Constants::AJAX_COMMAND_CHANGELOG_LIST_UPDATE_SUFFIX ) {
				$this->update_suffix();
			} elseif ( $command == Constants::AJAX_COMMAND_CHANGELOG_LIST_GET_CHANGELOG_DATA ) {
				$this->get_changelog_data();
			} elseif ( $command == Constants::AJAX_COMMAND_CHANGELOG_LIST_DELETE_CHANGELOG_DATA ) {
				$this->delete_changelog_data();
			}
		} else {
			wp_die( __( '認証に失敗しました。', 'changelog-extend' ), 403 );
		}
		wp_die( __( 'コマンドが正しくありません。', 'changelog-extend' ), 403 );
	}

	/** 設定値を初期値に戻す */
	private function init_settings() {
		update_option( Constants::SETTING_NAME, ML_CE_SETTING_DEFAULT_VALUES );

		wp_die( __( '初期化しました。', 'changelog-extend' ), 203 );
	}

	/** 非表示状態を更新する */
	private function update_hidden_status() {
		if ( ! array_key_exists( 'id', $_REQUEST ) || ! array_key_exists( 'check', $_REQUEST ) ) {
			wp_die( __('パラメータが正しくありません。', 'changelog-extend' ), 403 );
		}

		// 元データを取得し非表示状態を更新する
		$id = sanitize_text_field( wp_unslash( $_REQUEST[ 'id' ] ) );
		$check = sanitize_text_field( wp_unslash( $_REQUEST[ 'check' ] ) );
		$meta_value = Utils::get_changelog_meta_value( $id );
		$meta_value[ Constants::POST_META_VALUE_KEY_HIDDEN ] = 0;
		if ( strcasecmp( $check, 'true' ) == 0 ) {
			$meta_value[ Constants::POST_META_VALUE_KEY_HIDDEN ] = 1;
		}
		update_post_meta( $id, Constants::POST_META_KEY, $meta_value );

		wp_die( __( '更新しました。', 'changelog-extend' ), 203 );
	}

	/** タイトル接頭語を更新する */
	private function update_prefix() {
		if ( ! array_key_exists( 'id', $_REQUEST ) || ! array_key_exists( 'value', $_REQUEST ) ) {
			wp_die( __( 'パラメータが正しくありません。', 'changelog-extend' ), 403 );
		}

		// 元データを取得し非表示状態を更新する
		$id = sanitize_text_field( wp_unslash( $_REQUEST[ 'id' ] ) );
		$value = sanitize_text_field( wp_unslash( $_REQUEST[ 'value' ] ) );
		$meta_value = Utils::get_changelog_meta_value( $id );
		$meta_value[ Constants::POST_META_VALUE_KEY_PREFIX ] = $value;
		update_post_meta( $id, Constants::POST_META_KEY, $meta_value );

		wp_die( __( '更新しました。', 'changelog-extend' ), 203 );
	}

	/** タイトル接尾語を更新する */
	private function update_suffix() {
		if ( ! array_key_exists( 'id', $_REQUEST ) || ! array_key_exists( 'value', $_REQUEST ) ) {
			wp_die( __( 'パラメータが正しくありません。', 'changelog-extend' ), 403 );
		}

		// 元データを取得し非表示状態を更新する
		$id = sanitize_text_field( wp_unslash( $_REQUEST[ 'id' ] ) );
		$value = sanitize_text_field( wp_unslash( $_REQUEST[ 'value' ] ) );
		$meta_value = Utils::get_changelog_meta_value( $id );
		$meta_value[ Constants::POST_META_VALUE_KEY_SUFFIX ] = $value;
		update_post_meta( $id, Constants::POST_META_KEY, $meta_value );

		wp_die( __( '更新しました。', 'changelog-extend' ), 203 );
	}

	/** 更新履歴データを取得する */
	private function get_changelog_data() {
		if ( ! array_key_exists( 'id', $_REQUEST ) ) {
			wp_die( __( 'パラメータが正しくありません。', 'changelog-extend' ), 403 );
		}

		// 対象データを取得して返す
		$id = sanitize_text_field( wp_unslash( $_REQUEST[ 'id' ] ) );
		$changelog_extend = new Changelog_Extend_Entity( get_post( $id ) );
		$result = array(
				'id'        => $changelog_extend->get_id(),
				'type'      => $changelog_extend->get_type(),
				'date_time' => $changelog_extend->get_date_time()->format( 'Y-m-d\TH:i:s' ),
				'title'     => $changelog_extend->get_title(),
				'hidden'    => $changelog_extend->get_hidden(),
				'prefix'    => $changelog_extend->get_prefix(),
				'suffix'    => $changelog_extend->get_suffix(),
				'comment'   => $changelog_extend->get_comment(),
		);
		wp_send_json( $result );
	}

	/** 更新履歴データを削除する */
	private function delete_changelog_data() {
		if ( ! array_key_exists( 'delete_ids', $_REQUEST ) ) {
			wp_die( __( 'パラメータが正しくありません。', 'changelog-extend' ), 403 );
		}

		// 更新履歴データを削除する
		$ids = explode( ',', sanitize_text_field( wp_unslash( $_REQUEST[ 'delete_ids' ] ) ) );
		foreach ( $ids as $id ) {
			$post = get_post( $id );
			if ( $post->post_type == Constants::POST_TYPE_CHANGELOG ) {
				wp_delete_post( $id, true );
			}
		}

		wp_die( __( '削除しました。', 'changelog-extend' ), 203 );
	}
}
?>
