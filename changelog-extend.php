<?php
/**
 * Plugin Name: Changelog Extend
 * Plugin URI:
 * Description: 更新履歴の表示を行います。表示したい履歴の手動追加も可能です。
 * Version: 0.0.2
 * Author: 雅
 * Author URI: https://www.program-laboratory.com/
 * Text Domain: changelog-extend
 * Domain Path: /languages
 * License: GPLv2
 */
namespace miyabi_labo\Plugin\changelog_extend;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Please do not load this file directly.' );
}

// 定数を定義する
define( __NAMESPACE__ . '\ML_CE_PLUGIN_NAME', pathinfo( __FILE__, PATHINFO_FILENAME ) );
define( __NAMESPACE__ . '\ML_CE_PLUGIN_URL', plugins_url( '', __FILE__ ) );

// クラスローダーを読み込む
require_once( __DIR__ . '/class-loader.php' );

// ローダークラスをインスタンス化する
global $ml_ce_changelog_extend_class_loader;
if ( empty( $ml_ce_changelog_extend_class_loader ) ) {
	$ml_ce_changelog_extend_class_loader = new Loader();
}

// プラグインを有効化／無効化した時の処理を追加する
register_activation_hook( __FILE__, __NAMESPACE__ . '\Plugin_Register::activate' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\Plugin_Register::deactivate' );

// 初期化する
add_action( 'init', __NAMESPACE__ . '\Changelog_Extend::init' );
add_action( 'init', __NAMESPACE__ . '\Shortcode::init' );
add_action( 'init', __NAMESPACE__ . '\Admin::init' );
?>
