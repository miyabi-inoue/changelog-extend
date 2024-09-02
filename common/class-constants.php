<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - 定数クラス
 * @author 雅
 */
class Constants {

	/** プラグイン名 */
	public const PLUGIN_NAME = ML_CE_PLUGIN_NAME;
	/** プラグインURL */
	public const PLUGIN_URL = ML_CE_PLUGIN_URL;
	/** プラグインID */
	public const PLUGIN_ID = 'ml-' . self::PLUGIN_NAME;

	/** 投稿タイプ - 投稿 */
	public const POST_TYPE_POST = 'post';
	/** 投稿タイプ - 固定ページ */
	public const POST_TYPE_PAGE = 'page';
	/** 投稿タイプ - 更新履歴 */
	public const POST_TYPE_CHANGELOG = 'changelog';

	/** 投稿メタキー - 更新履歴 */
	public const POST_META_KEY = '_changelog';
	/** 投稿メタキー - 非表示フラグ */
	public const POST_META_VALUE_KEY_HIDDEN = 'hidden';
	/** 投稿メタキー - 接頭語 */
	public const POST_META_VALUE_KEY_PREFIX = 'prefix';
	/** 投稿メタキー - 接尾後 */
	public const POST_META_VALUE_KEY_SUFFIX = 'suffix';
	/** 投稿メタキー - コメント */
	public const POST_META_VALUE_KEY_COMMENT = 'comment';

	/** 設定名 */
	public const SETTING_NAME = self::PLUGIN_NAME;
	/** 設定名 - 設定 - 表示する投稿タイプ */
	public const SETTING_NAME_POST_TYPE = 'ce_post_type';
	/** 設定名 - 設定 - タイトル */
	public const SETTING_NAME_TITLE = 'ce_title';
	/** 設定名 - 設定 - タイトルテンプレート */
	public const SETTING_NAME_TITLE_TEMPLATE = 'ce_title_template';
	/** 設定名 - 設定 - リスト - 開始タグ */
	public const SETTING_NAME_LIST_START = 'ce_list_tag_start';
	/** 設定名 - 設定 - リスト - 終了タグ */
	public const SETTING_NAME_LIST_END = 'ce_list_tag_end';
	/** 設定名 - 設定 - 行テンプレート */
	public const SETTING_NAME_ROW_TEMPLATE = 'ce_row_template';
	/** 設定名 - 設定 - 行タイトル - 接頭語 - 省略時 */
	public const SETTING_NAME_DEFAULT_ROW_TITLE_PREFIX = 'ce_default_row_title_prefix';
	/** 設定名 - 設定 - 行タイトル - 接尾語 - 省略時 */
	public const SETTING_NAME_DEFAULT_ROW_TITLE_SUFFIX = 'ce_default_row_title_suffix';
	/** 設定名 - 設定 - 行タイトル - 接頭語 - 選択肢 */
	public const SETTING_NAME_ROW_TITLE_PREFIX_ITEMS = 'ce_row_title_prefix_items';
	/** 設定名 - 設定 - 行タイトル - 接尾語 - 選択肢 */
	public const SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS = 'ce_row_title_suffix_items';
	/** 設定名 - 設定 - 表示する行数 */
	public const SETTING_NAME_ROWS_TO_DISPLAY = 'ce_rows_to_display';
	/** 設定名 - 設定 - 日付書式 */
	public const SETTING_NAME_DEFAULT_DATE_FORMAT = 'ce_date_format';
	/** 設定名 - 設定 - NEW表示日数 */
	public const SETTING_NAME_DAYS_TO_DISPLAY_AS_NEW = 'ce_days_to_display_as_new';
	/** 設定名 - 設定 - NEWアイコン画像 */
	public const SETTING_NAME_NEW_ICON = 'ce_new_icon';

	/** 設定値 - 行タイトル接頭語／接尾語を非表示にする */
	public const SETTING_VALUE_HIDDEN_AFFIX = '@{HIDDEN}@';

	/** html設定値項目一覧 */
	public const SETTING_HTML_VALUES = array(
			self::SETTING_NAME_TITLE_TEMPLATE,
			self::SETTING_NAME_LIST_START,
			self::SETTING_NAME_LIST_END,
			self::SETTING_NAME_ROW_TEMPLATE,
	);

