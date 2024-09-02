<?php
namespace miyabi_labo\Plugin\changelog_extend;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'changelog-extend' ) );
}

/**
 * 拡張更新履歴プラグイン - 設定ページクラス
 * @author 雅
 */
class Setting_Page {

	/** コンストラクタ */
	public function __construct() {
		$this->init_page();
	}

	/** ページ初期化 */
	public function init_page() {
		$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) ) : Constants::SETTING_TAB_NAME_SETTING;
		switch ( $active_tab ){
			case Constants::SETTING_TAB_NAME_SETTING:
				$form = new Setting_Form();
				add_settings_section( 'setting_section', '', array( &$form, 'display_form' ), Constants::SETTING_PAGE_NAME );
				break;
			case Constants::SETTING_TAB_NAME_CHANGELOG:
				$list = new Changelog_List();
				add_settings_section( 'changelog_section', '', array( &$list, 'display_list' ), Constants::SETTING_PAGE_NAME );
				break;
			case Constants::SETTING_TAB_NAME_PREVIEW:
				$list = new Preview_Page();
				add_settings_section( 'preview_section', '', array( &$list, 'display_page' ), Constants::SETTING_PAGE_NAME );
				break;
		}
	}

	/** 設定ページを表示する */
	public function display_page() {
?>
<div class="wrap <?php echo esc_attr( Constants::PLUGIN_ID ); ?>">
<h1>設定</h1>
<?php	if( ! empty( $_GET[ 'settings-updated' ] ) ) : ?>
<?php		if( $_GET[ 'settings-updated' ] == true ) : ?>
<div id="settings_updated" class="updated notice is-dismissible"><p><strong><?php echo esc_html( __( '登録しました。', 'changelog-extend' ) ); ?></strong></p></div>
<?php		else : ?>
<div id="settings_error" class="error notice is-dismissible"><p><strong><?php echo esc_html( __( '登録に失敗しました。', 'changelog-extend' ) ); ?></strong></p></div>
<?php		endif; ?>
<?php	endif; ?>
<h2 class="nav-tab-wrapper">
<?php
		$params = array(
				Constants::SETTING_TAB_NAME_SETTING   => __( '設定', 'changelog-extend' ),
				Constants::SETTING_TAB_NAME_CHANGELOG => __( '更新履歴', 'changelog-extend' ),
				Constants::SETTING_TAB_NAME_PREVIEW   => __( 'プレビュー', 'changelog-extend' ),
		);
		foreach ( $params as $tab => $name ) : ?>
	<a href="<?php echo esc_url( sprintf( '?page=%1$s&amp;tab=%2$s', Constants::SLUG_SETTING, $tab ) ); ?>" class="nav-tab <?php $this->display_active_tab( $tab ); ?>"><?php echo esc_html( $name ); ?></a>
<?php	endforeach; ?>
</h2>
<?php 	do_settings_sections( Constants::SETTING_PAGE_NAME ); ?>
</div>
<?php
	}

	/**
	 * 選択されたタブがパラメーターと一致する場合は表示状態にする
	 * @param string タブ名
	 */
	private function display_active_tab( $tab_name ) {
		if ( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) );
		} else {
			$active_tab = Constants::SETTING_TAB_NAME_SETTING;
		}
		if ( $active_tab == $tab_name ) {
			echo esc_attr( 'nav-tab-active' );
		} else {
			echo '';
		}
	}
}
?>
