<?php
namespace miyabi_labo\Plugin\changelog_extend;

// WP_UNINSTALL_PLUGINが定義されているかチェックする
if (! defined ( 'WP_UNINSTALL_PLUGIN' )) {
	die ();
}

// 設定を削除する
delete_option ( Constants::SETTING_NAME );
?>