	/** タイトルテンプレート - タイトル */
	public const TITLE_TEMPLATE_TITLE = '%title%';
	/** 行テンプレート - 日付 */
	public const ROW_TEMPLATE_DATE = '%date%';
	/** 行テンプレート - 書式付き日付 */
	public const ROW_TEMPLATE_DATE_WITH_FORMAT = '%date:([^%]+)%';
	/** 行テンプレート - 新着表示 */
	public const ROW_TEMPLATE_NEW = '%new%';
	/** 行テンプレート - タイトル */
	public const ROW_TEMPLATE_TITLE = '%title%';

	/*
	 * 行テンプレートの置換文字は下記を想定(案)
	 * %date[:format]%	日付 [format]部分は省略可
	 *                  省略した場合は Y/m/d
	 *                  時刻も表示可とする
	 * %new%            NEWを表示する(設定でアイコンも表示可)
	 * %title%			タイトル
	 * %detail%			詳細
	 */

	/** スラグ - 設定ページ */
	public const SLUG_SETTING = self::PLUGIN_NAME . '-setting';
	/** スラグ - 設定エラー表示用 */
	public const SLUG_SETTINGS_ERROR = self::PLUGIN_NAME . '-settings-error';

	/** スラグ - プラグイン関連ページ一覧 */
	public const SLUG_RELATED_PAGES = array(
			self::SLUG_SETTING,
	);

	/** 設定ページ名 */
	public const SETTING_PAGE_NAME = self::PLUGIN_NAME . '-settings';

	/** 設定ページ - タブ名 - 設定 */
	public const SETTING_TAB_NAME_SETTING = 'tab-setting';
	/** 設定ページ - タブ名 - 更新履歴 */
	public const SETTING_TAB_NAME_CHANGELOG = 'tab-changelog';
	/** 設定ページ - タブ名 - プレビュー */
	public const SETTING_TAB_NAME_PREVIEW = 'tab-preview';

	/** アクション - 設定フォーム */
	public const CREDENTIAL_ACTION_SETTING_FORM = self::PLUGIN_NAME . '-setting-action';
	/** アクション - 更新履歴一覧 - 編集フォーム */
	public const CREDENTIAL_ACTION_CHANGELOG_LIST_EDIT_FORM = self::PLUGIN_NAME . '-changelog-list-edit-action';

	/** キー - 設定フォーム */
	public const CREDENTIAL_NAME_SETTING_FORM = self::PLUGIN_NAME . '-setting-key';
	/** キー - 更新履歴一覧 - 編集フォーム */
	public const CREDENTIAL_NAME_CHANGELOG_LIST_EDIT_FORM = self::PLUGIN_NAME . '-changelog-list-edit-key';

	/** SETTINGS CODE */
	public const SETTINGS_CODE = self::PLUGIN_NAME . 'settings-message';

	/** スタイルシートID - 共通 */
	public const CSS_ID_CHANGELOG_EXTEND = self::PLUGIN_NAME;
	/** スタイルシートID - 管理ページ共通 */
	public const CSS_ID_CHANGELOG_EXTEND_ADMIN = self::PLUGIN_NAME . '-admin';
	/** スタイルシートID - 更新履歴一覧 */
	public const CSS_ID_CHANGELOG_LIST = self::PLUGIN_NAME . '-changelog-list';

	/** スタイルシートID - jQuery UI */ 
	public const CSS_ID_JQUERY_UI = 'jquery-ui-dialog-min';

	/** スクリプトID - 設定 */
	public const SCRIPT_ID_SETTING = self::PLUGIN_NAME . '-setting';
	/** スクリプトID - 更新履歴一覧 */
	public const SCRIPT_ID_CHANGELOG_LIST = self::PLUGIN_NAME . '-changelog-list';

	/** スクリプトID - jQuery */
	public const SCRIPT_ID_JQUERY = 'jquery';
	/** スクリプトID - jQuery UI */
	public const SCRIPT_ID_JQUERY_UI = 'jquery-ui-dialog';
	/** スクリプトID - Localize */
	public const SCRIPT_ID_LOCALIZE = 'wp-i18n';

	/** スクリプトデータ */
	public const SCRIPT_DATA = 'ml_ec_js_data';

