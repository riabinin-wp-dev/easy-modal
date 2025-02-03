<?php class EasySettings
{
    /**
     * Summary of register Хуки
     * @return void
     */
    public function register()
    {
        add_action('admin_init', [$this, 'easy_settings_register']);
        // add_action('wp_head', [__CLASS__, 'render_easy_css']);

    }
    /**
     * Summary of easy_settings_register Регистрация хранения настроек
     * @return void
     */
    public function easy_settings_register()
    {
        register_setting(
            'easy_modal_settings_group',
            'easy_settings_css',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'easy_settings_css'],
                'default' => [],
            ]
        );
    }

    /**
     * Summary of easy_settings_css - Сохранение стилей
     * @param mixed $input
     */
    public function easy_settings_css($input)
    {
        return empty($input) ? '' : $input;
    }
    
    /**
     * Summary of render_easy_css Устарело. Не используется. Выводится инлайново в другом классе
     * @return void
     */
    static function render_easy_css(){
        $css = get_option('easy_settings_css', []);
        
        if(empty($css)){
            return;
        }

        echo "<style id='easy-modal-css'>" . esc_html($css) . "</style>";
    }
}