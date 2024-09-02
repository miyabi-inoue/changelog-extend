<?php
namespace miyabi_labo\Plugin\changelog_extend;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * 拡張更新履歴プラグイン - 更新履歴リストクラス
 * @author 雅
 */
class Changelog_List extends \WP_List_Table {

	/** 設定 */
	private $settings;

	/** コンストラクタ */
	public function __construct() {
		parent::__construct( array(
				'singular' => 'chagelog',
				'plural'   => 'changelogs',
				'ajax'     => true,
		) );

		// 設定を取得する
		$this->settings = Utils::get_settings();
	}

	/** 表示するデータを用意する */
	public function prepare_items() {
		// 列の設定を行う
		$columns = $this->get_columns();
		$hidden = array( 'id' );
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// 投稿データを取得する
		$params = array(
				'numberposts' => -1,
				'post_type'   => array( Constants::POST_TYPE_CHANGELOG ),
		);
		if ( ( $this->settings[ Constants::SETTING_NAME_POST_TYPE ] & 0x01 ) != 0 ) {
			$params[ 'post_type' ][] = Constants::POST_TYPE_POST;
		}
		if ( ( $this->settings[ Constants::SETTING_NAME_POST_TYPE ] & 0x02 ) != 0 ) {
			$params[ 'post_type' ][] = Constants::POST_TYPE_PAGE;
		}
		$posts = get_posts( $params );
		if ( ! is_array( $posts ) ) {
			return;
		}

		// 表示するデータを設定する
		$this->items = array();
		foreach ( $posts as $post ) {
			$changelog_extend = new Changelog_Extend_Entity( $post );
			$this->items[] = array(
					'id'        => $changelog_extend->get_id(),
					'type'      => $changelog_extend->get_type(),
					'date_time' => $changelog_extend->get_date_time()->format( get_option( 'date_format' ) . "\n" . get_option( 'time_format' ) ),
					'title'     => $changelog_extend->get_title(),
					'hidden'    => $changelog_extend->get_hidden(),
					'prefix'    => $changelog_extend->get_prefix(),
					'suffix'    => $changelog_extend->get_suffix(),
					'comment'   => $changelog_extend->get_comment(),
			);
		}

		$total_items = count( $this->items );
		$per_page = 20;

		// ページネーションを行う
		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
		) );

		// 表示するアイテムを調整する
		$current_page = $this->get_pagenum();
		$this->items = array_slice(
				$this->items,
				$per_page * ($current_page - 1),
				$per_page
		);
	}

	/**
	 * 列を取得する
	 * @return array 列情報
	 */
	public function get_columns() {
		return array(
				'cb'        => __( 'チェックボックス', 'changelog-extend' ),
				'hidden_cb' => __( '非表示', 'changelog-extend' ),
				'id'        => __( 'ID', 'changelog-extend' ),
				'date_time' => __( '更新日', 'changelog-extend' ),
				'prefix'    => __( 'タイトル接頭語', 'changelog-extend' ),
				'title'     => __( 'タイトル', 'changelog-extend' ),
				'suffix'    => __( 'タイトル接尾語', 'changelog-extend' ),
				'comment'   => __( 'コメント', 'changelog-extend' ),
				'edit'      => '',
		);
	}

	/**
	 * チェックボックス列を表示する
	 * @param array $item 行アイテム
	 * @return string チェックボックス列表示内容
	 */
	public function column_cb( $item ) {
		$result = '';
		if ( ! in_array( $item[ 'type' ], array( Constants::POST_TYPE_POST, Constants::POST_TYPE_PAGE ) ) ) {
			$result = sprintf(
					'<input type="checkbox" name="%1$s[]" value="%2$s" />',
					esc_attr( 'select' ),
					esc_attr( $item[ 'id' ] )
			);
		}
		return $result;
	}

	/**
	 * 非表示チェックボックス列を表示する
	 * @param array $item 行アイテム
	 * @return string 非表示チェックボックス列表示内容
	 */
	public function column_hidden_cb( $item ) {
		$hidden = '';
		if ( $item[ 'hidden' ] ) {
			$hidden = ' checked="checked"';
		}

		return sprintf(
				'<input type="checkbox" id="hidden_cb" data-post-id="%1$d" data-command="%2$s" value="1"%3$s />',
				(int)$item[ 'id' ],
				Constants::AJAX_COMMAND_CHANGELOG_LIST_UPDATE_HIDDEN_STATUS,
				$hidden
		);
	}

	/**
	 * 行タイトル接頭語列を表示する
	 * @param array $item 行アイテム
	 * @return string 行タイトル接頭語列表示内容
	 */
	public function column_prefix( $item ) {
		$select_items = array_merge(
				ML_CE_CHANGELOG_LIST_COMMON_AFFIX_SELECT_VALUE,
				array_combine( $this->settings[ Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS ], $this->settings[ Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS ] )
		);
		$items = '';
		$hide_select = '';
		$hide_input = '';
		$change_icon = '';
		foreach ( $select_items as $key => $value ) {
			$selected = '';
			if ( $key == $item[ 'prefix' ] ) {
				$selected = ' selected="selected"';
				$hide_input = ' style="display:none"';
				$change_icon = ' dashicons-edit';
			}
			$items .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $key, $value, $selected);
		}
		if ( empty( $hide_input ) ) {
			$hide_select = ' style="display:none"';
			$change_icon = ' dashicons-list-view';
		}
		return sprintf(
				'<select id="title_prefix"%5$s data-post-id="%1$d"DC data-command="%2$s">%3$s</select>' .
				'<input type="text" id="title_prefix"%6$s data-post-id="%1$d" data-command="%2$s" value="%4$s" />' .
				'<span class="dashicons%7$s" id="title_prefix_change"></span>',
				(int)$item[ 'id' ],
				Constants::AJAX_COMMAND_CHANGELOG_LIST_UPDATE_PREFIX,
				$items,
				$item[ 'prefix' ],
				$hide_select,
				$hide_input,
				$change_icon
		);
	}

	/**
	 * 行タイトル接尾語列を表示する
	 * @param array $item 行アイテム
	 * @return string 行タイトル接尾語列表示内容
	 */
	public function column_suffix( $item ) {
		$select_items = array_merge(
				ML_CE_CHANGELOG_LIST_COMMON_AFFIX_SELECT_VALUE,
				array_combine( $this->settings[ Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ], $this->settings[ Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ] )
		);
		$items = '';
		$hide_select = '';
		$hide_input = '';
		$change_icon = '';
		foreach ( $select_items as $key => $value ) {
			$selected = '';
			if ( $key == $item[ 'suffix' ] ) {
				$selected = ' selected="selected"';
				$hide_input = ' style="display:none"';
				$change_icon = ' dashicons-edit';
			}
			$items .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $key, $value, $selected);
		}
		if ( empty( $hide_input ) ) {
			$hide_select = ' style="display:none"';
			$change_icon = ' dashicons-list-view';
		}
		return sprintf(
				'<select id="title_prefix"%5$s data-post-id="%1$d" data-command="%2$s">%3$s</select>' .
				'<input type="text" id="title_prefix"%6$s data-post-id="%1$d" data-command="%2$s" value="%4$s" />' .
				'<span class="dashicons%7$s" id="title_prefix_change"></span>',
				(int)$item[ 'id' ],
				Constants::AJAX_COMMAND_CHANGELOG_LIST_UPDATE_SUFFIX,
				$items,
				$item[ 'suffix' ],
				$hide_select,
				$hide_input,
				$change_icon
		);
	}

	/**
	 * 編集ボタン列を表示する
	 * @param array $item 行アイテム
	 * @return string 編集ボタン列表示内容
	 */
	public function column_edit( $item ) {
		return sprintf(
				'<button type="button" id="edit" data-post-id="%1$d" data-command="%2$s">%3$s</button>',
				(int)$item[ 'id' ],
				Constants::AJAX_COMMAND_CHANGELOG_LIST_GET_CHANGELOG_DATA,
				__( '編集', 'changelog-extend' )
		);
	}

	/**
	 * 列を表示する
	 * @param array $item 行アイテム
	 * @param string $column_name 列名
	 * @return string 列表示内容
	 */
	public function column_default( $item, $column_name ) {
		return nl2br( esc_html( $item[ $column_name ] ) );
	}

	/**
	 * テーブルナビゲーションを表示する
	 */
	protected function display_tablenav( $which ) {
		// ヘッダ部には表示しない
		if ( 'top' === $which ) {
			return;
		}
		parent::display_tablenav( $which );
	}

	/**
	 * テーブルナビゲーションに追加コントロールを表示する
	 */
	protected function extra_tablenav( $which ) {
?>
<div class="alignleft actions bulkactions">
	<button type="button" id="addnew" value=""><?php echo esc_html( __( '新規追加', 'changelog-extend' ) ); ?></button>
	<button type="button" id="delete" data-command="<?php echo esc_attr( Constants::AJAX_COMMAND_CHANGELOG_LIST_DELETE_CHANGELOG_DATA ); ?>"><?php echo esc_html( __( '削除', 'changelog-extend' ) ); ?></button>
</div>
<?php
	}

	/**
	 * リストを表示する
	 * @param array $args パラメータ
	 */
	public function display_list( $args ) {
?>
<div class="setting_tab_content">
<?php
		$this->prepare_items();
		$this->display();
		$this->display_edit_form();
?>
</div>
<?php
	}

	/** 編集フォームを表示する */
	private function display_edit_form() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : Constants::SETTING_TAB_NAME_SETTING;
