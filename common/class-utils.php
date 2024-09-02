<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - ユーティリティクラス
 * @author 雅
 */
class Utils {

	/**
	 * 設定を取得する
	 * @return array 設定値
	 */
	public static function get_settings() {
		$settings = get_option( Constants::SETTING_NAME );
		foreach ( array( Constants::SETTING_NAME_ROW_TITLE_PREFIX_ITEMS, Constants::SETTING_NAME_ROW_TITLE_SUFFIX_ITEMS ) as $name ) {
			if ( array_key_exists( $name, $settings ) ) {
				if ( ! is_array( $settings[ $name ] ) ) {
					$settings[ $name ] = preg_split( "/\r\n|\r|\n/", $settings[ $name ] );
				}
				$settings[ $name ] = array_values( array_filter( $settings[ $name ] ) );
			}
		}
		foreach ( Constants::SETTING_HTML_VALUES as $name ) {
			$settings[ $name ] = wp_specialchars_decode( $settings[ $name ], ENT_QUOTES );
		}
		return wp_parse_args( $settings, ML_CE_SETTING_DEFAULT_VALUES );
	}

	/**
	 * 更新履歴メタ情報を取得する
	 * @return array メタ情報
	 */
	public static function get_changelog_meta_value( $post_id ) {
		return wp_parse_args(
				get_post_meta( $post_id, Constants::POST_META_KEY, TRUE ),
				array(
						Constants::POST_META_VALUE_KEY_HIDDEN => 0,
						Constants::POST_META_VALUE_KEY_PREFIX => '',
						Constants::POST_META_VALUE_KEY_SUFFIX => '',
						Constants::POST_META_VALUE_KEY_COMMENT => '',
				)
		);
	}

	/**
	 * プラグインに関連しているページか調べる
	 * @return boolean 値
	 */
	public static function is_related_plugin() {
		if ( empty( $_REQUEST[ 'page' ] ) ) {
			$params = array();
			parse_str( wp_parse_url( wp_get_referer(), PHP_URL_QUERY ), $params );
			if ( ! array_key_exists( 'page', $params ) ) {
				return FALSE;
			}
			$page = $params[ 'page' ];
		} else {
			$page = sanitize_text_field( wp_unslash( $_REQUEST[ 'page' ] ) );
		}
		return in_array( $page, Constants::SLUG_RELATED_PAGES );
	}
}
?>
