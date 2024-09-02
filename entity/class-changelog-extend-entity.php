<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - 拡張更新履歴エンティティクラス
 * @author 雅
 */
class Changelog_Extend_Entity {

	/** 投稿ID */
	private $id;
	/** 投稿種別 */
	private $type;
	/** 投稿日時 */
	private $date_time;
	/** タイトル */
	private $title;
	/** 非表示フラグ */
	private $hidden;
	/** タイトル接頭語 */
	private $prefix;
	/** タイトル接尾語 */
	private $suffix;
	/** コメント */
	private $comment;

	/**
	 * コンストラクタ
	 * @param WP_Post $post_id
	 */
	public function __construct( $post ) {
		$meta_value = Utils::get_changelog_meta_value( $post->ID );

		$this->meta_value = Utils::get_changelog_meta_value( $post->ID );
		$this->id = $post->ID;
		$this->type = $post->post_type;
		$this->date_time = new \DateTime( $post->post_date );
		$this->title = $post->post_title;
		$this->hidden = $meta_value[ Constants::POST_META_VALUE_KEY_HIDDEN ];
		$this->prefix = $meta_value[ Constants::POST_META_VALUE_KEY_PREFIX ];
		$this->suffix = $meta_value[ Constants::POST_META_VALUE_KEY_SUFFIX ];
		$this->comment = $meta_value[ Constants::POST_META_VALUE_KEY_COMMENT ];
	}

	/**
	 * 投稿IDを取得する
	 * @return int 投稿ID
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * 投稿IDを設定する
	 * @param int 投稿ID
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * 投稿種別を取得する
	 * @return string 投稿種別
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * 投稿種別を設定する
	 * @param string 投稿種別
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * 投稿日時を取得する
	 * @return \DateTime 投稿日時
	 */
	public function get_date_time() {
		return $this->date_time;
	}

	/**
	 * 投稿日時を設定する
	 * @param mixed 投稿日時
	 */
	public function set_date_time( $date_time ) {
		if ( $date_time instanceof \DateTime ) {
			$this->date_time = $date_time;
		} else {
			$this->date_time = new \DateTime( $date_time );
		}
	}

	/**
	 * タイトルを取得する
	 * @return string タイトル
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * タイトルを設定する
	 * @param string タイトル
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * 非表示フラグを取得する
	 * @return int 非表示フラグ
	 */
	public function get_hidden() {
		return $this->hidden;
	}

	/**
	 * 非表示フラグを設定する
	 * @param int 非表示フラグ
	 */
	public function set_hidden( $hidden ) {
		$this->hidden = $hidden;
	}

	/**
	 * 非表示にするか調べる
	 * @param boolean 値
	 */
	public function is_hidden() {
		return $this->hidden != 0;
	}

	/**
	 * タイトル接頭語を取得する
	 * @return string タイトル接頭語 
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * タイトル接頭語を設定する
	 * @param string タイトル接頭語
	 */
	public function set_prefix($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * タイトル接尾語を取得する
	 * @return string タイトル接尾語
	 */
	public function get_suffix() {
		return $this->suffix;
	}

	/**
	 * タイトル接尾語を設定する
	 * @param string タイトル接尾語
	 */
	public function set_suffix($suffix) {
		$this->suffix = $suffix;
	}

	/**
	 * コメントを取得する
	 * @return string コメント
	 */
	public function get_comment() {
		return $this->comment;
	}

	/**
	 * コメントを設定する
	 * @param string コメント
	 */
	public function set_comment($comment) {
		$this->comment = $comment;
	}
}
?>
