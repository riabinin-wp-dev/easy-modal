<?php
class EasyModal
{
    /**
     * Summary of register регистрация хуков
     * @return void
     */
    public function register()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front']);
        add_action('admin_init', [$this, 'settings_init']);
        add_action('plugins_loaded', [$this, 'getModals']);
        add_action('plugins_loaded', [$this, 'getSettings']);
        add_action('wp_footer', [__CLASS__, 'renderModal'], 5);
    }

    /**
     * Summary of getSettings Регистрация настроек модального окна
     * @return void
     */
    public function getSettings()
    {
        $settings = new EasySettings;
        $settings->register();
    }

    /**
     * Summary of getModals запуск настроек класса молальных окон
     * @return void
     */
    public function getModals()
    {
        $modal = new Modal;
        $modal->register();
    }

    public function settings_init()
    {
        register_setting('easy_modal', 'easy_modal_settings');
        // add_settings_section('easy_modal', esc_html__('Settings', 'easymodal'), [$this, 'settings_section_html'], 'easy_modal_settings');
    }

    /**
     * Summary of add_admin_menu регистрация пункта меню
     * @return void
     */
    public function add_admin_menu()
    {
        add_menu_page(esc_html__('Easy modal settings page', 'easymodal'), esc_html__('EasyModal', 'easymodal'), 'manage_options', 'easy_modal', [$this, 'easy_modal_page'], 'dashicons-feedback', 80);
    }

    /**
     * Summary of easy_modal_page  подключение разметки страницы плагина
     * @return void
     */
    public function easy_modal_page()
    {
        require_once EASY_MODAL_PLUGIN_DIR . '/admin/admin.php';
    }

    /**
     * Summary of enqueue_admin подключение скриптов и стилей на странице плагина
     * @return void
     */
    public function enqueue_admin()
    {
        if (!$this->checkPage()) {
            return;
        }
        // Подключаем встроенные ресурсы WordPress для CodeMirror
        wp_enqueue_code_editor(['type' => 'text/html']);
        wp_enqueue_code_editor(['type' => 'text/css']);
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
        // подключение своих стилей и скриптов
        wp_enqueue_style('easyModalStyle', plugins_url('../../assets/admin/styles.css', __FILE__));
        wp_enqueue_script('easyModalScript', plugins_url('../../assets/admin/scripts.js', __FILE__));
        wp_enqueue_script('easyModalAjax', plugins_url('../../assets/admin/ajax.js', __FILE__));
        wp_localize_script('easyModalAjax', 'easyAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('easy_modal_nonce'),
        ]);
    }

    /**
     * Summary of enqueue_front подключение скриптов и стилей на страницах сайта
     * @return void
     */
    public function enqueue_front()
    {
        // подключение своих стилей и скриптов
        wp_enqueue_style('easyModalStyle', plugins_url('../../assets/front/styles.css', __FILE__));
        wp_enqueue_script('easyModalScript', plugins_url('../../assets/front/scripts.js', __FILE__));
        wp_enqueue_script('easyModalClass', plugins_url('../../assets/js/modal.js', __FILE__));
        //передача объекта данных модальных окон
        $modals = $this->checkCode();
        //добавляем стили с настроек
        $settings_css = get_option('easy_settings_css', []);
        if (!empty($modals)) {
            wp_localize_script('easyModalClass', 'modalData', $modals);
        }
        if (!empty($settings_css)) {
            wp_add_inline_style('easyModalStyle', $settings_css);
        }

    }
    /**
     * Summary of process_modal_content Раскрываем шорткоды - устарела - не используется
     * @param mixed $content
     * @return array|string|null
     */
    private static function process_modal_content($content)
    {
        return preg_replace_callback(
            '/\[(.*?)\]/', // Находим текст в квадратных скобках (шорткоды)
            function ($matches) {
                return do_shortcode($matches[0]);
            },
            $content
        );
    }
    /**
     * Summary of checkCode Инициализируем разворачивание шорткодов
     * Возвращает массив модальных окон с развернутыми шорткодами
     */
    static function checkCode()
    {
        $modals = get_option('easy_modal_settings', []);

        if (empty($modals)) {
            return;
        }

        foreach ($modals as &$modal) {
            if (empty($modal['content'])) {
                continue;
            }
            $modal['content'] = do_shortcode(wp_kses_post($modal['content']));
        }
        return $modals;
    }

    /**
     * Summary of renderModal Выведем модальные окна
     * @return void
     */
    static function renderModal()
    {
        $modals = get_option('easy_modal_settings', []);

        if (empty($modals)) {
            return;
        }
        ob_start();
        foreach ($modals as $key => $modal) { ?>
            <div class="modal-overlay" id="modal-<?php echo esc_attr($modal['id']); ?>">
                <div class="modal-window" onclick="event.stopPropagation()">
                    <button class="modal-close">
                        <span class="line line1"></span>
                        <span class="line line2"></span>
                    </button>
                    <?php echo do_shortcode(wp_kses_post($modal['content'])); ?>
                </div>
            </div>
        <?php }
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Summary of checkPage проверка страницы плагина
     * @return bool
     */
    static function checkPage()
    {
        $current_screen = get_current_screen();
        return $current_screen && $current_screen->id === 'toplevel_page_easy_modal';
    }


    /**
     * Summary of activation обновляем постоянные ссылки
     * @return void
     */
    static function activation()
    {
        flush_rewrite_rules();
    }

    /**
     * Summary of activation обновляем постоянные ссылки
     * @return void
     */
    static function deactivation()
    {
        flush_rewrite_rules();
    }
}
