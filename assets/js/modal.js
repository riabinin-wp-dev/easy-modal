class Modal {
    constructor(option) {
        this.id = option.id;
        this.content = option.content;
        this.class = option.class;
        this.title = option.title;
    }

    /**
     * функция поиска классов для открытия модальных окон
     * @returns 
     */
    findClasses() {
        let classes = document.querySelectorAll(`.${this.class}`);        
        if (classes.length == 0) {
            return;
        }
        classes.forEach(element => {
            element.addEventListener('click', () => {
                this.modalOpen(`modal-${this.id}`);
            })
        });
    }

    /**
     * Функция открытия модального окна
     * @param {*} id   - id окна передаем
     * @returns 
     */
    modalOpen(id) {
        const modalOverlay = document.getElementById(id);
        const buttonClose = modalOverlay.querySelector('.modal-close');

        if (!modalOverlay || !buttonClose) {
            return;
        }
        modalOverlay.classList.add('active');
        setTimeout(() => {
            modalOverlay.classList.add('animation');
        }, 300);

        buttonClose.addEventListener('click', () => {
            this.modalClose(id);
        })
        modalOverlay.addEventListener('click', () => {
            this.modalClose(id);
        })
    }

    /**
     * Функция закрытия модального окна
     * @param {*} id  передается id окна
     * @returns 
     */
    modalClose(id) {
        const modalOverlay = document.getElementById(id);
        if (!modalOverlay) {
            return;
        }
        modalOverlay.classList.remove('animation');
        setTimeout(() => {
            modalOverlay.classList.remove('active');
        }, 300);
    }

    /** Рендеринг через JS
     * устаревший рендеринг 
     */
    renderModal() {
        let html = `<div class="modal-overlay" id="modal-${this.id}">
            <div class="modal-window" onclick="event.stopPropagation()">
                <button class="modal-close">
                    <span class="line line1"></span>
                    <span class="line line2"></span>
                </button>
            ${this.content}
            </div>
        </div>`;

        window.addEventListener('DOMContentLoaded', () => {
            document.body.insertAdjacentHTML('beforeend', html);
            this.findClasses();
        })
    }

    /**
     * расчитываем величину скролла для нейтрализации горизонтального шума 
     */
    checkScroll(){
        const body = document.body;
        // const inner = document.querySelector('.page'); // или другой элемент, в котором нужно вычислить ширину
        const scrollBarWidth = window.innerWidth - body.clientWidth;
        document.documentElement.style.setProperty('--scrollbar-width', `${scrollBarWidth}px`);
    }
}

/**
 * Вызов - инициализация класса
 */
document.addEventListener('DOMContentLoaded', () => {
    if (Array.isArray(modalData) && modalData.length > 0) {
        modalData.forEach(modal => {
            if (modal && modal.content) {
                let instance = new Modal(modal);
                instance.findClasses();
            }
        });
    }
});