	/** ajaxアクション - 設定フォーム */
	public const AJAX_ACTION_SETTING = 'ajax-action-setting';
	/** ajaxアクション - 更新履歴一覧 */
	public const AJAX_ACTION_CHANGELOG_LIST = 'ajax-action-changelog-list';

	/** ajaxデータ - 設定フォーム */
	public const AJAX_NONCE_SETTING = 'ml-ec-nonce-setting';
	/** ajaxデータ - 更新履歴一覧 */
	public const AJAX_NONCE_CHANGELOG_LIST = 'ml-ec-nonce-changelog-list';

	/** ajaxコマンド - 設定 - 設定タブ - 初期化 */
	public const AJAX_COMMAND_SETTING_INIT_SETTING = 'init-setting';
	/** ajaxコマンド - 更新履歴一覧 - 非表示状態更新 */
	public const AJAX_COMMAND_CHANGELOG_LIST_UPDATE_HIDDEN_STATUS = 'update-hidden-status';
	/** ajaxコマンド - 更新履歴一覧 - 接頭語更新 */
	public const AJAX_COMMAND_CHANGELOG_LIST_UPDATE_PREFIX = 'update-prefix';
	/** ajaxコマンド - 更新履歴一覧 - 接尾後更新 */
	public const AJAX_COMMAND_CHANGELOG_LIST_UPDATE_SUFFIX = 'update-suffix';
	/** ajaxコマンド - 更新履歴一覧 - 更新履歴情報取得 */
	public const AJAX_COMMAND_CHANGELOG_LIST_GET_CHANGELOG_DATA = 'get-changelog-data'; 
	/** ajaxコマンド - 更新履歴一覧 - 更新履歴削除 */
	public const AJAX_COMMAND_CHANGELOG_LIST_DELETE_CHANGELOG_DATA = 'delete-changelog-data';

	/** ショートコード - 接頭語 */
	public const SHORTCODE_PREFIX = 'ml_ce_';
	/** ショートコード - 更新履歴を表示する */
	public const SHORTCODE_CHANGELOGEX = self::SHORTCODE_PREFIX . 'changelogex';
}

/** 更新履歴一覧 - 接頭語／接尾語共通選択肢 */
define( __NAMESPACE__ . '\ML_CE_CHANGELOG_LIST_COMMON_AFFIX_SELECT_VALUE', array(
		''                                    => __( '(未選択)', 'changelog-extend' ),
		Constants::SETTING_VALUE_HIDDEN_AFFIX => __( '(表示しない)', 'changelog-extend' ),
) );

/** 初期設定値 */
define( __NAMESPACE__ . '\ML_CE_SETTING_DEFAULT_VALUES', array(
		Constants::SETTING_NAME_POST_TYPE                => 0x01 | 0x02,
		Constants::SETTING_NAME_TITLE                    => __( '更新履歴', 'changelog-extend' ),
		Constants::SETTING_NAME_TITLE_TEMPLATE           => esc_attr( '<h2 class="ce_title">%title%</h2>' ),
		Constants::SETTING_NAME_LIST_START               => esc_attr( '<ul class="ce_changelog_list">' ),
		Constants::SETTING_NAME_LIST_END                 => esc_attr( '</ul>' ),
		Constants::SETTING_NAME_ROW_TEMPLATE             => esc_attr( '<li class="ce_row"><span class="ce_row_date">' . Constants::ROW_TEMPLATE_DATE . '</span>%new%<span class="ce_row_title">' . Constants::ROW_TEMPLATE_TITLE . '</span></li>' ),
		Constants::SETTING_NAME_DEFAULT_ROW_TITLE_PREFIX => '',
		Constants::SETTING_NAME_DEFAULT_ROW_TITLE_SUFFIX => __( 'を追加しました。', 'changelog-extend' ),
		Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS   => array(),
		Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS   => array(
				__( 'を追加しました。', 'changelog-extend' ),
				__( 'を更新しました。', 'changelog-extend' ),
				__( 'を公開しました。', 'changelog-extend' ),
		),
		Constants::SETTING_NAME_ROWS_TO_DISPLAY          => 10,
		Constants::SETTING_NAME_DAYS_TO_DISPLAY_AS_NEW   => 7,
		Constants::SETTING_NAME_DEFAULT_DATE_FORMAT      => 'Y/m/d',
		Constants::SETTING_NAME_NEW_ICON                 => '',
) );
?>
