<?php
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        die;
    }

    // Удаляем настройки плагина
    delete_option('easy_modal_settings');
    delete_option('easy_settings_css');
?>