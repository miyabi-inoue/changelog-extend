<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - 管理ページクラス
 * @author 雅
 */
class Admin {

	/** 送信処理インスタンス */
	private $submit = NULL;
	/** AJAX通信インスタンス */
	private $ajax = NULL;

	/**
	 * 初期化する
	 * @return Admin インスタンス
	 */
	public static function init() {
		// 管理者ページ以外かログインされていない場合は何もしない
		if ( ! is_admin() || ! is_user_logged_in() ) {
			return null;
		}
		return new self();
	}

	/** コンストラクタ */
	public function __construct() {
		// メニューを追加する
		add_action( 'admin_menu', array( &$this, 'add_menu' ) );

		// 拡張更新履歴プラグイン関連ページ以外はここで戻る
		if ( ! Utils::is_related_plugin() ) {
			return;
		}

		// スクリプトを追加する
		add_action( 'admin_enqueue_scripts', array( &$this, 'add_script' ) );

		// ajax通信の処理を追加する
		if ( ! empty( $_REQUEST[ 'action' ] ) ) {
			$this->ajax = new Admin_Ajax( sanitize_text_field( wp_unslash( $_REQUEST[ 'action' ] ) ) );
		}

		// 送信時の処理を追加する
		if ( ! empty( $_SERVER[ 'REQUEST_METHOD' ] ) && ! strcasecmp( sanitize_text_field( wp_unslash( $_SERVER[ 'REQUEST_METHOD' ] ) ), 'post' ) && empty( $this->ajax ) ) {
			$this->submit = new Admin_Submit();
		}
	}

	/** メニューを追加する */
	public function add_menu() {
		add_options_page(
				__( '更新履歴', 'changelog-extend' ),
				__( '更新履歴', 'changelog-extend' ),
				'manage_options',
				Constants::SLUG_SETTING,
				array( &$this, 'show_setting_form' )
		);
	}

	/** 設定ページを表示する */
	public function show_setting_form() {
		$page = new Setting_Page();
		$page->display_page();
	}

	/** スクリプトを追加する */
	public function add_script( $hook_suffix ) {
		// 選択中のタブを取得する
		if ( ! empty( $_REQUEST[ 'tab' ] ) ) {
			$tab = sanitize_text_field( wp_unslash( $_REQUEST[ 'tab' ] ) );
		} else {
			$tab = Constants::SETTING_TAB_NAME_SETTING;
		}

		// スタイルシートの読み込みを行う
		wp_enqueue_style( Constants::CSS_ID_CHANGELOG_EXTEND_ADMIN, sprintf( '%1$s/admin/form/css/%2$s-admin.css', Constants::PLUGIN_URL, Constants::PLUGIN_NAME ) );
		if ( $tab == Constants::SETTING_TAB_NAME_CHANGELOG ) {
			wp_enqueue_style( Constants::CSS_ID_CHANGELOG_LIST, sprintf( '%1$s/admin/form/css/changelog-list.css', Constants::PLUGIN_URL ) );
		}
		if ( $tab == Constants::SETTING_TAB_NAME_PREVIEW ) {
			wp_enqueue_style( Constants::CSS_ID_CHANGELOG_EXTEND, sprintf( '%1$s/css/%2$s.css', Constants::PLUGIN_URL, Constants::PLUGIN_NAME ) );
		}
		if ( in_array( $tab, array( Constants::SETTING_TAB_NAME_CHANGELOG ) ) ) {
			wp_enqueue_style( Constants::CSS_ID_JQUERY_UI, includes_url().'css/jquery-ui-dialog.min.css' );
		}

		// JavaScriptの読み込みを行う
		if ( $tab == Constants::SETTING_TAB_NAME_SETTING ) {
			wp_enqueue_script( Constants::SCRIPT_ID_SETTING, sprintf( '%1$s/admin/form/js/setting-form.js', Constants::PLUGIN_URL ), array( Constants::SCRIPT_ID_JQUERY, Constants::SCRIPT_ID_LOCALIZE ) );

			// AJAX通信用データを用意する
			$ajax_object = array(
					'ajax_url'	=>	admin_url( 'admin-ajax.php' ),
					'action'	=>	Constants::AJAX_ACTION_SETTING,
					'nonce'		=>	wp_create_nonce( Constants::AJAX_NONCE_SETTING ),
			);
			wp_localize_script( Constants::SCRIPT_ID_SETTING, Constants::SCRIPT_DATA, $ajax_object );
			wp_set_script_translations( Constants::SCRIPT_ID_SETTING, 'changelog-extend', Constants::PLUGIN_URL . '/languages' );
		}
		if ( $tab == Constants::SETTING_TAB_NAME_CHANGELOG ) {
			wp_enqueue_script( Constants::SCRIPT_ID_CHANGELOG_LIST, sprintf( '%1$s/admin/form/js/changelog-list.js', Constants::PLUGIN_URL ), array( Constants::SCRIPT_ID_JQUERY, Constants::SCRIPT_ID_JQUERY_UI, Constants::SCRIPT_ID_LOCALIZE ) );

			// AJAX通信用データを用意する
			$ajax_object = array(
					'ajax_url'	=>	admin_url( 'admin-ajax.php' ),
					'action'	=>	Constants::AJAX_ACTION_CHANGELOG_LIST,
					'nonce'		=>	wp_create_nonce( Constants::AJAX_NONCE_CHANGELOG_LIST ),
			);
			wp_localize_script( Constants::SCRIPT_ID_CHANGELOG_LIST, Constants::SCRIPT_DATA, $ajax_object );
			wp_set_script_translations( Constants::SCRIPT_ID_CHANGELOG_LIST, 'changelog-extend', Constants::PLUGIN_URL . '/languages' );
		}
		if ( in_array( $tab, array( Constants::SETTING_TAB_NAME_CHANGELOG ) ) ) {
			wp_enqueue_script( Constants::SCRIPT_ID_JQUERY_UI );
		}
		if ( in_array( $tab, array( Constants::SETTING_TAB_NAME_SETTING, Constants::SETTING_TAB_NAME_CHANGELOG ) ) ) {
			wp_enqueue_script( Constants::SCRIPT_ID_LOCALIZE );
		}
	}
}
?>