?>
<div id="edit_dialog" class="dialog">
<form id="edit_form" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="tab" value="<?php echo esc_attr( $active_tab ); ?>" />
<?php wp_nonce_field( Constants::CREDENTIAL_ACTION_CHANGELOG_LIST_EDIT_FORM, Constants::CREDENTIAL_NAME_CHANGELOG_LIST_EDIT_FORM ); ?>
<input type="hidden" name="id" value="" />
<input type="hidden" name="type" value="" />
<div id="dialog_body">
<dl>
<dt><?php echo esc_html( __( '更新日時', 'changelog-extend' ) ); ?></dt>
<dd><input type="datetime-local" name="date_time" id="date_time" step="1" value="" /></dd>
<dt><?php echo esc_html( __( 'タイトル 接頭語', 'changelog-extend' ) ); ?></dt>
<dd>
	<select name="select_prefix" id="prefix">
		<option value=""><?php echo esc_html( __( '(未選択)', 'changelog-extend' ) ); ?></option>
<?php	foreach ( $this->settings[ Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS ] as $item ) : ?>
		<option value="<?php echo esc_attr( $item ); ?>"><?php echo esc_html( $item ); ?></option>
<?php	endforeach; ?>
	</select>
	<input name="prefix" type="text" id="prefix" value="" />
	<span class="dashicons dashicons-edit" id="prefix_change"></span>
