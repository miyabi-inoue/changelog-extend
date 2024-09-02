<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - プレビューページクラス
 * @author 雅
 */
class Preview_Page {

	/**
	 * プレビューページを表示する
	 * @param array $args パラメータ
	 */
	public function display_page($args) {
?>
<div class="ml-ce-setting-tab-content">
<?php	echo do_shortcode( '[' . Constants::SHORTCODE_CHANGELOGEX . ']' ); ?>
</div>
<?php
	}
}
