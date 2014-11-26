<?php

/**
 * Plugin Name: Author Meta Box
 * Plugin URI:  https://github.com/ShogunNR/snr-author-meta-box
 * Description: Adds an author meta box. Allows you to change the author's name, biographical info, website and avatar for each posts.
 * Version:     1.0.0
 * Author:      ShogunNR
 * Author URI:  https://github.com/ShogunNR/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { 
        exit;
}

/**
 * Defines the filename of the plugin.
 */
define( 'SNRAMB_PLUGIN_FILE', __FILE__ );

/**
 * Includes the core plugin class for executing the plugin.
 */
require_once( plugin_dir_path( __FILE__ ) . 'inc/class-snr-author-meta-box.php' );

/**
 * Begins execution of the plugin.
 */
$snr_author_meta_box = new SNR_Author_Meta_Box();
$snr_author_meta_box->run();