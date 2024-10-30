<?php
/*
Plugin Name: Iran Alves - Upload and Update List of Files
Description: Generate files list for download or other purposes
Version: 0.1
Author: Iran Alves
Author URI: makingpie.com.br
License: GPL2
Copyright (C) 2020 Iran
*/

defined( 'ABSPATH' ) or die('No script kiddies please!');

// Constantes
define('PLUGIN_IAUULF_VERSION', '0.1');
define('PLUGIN_IAUULF_NAME', 'Iran Alves - Upload and Update List of Files');
define('PLUGIN_IAULLF_URL', plugin_dir_url(__FILE__));

/**
 * Verifica se classe foi iniciada
 * @since 0.1
 */
if ( !isset($upload_and_update_list_files) || is_a($upload_update_list_files, 'IAUULF') || !class_exists('IAUULF')) {
    require_once('includes/class-uulf.php');
    $upload_update_list_files = new IAUULF();
}

/**
 * Inicializar plugin
 * @since 0.1
 */
$upload_update_list_files->init();
