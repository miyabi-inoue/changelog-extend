<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - 設定フォームクラス
 * @author 雅
 */
class Setting_Form {

	/**
	 * 設定フォームを表示する
	 * @param array $args パラメータ
	 */
	public function display_form( $args ) {
		// 設定を取得する
		$settings = Utils::get_settings();

		// 表示する投稿タイプのチェック状態を設定する
		$post_type_post = '';
		$post_type_page = '';
		if ( ( $settings[ Constants::SETTING_NAME_POST_TYPE ] & 0x01 ) != 0 ) {
			$post_type_post = ' checked="checked"';
		}
		if ( ( $settings[ Constants::SETTING_NAME_POST_TYPE ] & 0x02 ) != 0 ) {
			$post_type_page = ' checked="checked"';
		}
?>
<div class="ml-ce-setting-tab-content">
<form id="setting_form" action="" method="post" enctype="multipart/form-data">
<?php wp_nonce_field( Constants::CREDENTIAL_ACTION_SETTING_FORM, Constants::CREDENTIAL_NAME_SETTING_FORM ); ?>
<h3><?php echo esc_html( __( '表示設定', 'changelog-extend' ) ); ?></h3>
<dl>
<dt><?php echo esc_html( __( '表示する投稿タイプ', 'changelog-extend' ) ); ?></dt>
<dd>
	<input type="checkbox" id="post-type-post" name="<?php echo esc_attr( Constants::SETTING_NAME_POST_TYPE ); ?>_post" value="1"<?php echo $post_type_post; ?> />
	<label for="post-type-post"><?php echo esc_html( __( '投稿ページ', 'changelog-extend' ) ); ?></label><br />
	<input type="checkbox" id="post-type-page" name="<?php echo esc_attr( Constants::SETTING_NAME_POST_TYPE ); ?>_page" value="2"<?php echo $post_type_page; ?> />
	<label for="post-type-page"><?php echo esc_html( __( '固定ページ', 'changelog-extend' ) ); ?></label>
</dd>
</dl>
<h3><?php echo esc_html( __( 'タイトル設定', 'changelog-extend' ) ); ?></h3>
<dl>
<dt><?php echo esc_html( __( 'タイトル', 'changelog-extend' ) ); ?></dt>
<dd><input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_TITLE ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_TITLE ] ); ?>" /></dd>
<dt><?php echo esc_html( __( 'タイトル テンプレート', 'changelog-extend' ) ); ?></dt>
<dd>
	<input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_TITLE_TEMPLATE ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_TITLE_TEMPLATE ] ); ?>" /><br />
	<span><?php
		echo esc_html( sprintf(
				// translators: %1$s: title variable
				__( 'テンプレート内で使用可能な変数は「%1$s」です。', 'changelog-extend' ),
				Constants::TITLE_TEMPLATE_TITLE
		) );
		?></span>
</dd>
</dl>
<h3><?php echo esc_html( __( 'リスト設定', 'changelog-extend' ) ); ?></h3>
<dl>
<dt><?php echo esc_html( __( '開始タグ', 'changelog-extend' ) ); ?></dt>
<dd><input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_LIST_START ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_LIST_START ] ); ?>" /></dd>
<dt><?php echo esc_html( __( '終了タグ', 'changelog-extend' ) ); ?></dt>
<dd><input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_LIST_END ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_LIST_END ] ); ?>" /></dd>
<dt><?php echo esc_html( __( '行テンプレート', 'changelog-extend' ) ); ?></dt>
<dd>
	<input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_ROW_TEMPLATE ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_ROW_TEMPLATE ] ); ?>" /><br />
	<span><?php
		echo esc_html( sprintf(
				// translators: %1$s: date variable, %2$s: now variable, %3$s: title variable
				__( 'テンプレート内で使用可能な変数は「%1$s」「%2$s」「%3$s」です。', 'changelog-extend' ),
				Constants::ROW_TEMPLATE_DATE,
				Constants::ROW_TEMPLATE_NEW,
				Constants::ROW_TEMPLATE_TITLE
		) );
?><br /><?php
		echo esc_html( sprintf(
				// translators: %1$s: date variable, %2$s: date variable, %3$s: date variable
				__( '「%1$s」に書式を指定する場合は「%2$s」とします。例　%3$s', 'changelog-extend' ),
				Constants::ROW_TEMPLATE_DATE,
				str_replace( '([^%]+)', __( '[書式]', 'changelog-extend' ), Constants::ROW_TEMPLATE_DATE_WITH_FORMAT ),
				str_replace( '([^%]+)', 'Y/m/d', Constants::ROW_TEMPLATE_DATE_WITH_FORMAT )
		) );