</dd>
<dt><?php echo esc_html( __( 'タイトル', 'changelog-extend' ) ); ?></dt>
<dd><input type="text" name="title" id="title" value="" /></dd>
<dt><?php echo esc_html( __( 'タイトル 接尾語', 'changelog-extend' ) ); ?></dt>
<dd>
	<select name="select_suffix" id="suffix">
		<option value=""><?php echo esc_html( __( '(未選択)', 'changelog-extend' ) ); ?></option>
<?php	foreach ( $this->settings[ Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ] as $item ) : ?>
		<option value="<?php echo esc_attr( $item ); ?>"><?php echo esc_html( $item ); ?></option>
<?php	endforeach; ?>
	</select>
	<input name="suffix" type="text" id="suffix" value="" />
	<span class="dashicons dashicons-edit" id="suffix_change"></span>
</dd>
<dt><?php echo esc_html( __( '非表示', 'changelog-extend' ) ); ?></dt>
<dd><input type="checkbox" name="hidden" id="hidden" value="1" /></dd>
<dt><?php echo esc_html( __( 'コメント', 'changelog-extend' ) ); ?></dt>
<dd><textarea name="comment" id="comment"></textarea></dd>
</dl>
</div>
<div>
	<p class="footer">
		<?php submit_button( __( '登録', 'changelog-extend' ), 'primary', 'submit', false ); ?>
		<button type="button" id="cancel" class="button"><?php echo esc_html( __( 'キャンセル', 'changelog-extend' ) ); ?></button>
	</p>
</div>
</form>
</div>
<?php
	}
}
?>
