<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - プラグイン登録クラス
 * @author 雅
 */
class Plugin_Register {

	/** プラグインを有効化する */
	public static function activate() {
		// 設定値を初期化する
		update_option( Constants::SETTING_NAME, ML_CE_SETTING_DEFAULT_VALUES );
	}

	/** プラグインを無効化する */
	public static function deactivate() {
		// 設定値を削除する
		delete_option( Constants::SETTING_NAME );
	}
}
?>
