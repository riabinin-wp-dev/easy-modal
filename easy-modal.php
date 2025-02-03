<?php
/*
Plugin Name: Easy-modal
Plugin URI: https://t.me/ryabininvitaliy
Description: Простой плагин для модальных окон.
Version: 1.0
Author: Ryabinin Vitaliy
Author URI: https://t.me/ryabininvitaliy
License: GPLv2 or later
Text Domain: easymodal
*/

if (!defined('ABSPATH')) {
    die;
}
//константа для пути
if (!defined('EASY_MODAL_PLUGIN_DIR')) {
    define('EASY_MODAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

require_once EASY_MODAL_PLUGIN_DIR . '/inc/classes/easymodal.php';
require_once EASY_MODAL_PLUGIN_DIR . '/inc/classes/modal.php';
require_once EASY_MODAL_PLUGIN_DIR . '/inc/classes/settingsmodal.php';
// require_once EASY_MODAL_PLUGIN_DIR . "inc/vendor/meta-box-class/class_modal_meta-box.php";

if (class_exists('EasyModal')) {
    $easyModal = new EasyModal;
    $easyModal->register();
}

register_activation_hook(__FILE__, array($easyModal, 'activation'));
register_deactivation_hook(__FILE__, array($easyModal, 'deactivation'));