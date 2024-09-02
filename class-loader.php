<?php
namespace miyabi_labo\Plugin\changelog_extend;

/**
 * 拡張更新履歴プラグイン - クラスローダークラス
 * @author 雅
 */
class Loader {

	/** ファイルマップ */
	private $file_map = NULL;

	/** コンストラクタ */
	public function __construct() {
		// ファイルマップを初期化する
		$this->file_map = self::get_list_files( __DIR__, array( 'php' ) );
		// ファイルマップが初期化されていない場合はエラーにする
		if ( empty( $this->file_map ) ) {
			throw new \Exception( __( 'ファイルマップの初期化に失敗しました', 'changelog-extend' ) );
		}

		// クラスのオートローダーを定義する
		spl_autoload_register( array( &$this, 'classLoader' ) );
	}

	/** クラスに対応したファイルを自動でロードする */
	public function classLoader( $class ) {
		$parts = explode( '\\', $class );
		$class_name = array_pop( $parts );
		$namespace = implode( '\\', $parts );
		$file_name = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';

		// 違うネームスペースのファイルは何もしない
		if ( $namespace != __NAMESPACE__ ) {
			return false;
		}

		// ファイルマップにクラスファイルない場合は何もしない
		if ( ! array_key_exists( $file_name, $this->file_map ) ) {
			// translators: %1$s: class name
			throw new \Exception( sprintf( __( 'クラスファイルが見つかりません。(%1$s)', 'changelog-extend' ), $class ) );
		}

		// クラスファイルが存在しないか複数ある場合はエラーにする
		$class_files = $this->file_map[ $file_name ];
		if ( count( $class_files ) > 1 ) {
			// ファイルの最終更新日を取得する
			$class_file_times = array();
			foreach ( $class_files as $class_file ) {
				$class_file_times[] = array(
						'name'     => $class_file,
						'filetime' => filemtime( $class_file ),
				);
			}

			// 警告を出力する
			echo esc_html( __( 'Warning: 同一クラス名のファイルが複数存在します。', 'changelog-extend' ) . PHP_EOL . implode( PHP_EOL, array_map( function( $item ) {
				// translators: %1$s: file name, %2$s: file datetime
				return sprintf( __( ' ファイル名 : %1$s, 日時 : %2$s', 'changelog-extend' ), $item[ 'name' ], wp_date( 'Y/m/d H:i:s', $item[ 'filetime' ] ) ); 
			}, $class_file_times ) ) );

			// 最新のファイルを取得する
			$filetime = max( array_column( $class_file_times, 'filetime' ) );
			$index = array_search( $filetime, array_column( $class_file_times, 'filetime' ) );

			$class_file = $class_file_times[ $index ][ 'name' ];
		} else {
			$class_file = $class_files[ 0 ];
		}

		// クラスファイルを読み込む
		require_once( $class_file );
	}

	/**
	 * 指定した拡張子のファイルリストを取得する
	 * @param string $dir 対象ディレクトリ
	 * @param array $extentions 拡張子
	 * @return array ファイルリスト 
	 */
	private function get_list_files( $dir, $extentions = array() ) {
		$result = array();

		// ディレクトリを開く
		$handle = opendir($dir );
		if ( $handle === false ) {
			return $result;
		}

		// ディレクトリ内のファイルリストを取得する
		while ( ( $file = readdir( $handle ) ) !== false ) {
			// 特殊ディレクトリはスキップする
			if ( in_array( $file, array( '.', '..' ) ) ) {
				continue;
			}

			// ディレクトリの場合は再帰的に呼び出す
			$path = $dir . DIRECTORY_SEPARATOR . $file;
			if ( is_dir( $path ) ) {
				$result = array_merge( $result, self::get_list_files( $path, $extentions ) );
				continue;
			}

			// 拡張子が指定されている場合は含まれているか調べる
			if ( ! empty( $extentions ) ) {
				$extention = substr( $file, strrpos( $file, '.' ) + 1 );
				if ( ! in_array( $extention, $extentions ) ) {
					continue;
				}
			}

			$result[ $file ][] = $path;
		}

		// ディレクトリを開く
		closedir( $handle );

		return $result;
	}
}
?>
