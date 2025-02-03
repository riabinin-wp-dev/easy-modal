<?php class Modal
{
    /**
     * Summary of register Хуки
     * @return void
     */
    public function register()
    {
        // add_action('admin_footer', [$this, 'init_codemirror_editor']);
        // сохраняем модальное окно
        add_action('admin_post_easy_modal_save', [$this, 'handle_easy_modal_save']);
        // add action для ajax добавления блока модального окна
        add_action('wp_ajax_add_modal_action', [$this, 'add_modal_action']);
        // add_action( 'wp_ajax_nopriv_add_modal', [$this, 'add_modal_action']);
        add_action('admin_init', [$this, 'easy_modal_register_settings']);
        add_action('admin_init', [$this, 'easyDeleteModals']);
    }

    /**
     * Summary of modal_code_callback - генерация формы модального окна по клику "добавить"
     * @return void
     */
    public function modal_code_callback()
    {
        $id = uniqid('modal_code_editor_');
        ob_start(); ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-form>
            <div class="easy_form">
                <input type="hidden" name="action" value="easy_modal_save">
                <?php wp_nonce_field('easy_modal_save_nonce'); ?>
                <label for="modal_title"
                    class="modal_title"><?php echo esc_html__('Как назовем модальное окно?', 'easymodal'); ?></label>
                <input type="text" name="easy_modal[title]" id="modal_title" required>
                <label for="modal_content"
                    class="modal_content"><?php echo esc_html__('Что будем в него выводить?', 'easymodal'); ?></label>
                <textarea id="<?php echo esc_attr($id); ?>" name="easy_modal[code]" rows="10" class="large-text code" data-editor
                    placeholder="<?php esc_html_e('Введите HTML, CSS или JavaScript код для модального окна.', 'easymodal'); ?>"></textarea>
                <label for="modal_class"
                    class="modal_class"><?php echo esc_html__('Каким классом будем открывать?', 'easymodal'); ?><br>
                    <small><?php echo esc_html__('PS: Точку ставить не нужно.', 'easymodal'); ?></small></label>
                <input type="text" name="easy_modal[class]" id="modal_class" required>
                <button type="submit"
                    class="button button-primary easy_modal_save --bottom"><?php echo esc_html__('Сохранить модальное окно', 'easymodal'); ?></button>
            </div>
        </form>
        <?php
        $response['html'] = ob_get_clean();
        $response['id'] = $id;
        wp_send_json($response);
    }

    /**
     * Summary of add_modal_action обработчик ajax добавления нового модального окна
     * @return void
     */
    public function add_modal_action()
    {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'easy_modal_nonce')) {
            wp_send_json_error(['message' => 'Ошибка валидации nonce!'], 400);
            wp_die();
        }
        // получаем разметку модального окна
        $this->modal_code_callback();
    }

    /**
     * Summary of init_codemirror_editor инициализация эдитора
     * @param mixed $modal_ids
     * @return void
     */
    public function init_codemirror_editor($modal_ids)
    {
        if (empty($modal_ids) || !is_array($modal_ids)) {
            return;
        }
        // Преобразуем массив PHP в JSON
        $modal_ids_json = json_encode($modal_ids);
        ?>
        <script>
            const modalIds = <?php echo $modal_ids_json; ?>;
            document.addEventListener('DOMContentLoaded', function () {
                modalIds.forEach(id => {
                    const container = document.getElementById(id);
                    if (!container && typeof wp === 'undefined') {
                        console.log('контейнер не найден');
                        console.warn("CodeMirror не инициализирован: элемент не найден или wp.codeEditor недоступен.");
                        return;
                    }
                    const editor = wp.codeEditor.initialize(container.querySelector('[data-editor]'), {
                            mode: 'htmlmixed', 
                            lineNumbers: true,
                            indentUnit: 4,
                            tabSize: 4,
                            theme: 'monokai'
                    });
                    //добавляем отслеживание секции для перезагрузки редактора при открытии(для устранения багов)
                    const accordeon = container.closest('.easy_accordeon ');
                    if (!accordeon) {
                        return;
                    }
                    const observer = new MutationObserver((mutationsList) => {
                        for (let mutation of mutationsList) {
                            // Проверяем добавились ли новые классы
                            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                // Проверяем наличие вашего класса
                                if (accordeon.classList.contains('active')) {
                                    editor.codemirror.refresh();
                                    observer.disconnect(); // Останавливаем наблюдение
                                    break; // Выходим из цикла, если функция вызвана
                                }
                            }
                        }
                    })
                    // Конфигурация наблюдения: отслеживаем изменения атрибутов
                    const config = { attributes: true };
                    // Начинаем наблюдение за элементом
                    observer.observe(accordeon, config);
                });
            });
        </script>
        <?php
    }

    /**
     * Summary of handle_easy_modal_save  сохранение модального окна
     * @return never
     */
    public function handle_easy_modal_save()
    {
        // Проверка nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'easy_modal_save_nonce')) {
            wp_die('Ошибка безопасности. Данные не сохранены.');
        }

        $title = isset($_POST['easy_modal']['title']) ? wp_kses_post($_POST['easy_modal']['title']) : null;
        $content = isset($_POST['easy_modal']['code']) ? wp_kses_post($_POST['easy_modal']['code']) : null;
        $class = isset($_POST['easy_modal']['class']) ? wp_kses_post($_POST['easy_modal']['class']) : null;
        // error_log(print_r($_POST['easy_modal'],true));

        if (is_null($title) || is_null($content) || is_null($class)) {
            wp_die('Переданы пустые значения.');
        }
        // Получаем список текущих модальных окон
        $modals = get_option('easy_modal_settings', []);
        // Создаём новое модальное окно
        $new_modal = [
            'id' => uniqid('modal_'),  // Уникальный ID
            'title' => $title,
            'content' => $content,
            'class' => $class,
        ];
        // Добавляем новое окно в массив
        $modals[] = $new_modal;
        // Сохраняем обновлённый массив в опции
        update_option('easy_modal_settings', $modals);
        // Перенаправляем обратно на страницу настроек с сообщением об успехе
        wp_redirect(admin_url('admin.php?page=easy_modal&status=success'));
        exit;
    }

    /**
     * Summary of easySuccess вывод уведомления об успешном сохранении
     * @return void
     */
    public function easySuccess()
    {
        if (!isset($_GET['status']) || !isset($_GET['page']) || $_GET['status'] != 'success' || $_GET['page'] != 'easy_modal') {
            return;
        }
        ?>
        <?php
        echo '<span class="wrap_success notice notice-success">' . esc_html__('Данные успешно сохранены', 'easymodal') . '</span>';
    }
    /**
     * Summary of easyDeleted вывод уведомления об удалении
     * @return void
     */
    public function easyDeleted()
    {
        if (!isset($_GET['status']) || !isset($_GET['page']) || $_GET['status'] != 'deleted' || $_GET['page'] != 'easy_modal') {
            return;
        }
        ?>
        <?php echo '<span class="wrap_success notice notice-error">' . esc_html__('Модальное окно успешно удалено', 'easymodal') . '</span>';
    }

    /**
     * Summary of loadModals отрисовка имеющихся модальных окон
     * @return void
     */
    public function loadModals()
    {
        settings_fields('easy_modal_settings_group');
        $modals = get_option('easy_modal_settings', []);

        if (empty($modals)) {
            return;
        }
        // массив id модальных окон
        $modal_ids = []; ?>
        <ul>
            <?php foreach ($modals as $key => $modal): ?>
                <!-- <?php //error_log(print_r($modal, true)); ?> -->
                <?php array_push($modal_ids, $modal['id']); ?>
                <!-- //пока оставить, возможно потом переделаю на уход на отдельную страницу -->
                <li class="easy_accordeon <?php echo isset($_GET['edit_modal']) && absint($_GET['edit_modal']) == $key ? 'active' : ''; ?>">
                    <div class="accordeon-header">
                        <strong><?php echo esc_html($modal['title']); ?></strong>
                        <button type="button" class="toggle-arrow" aria-label="Toggle"></button>
                    </div>
                    <div class="accordeon-content" id="<?php echo esc_attr($modal['id']); ?>">
                        <textarea name="easy_modal_settings[<?php echo esc_attr($key); ?>][content]" rows="10" class="large-text code"
                            data-editor><?php echo esc_html($modal['content']); ?></textarea>
                        <input type="hidden" name="easy_modal_settings[<?php echo esc_attr($key); ?>][id]" value="<?php echo esc_attr($modal['id']); ?>">
                        <div class="flex-row">
                            <a href="?delete_modal=<?php echo esc_attr($key); ?>"><?php esc_html_e('Удалить', 'easymodal'); ?></a>
                            <div class="easy_modal_class_show">
                                <small><?php echo esc_html__('Класс для вызова:  ', 'easymodal') ?></small>
                                <span class="easy_accordeon_class"><?php echo esc_html($modal['class']); ?></span>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        $this->init_codemirror_editor($modal_ids);
    }

    /**
     * Summary of easy_modal_register_settings добавляем регистрацию группы
     * @return void
     */
    public function easy_modal_register_settings()
    {
        register_setting(
            'easy_modal_settings_group',  // Группа настроек
            'easy_modal_settings',  // Название опции
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'easy_modal_sanitize_modals'],  // Ваша функция очистки
                'default' => [],  // По умолчанию пустой массив
            ]
        );
    }

    /**
     * Summary of easy_modal_sanitize_modals общее сохранение всех модальных окон
     * @param mixed $input
     */
    public function easy_modal_sanitize_modals($input)
    {
        // загрузим существующий массив
        $existing_modals = get_option('easy_modal_settings', []);
        // если вообще пустой, то выходим
        if (empty($input)) {
            return $existing_modals;
        }
        // если есть изменения + проверяем чтоб название и контент тоже существовали
        $output = [];
        foreach ($input as $key => $modal) {
            //error_log(print_r($input, true));
            $output[$key] = [
                'id' => sanitize_text_field($modal['id']),
                'title' => isset($modal['title']) ? wp_kses_post($modal['title']) : $existing_modals[$key]['title'],
                'content' => isset($modal['content']) ? wp_kses_post($modal['content']) : $existing_modals[$key]['content'],
                'class' => isset($modal['class']) ? wp_kses_post($modal['class']) : $existing_modals[$key]['class'],
            ];
        }
        return $output;
    }

    /**
     * Summary of easyDeleteModals Удаление модального окна
     * @return void
     */
    public function easyDeleteModals()
    {
        if (!isset($_GET['delete_modal'])) {
            return;
        }

        $modals = get_option('easy_modal_settings', []);
        $key = sanitize_text_field($_GET['delete_modal']);

        if (isset($modals[$key])) {
            unset($modals[$key]);

            // Если массив пустой, удаляем опцию, иначе обновляем
            if (empty($modals)) {
                delete_option('easy_modal_settings');  // Удаляем опцию, если больше нет окон
            } else {
                update_option('easy_modal_settings', $modals);  // Сохраняем обновлённый массив
            }
            wp_redirect(admin_url('admin.php?page=easy_modal&status=deleted'));  // Перенаправление после удаления
            exit;
        }
    }
}
