<?php global $easyModal; 
$e = 'easy_modal'; ?>

<form method="post" action="options.php">
    <div class="easy_form_update">
        <?php submit_button(__('Сохранить модальные окна', 'easymodal')); ?>
    </div>
    
    <?php $easyModal->loadModals(); ?>
</form>
    <div class="<?= $e; ?>_wrapper" data-container></div>            
    <button id="add-modal" class="button button-primary <?= $e; ?>_add" data-add><?php esc_html_e('Добавить модальное окно', 'easymodal'); ?></button>