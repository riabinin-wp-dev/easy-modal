document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById("add-modal");
    const container = document.querySelector('[data-container]');

    if (button) {
        button.addEventListener("click", function () {
            fetch(easyAjax.ajax_url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    action: "add_modal_action", 
                    _ajax_nonce: easyAjax.nonce, 
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.html) {
                        container.insertAdjacentHTML('beforeend', data.html);
                        //инициализация редактора
                        const editorWrapper  = wp.codeEditor.initialize(document.getElementById(data.id), {type: 'text/html'});
                        //подписываемся на события редактора
                        const editor = editorWrapper.codemirror;
                        editor.on('change', (instance)=>{
                            const textarea = document.getElementById(data.id);
                            textarea.value = instance.getValue();
                            // console.log(textarea.value);
                        })
                        //скпываем кнопку
                        button.style.display = 'none';
                    } else {
                        console.error("Ошибка:", data.message);
                    }
                })
                .catch((error) => console.error("Ошибка запроса:", error));
        });
    }
});