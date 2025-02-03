document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            // Add active class to the clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });
});

//аккордеоны
document.addEventListener("DOMContentLoaded", function () {
    const accordions = document.querySelectorAll(".easy_accordeon");

    accordions.forEach((accordion) => {
        const header = accordion.querySelector(".accordeon-header");
        header.addEventListener("click", () => {
            const isActive = accordion.classList.contains("active");
            // Скрыть все остальные аккордеоны
            accordions.forEach((acc) => acc.classList.remove("active"));

            // Переключить текущий аккордеон
            if (!isActive) {
                accordion.classList.add("active");
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const settingsEditor = wp.codeEditor.initialize(document.querySelector('#easy_modal_css'), {
            mode: 'css',
            lineNumbers: true,
            indentUnit: 4,
            tabSize: 4,
            theme: 'monokai'
    });

    const editor = settingsEditor.codemirror;
    editor.on('change',(instance)=>{
        const textarea = document.querySelector('#easy_modal_css');
        textarea.value = instance.getValue();
        // console.log(textarea.value);
    })

    const settingsTab = document.querySelector('[data-tab="settings"]');
    if (!settingsTab) {
        return;
    }
    settingsTab.addEventListener('click', () => {
        setTimeout(() => {
            settingsEditor.codemirror.refresh();
        }, 100);
    })
})
