<?php global $easyModal;
$easyModal = new Modal; ?>
<div class="wrap custom_wrap">
    <!-- выводим уведомление -->
    <?php $easyModal->easySuccess(); ?>
    <?php $easyModal->easyDeleted(); ?>

    <h1 class="easymodal_title"><?php esc_html_e('Плагин модальных окон - Easy Modal', 'easymodal'); ?></h1>
    <div class="easy_modal_wrapper">
        <div class="tabs">
            <div class="tab active" data-tab="code">Модальные окна</div>
            <div class="tab" data-tab="settings">Настройки</div>
        </div>
    
        <div id="code" class="tab-content active">
            <!-- Code modals -->
            <?php require_once plugin_dir_path(__FILE__) . 'modal-tab.php'; ?>
        </div>
        <div id="settings" class="tab-content">
            <!-- Settings content goes here -->
            <?php require_once plugin_dir_path(__FILE__) . 'settings-tab.php'; ?>
        </div>
    </div>
</div>
<?php settings_errors(); ?>