?></span>
</dd>
<dt><?php echo esc_html( __( '行数', 'changelog-extend' ) ); ?></dt>
<dd><input class="ml-ce-row-to-display" type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_ROWS_TO_DISPLAY ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_ROWS_TO_DISPLAY ] ); ?>" /></dd>
<dt><?php echo esc_html( __( '日付書式', 'changelog-extend' ) ); ?></dt>
<dd><input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_DEFAULT_DATE_FORMAT ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_DEFAULT_DATE_FORMAT ] ); ?>" /></dd>
<dt><?php echo esc_html( __( 'NEW表示日数', 'changelog-extend' ) ); ?></dt>
<dd><input class="ml-ce-days-to-display-as-new" type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_DAYS_TO_DISPLAY_AS_NEW ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_DAYS_TO_DISPLAY_AS_NEW ] ); ?>" />日</dd>
<dt><?php echo esc_html( __( 'NEWアイコン画像', 'changelog-extend' ) ); ?></dt>
<dd>
	<input type="text" name="<?php echo esc_attr( Constants::SETTING_NAME_NEW_ICON ); ?>" value="<?php echo esc_attr( $settings[ Constants::SETTING_NAME_NEW_ICON ] ); ?>" /><br />
	<span><?php echo esc_html( __( '画像を表示する場合に指定します。ドキュメントルートからのパスを入力してください。', 'changelog-extend' ) ); ?></span>
</dd>
</dl>
<h4><?php echo esc_html( __( '行タイトル 接頭語', 'changelog-extend' ) ); ?></h4>
<dl>
<dt><?php echo esc_html( __( '未選択時', 'changelog-extend' ) ); ?></dt>
<dd>
	<select id="default_row_title_prefix" name="<?php echo esc_attr( Constants::SETTING_NAME_DEFAULT_ROW_TITLE_PREFIX ); ?>">
		<option value=""><?php echo esc_html( __( '(表示しない)', 'changelog-extend' ) ); ?></option>
<?php
	foreach ( $settings[ Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS ] as $item ) :
		$selected = '';
		if ( $item == $settings[ Constants::SETTING_NAME_DEFAULT_ROW_TITLE_PREFIX ] ) {
			$selected = ' selected="selected"';
		}
?>
		<option value="<?php echo esc_attr( $item ); ?>"<?php echo $selected; ?>><?php echo esc_html( $item ); ?></option>
<?php
	endforeach;
?>
	</select>
</dd>
<dt><?php echo esc_html( __( '選択項目', 'changelog-extend' ) ); ?></dt>
<dd>
	<textarea id="row_title_prefix_items" name="<?php echo esc_attr( Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS ); ?>"><?php echo esc_textarea( implode( "\n", $settings[ Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS ] ) ); ?></textarea><br />
	<span><?php
	echo esc_html( __( '更新履歴設定で選択肢として表示されます。改行して入力してください。', 'changelog-extend' ) );
?><br /><?php
	echo esc_html( __( 'htmlは使用できません。', 'changelog-extend' ) );
?></span>
</dd>
</dl>
<h4><?php echo esc_html( __( '行タイトル 接尾語', 'changelog-extend' ) ); ?></h4>
<dl>
<dt><?php echo esc_html( __( '未選択時', 'changelog-extend' ) ); ?></dt>
<dd>
	<select id="default_row_title_suffix" name="<?php echo esc_attr( Constants::SETTING_NAME_DEFAULT_ROW_TITLE_SUFFIX ); ?>">
		<option value=""><?php echo esc_html( __( '(表示しない)', 'changelog-extend' ) ); ?></option>
<?php
	foreach ( $settings[ Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ] as $item ) :
		$selected = '';
		if ( $item == $settings[ Constants::SETTING_NAME_DEFAULT_ROW_TITLE_SUFFIX ] ) {
			$selected = ' selected="selected"';
		}
?>
		<option value="<?php echo esc_attr( $item ); ?>"<?php echo $selected; ?>><?php echo esc_html( $item ); ?></option>
<?php
	endforeach;
?>
	</select>
</dd>
<dt><?php echo esc_html( __( '選択項目', 'changelog-extend' ) ); ?></dt>
<dd>
	<textarea id="row_title_suffix_items" name="<?php echo esc_attr( Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ); ?>"><?php echo esc_textarea( implode( "\n", $settings[ Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ] ) ); ?></textarea><br />
	<span><?php
	echo esc_html( __( '更新履歴設定で選択肢として表示されます。改行して入力してください。', 'changelog-extend' ) );
?><br /><?php
	echo esc_html( __( 'htmlは使用できません。', 'changelog-extend' ) );
?></span>
</dd>
</dl>
<p class="submit">
	<?php submit_button( __( '保存', 'changelog-extend' ), 'primary', 'submit', false ); ?>
	<button type="button" id="init" class="button" data-command="<?php echo esc_attr( Constants::AJAX_COMMAND_SETTING_INIT_SETTING ); ?>"><?php echo esc_html( __( '初期値に戻す', 'changelog-extend' ) ); ?></button>
</p>
</form>
</div>
<?php
	}
}
