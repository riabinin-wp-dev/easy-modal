<div class="easy_table">
    <form method="post" action="options.php">
        
       <div class="easy_flex_column">
           <label for="easy_modal_css" class="easy_title_css">Дополнительные стили css</label>
           <textarea id="easy_modal_css" name="easy_settings_css" rows="10" cols="50"><?php echo esc_textarea(get_option('easy_settings_css', '')); ?></textarea>
       </div>
        <div class="easy_settings_save">
            <?php settings_fields('easy_modal_settings_group');
            submit_button(__('Сохранить настройки', 'easymodal')); ?>
         </div>
    </form>
</div>
