<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - ショートコードクラス
 * @author 雅
 */
class Shortcode {

	/** 設定 */
	private $settings;

	/**
	 * 初期化する
	 * @return Shortcode インスタンス
	 */
	public static function init() {
		return new self();
	}

	/** コンストラクタ */
	private function __construct() {
		// ショートコートを登録する
		add_shortcode( Constants::SHORTCODE_CHANGELOGEX, array( &$this, 'changelogex' ) );

		// スタイルシートを読み込む
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
	}

	/**
	 * 更新履歴を表示する
	 * @param array 属性
	 * @return string 更新履歴html
	 */
	public function changelogex( $attributes ) {
		// 引数が属性名で指定されていない場合は順番に格納する
		$defaults = array(
				'hide-title' => 0,
				'rows'       => 0,
		);
		$attributes = wp_parse_args( $attributes, $defaults );

		// 設定を取得する
		$this->settings = Utils::get_settings();

		if ( $attributes[ 'rows' ] == 0 ) {
			$attributes[ 'rows' ] = $this->settings[ Constants::SETTING_NAME_ROWS_TO_DISPLAY ];
		}

		// 日付のフォーマットを取得する
		$date_format = $this->getDateFormat( $this->settings[ Constants::SETTING_NAME_ROW_TEMPLATE ] );

		// 日付置換用検索文字列を定義する 
		$search_date_time = array(
				Constants::ROW_TEMPLATE_DATE,
				str_replace( '([^%]+)', $date_format, Constants::ROW_TEMPLATE_DATE_WITH_FORMAT ),
		);

		// 新着表示の表示方法を決定する
		if ( ! empty( $_SERVER[ 'DOCUMENT_ROOT' ] ) && ! empty( $this->settings[ Constants::SETTING_NAME_NEW_ICON ] ) ) {
			$filename = sanitize_text_field( wp_unslash( $_SERVER[ 'DOCUMENT_ROOT' ] ) ) . $this->settings[ Constants::SETTING_NAME_NEW_ICON ];
			if ( file_exists( $filename ) ) {
				$image_size  = getimagesize( $filename );
				if ( ! empty( $image_size ) ) {
					$new = sprintf( '<img class="ce_row_new" %1$s src="%2$s" />', $image_size[3], esc_url( $this->settings[ Constants::SETTING_NAME_NEW_ICON ] ) );
				}
			}
		}
		if ( empty( $new ) ) {
			$new = '<span class="ce_row_new">New!</span>';
		}

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
			return '<div>データの取得に失敗しました。</div>';
		}

		// 更新履歴のhtmlを組み立てる
		$current_date_time = new \DateTime();
		$index = 0;
		$result = '';
		if ( $attributes[ 'hide-title' ] != 1 ) {
			$result .= str_replace( Constants::TITLE_TEMPLATE_TITLE, $this->settings[ Constants::SETTING_NAME_TITLE ], $this->settings[ Constants::SETTING_NAME_TITLE_TEMPLATE ] ) . "\n";
		}
		$result .= $this->settings[ Constants::SETTING_NAME_LIST_START ] . "\n";
		foreach ( $posts as $post ) {
			// 非表示にする設定の場合はスキップする
			$changelog_extend = new Changelog_Extend_Entity( $post );
			if ( $changelog_extend->is_hidden() ) {
				continue;
			}

			// 指定された行数を超えた場合はループを抜ける
			if ( $attributes[ 'rows' ] > 0 && $attributes[ 'rows' ] < ++$index ) {
				break;
			}

			// 表示する行の情報を組み立てる
			$line = $this->settings[ Constants::SETTING_NAME_ROW_TEMPLATE ];
			$datetime = $changelog_extend->get_date_time();
			$line = str_replace( $search_date_time, $datetime->format( $date_format ), $line );
			if ( $current_date_time->diff( $datetime )->days < $this->settings[ Constants::SETTING_NAME_DAYS_TO_DISPLAY_AS_NEW ] ) {
				$line = str_replace( Constants::ROW_TEMPLATE_NEW, $new, $line );
			} else {
				$line = str_replace( Constants::ROW_TEMPLATE_NEW, '', $line );
			}
			$title = '';
			if ( empty( $changelog_extend->get_prefix() ) ) {
				$title .= $this->settings[ Constants::SETTING_NAME_DEFAULT_ROW_TITLE_PREFIX ];
			} elseif ( $changelog_extend->get_prefix() != Constants::SETTING_VALUE_HIDDEN_AFFIX ) {
				$title .= $changelog_extend->get_prefix();
			}
			$title .= $changelog_extend->get_title();
			if ( empty( $changelog_extend->get_suffix() ) ) {
				$title .= $this->settings[ Constants::SETTING_NAME_DEFAULT_ROW_TITLE_SUFFIX ];
			} elseif ( $changelog_extend->get_suffix() != Constants::SETTING_VALUE_HIDDEN_AFFIX ) {
				$title .= $changelog_extend->get_suffix();
			}
			$line = str_replace( Constants::ROW_TEMPLATE_TITLE, $title, $line );
			$result .= $line . "\n";
		}
		$result .= $this->settings[ Constants::SETTING_NAME_LIST_END ] . "\n";

		return $result;
	}

	/** スタイルシートを読み込む */
	public function enqueue_scripts() {
		// 投稿にショートコードが含まれていない場合は何もしない
		$post= get_post();
		if ( empty( $post ) || ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, Constants::SHORTCODE_CHANGELOGEX ) ) {
			return;
		}
		wp_enqueue_style( Constants::CSS_ID_CHANGELOG_EXTEND, sprintf( '%1$s/css/%2$s.css', Constants::PLUGIN_URL, Constants::PLUGIN_NAME ) );
	}

	/**
	 * 行テンプレートに日付がある場合はフォーマットを取得する
	 * @param string 行テンプレート
	 * @return string 日付フォーマット
	 */
	private function getDateFormat( $template ) {
		$matches = array();
		if ( preg_match( '/' . Constants::ROW_TEMPLATE_DATE_WITH_FORMAT . '/', $template, $matches ) === 1 ) {
			return $matches[1];
		}
		return $this->settings[ Constants::SETTING_NAME_DEFAULT_DATE_FORMAT ];;
	}
}
?>
