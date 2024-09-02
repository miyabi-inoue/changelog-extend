<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - メインクラス
 * @author 雅
 */
class Changelog_Extend {

	/**
	 * 初期化する
	 * @return Changelog_Extend インスタンス
	 */
	public static function init() {
		return new self();
	}

	/** コンストラクタ */
	private function __construct() {
		// 投稿タイプを登録する
		$this->register_post_type();
	}

	/** 投稿タイプを登録する */
	private function register_post_type() {
		register_post_type( Constants::POST_TYPE_CHANGELOG, array(
				'lables'  => array(
						'name' => __( '更新履歴', 'changelog-extend' ),
				),
		) );
	}
}
?>
