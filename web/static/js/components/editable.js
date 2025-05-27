const EMPTY_EDITABLE_ATTRIBUTE = 'data-empty';

/**
 * Class representing an editable HTML element with a pencil icon and submission callback.
 */
export default class Editable {
    /**
     * @prop {HTMLElement}
     * @private
     */
    element;

    /**
     * @param {HTMLElement} e 
     */
    gaugeEmpty = (e) => {
        return e.innerText.trim() == "";
    }

    /**
     * @prop {Function}
     * @private
     */
    #callbacks = {
        submit: async () => {},
        beginEdit: async () => {},
        endEdit: async () => {}
    };

    /**
     * @prop {string}
     */
    #defaultText;

    /**
     * @prop {string}
     */
    lastValidValue;

    /**
     * Creates an editable wrapper around the given element.
     * @param {HTMLElement} element - The target element to make editable.
     * @param {string} defaultText - The default text to display when editable is empty
     */
    constructor(element, defaultText) {
        this.element = element;
        this.element.contentEditable = true;
        this.element.classList.add('editable');
        this.element.classList.add('pre-line');

        this.lastValidValue = this.element.innerText;
        this.#defaultText = defaultText;

        if (this.empty) {
            this.setEmpty(true);
            this.lastValidValue = "";
        }

        this.#addEventListeners();
    }

    /**
     * Adds event listeners to handle blur submission.
     */
    #addEventListeners() {
        this.element.addEventListener('blur', async () => {
            await this.#callbacks.endEdit(this);
            if (this.gaugeEmpty(this.element)) {
                this.setEmpty(true);
            }
            await this.#callbacks.submit(this);
        });

        this.element.addEventListener('focus', async () => {
            this.#prepareForInput();
            await this.#callbacks.beginEdit(this);
        });
    }

    /**
     * Registers a callback to be invoked when the element loses focus.
     * @param {("submit"|"beginEdit"|"endEdit")} eventType - Function to call on submission.
     * @param {async (elem: Editable) => void} callback - Function to call on submission.
     */
    on(eventType, callback) {
        switch (eventType) {
            case "submit":
                this.#callbacks.submit = callback;
                break;
            case "beginEdit":
                this.#callbacks.beginEdit = callback;
                break;
            case "endEdit":
                this.#callbacks.endEdit = callback;
                break;
            default:
                window.console.warn(`Event type ${eventType} is undefined`);
        }
    }

    /**
     *
     * @returns {boolean} - Whether the Editable is empty
     */
    get empty() {
        return this.element.hasAttribute(EMPTY_EDITABLE_ATTRIBUTE);
    }

    /**
     * 
     * @param {boolean} val
     */
    setEmpty(val) {
        if (val) {
            this.element.setAttribute(EMPTY_EDITABLE_ATTRIBUTE, true);
            this.element.innerText = this.#defaultText;
            this.element.classList.add('italic');
        } else {
            this.element.removeAttribute(EMPTY_EDITABLE_ATTRIBUTE);
            this.element.classList.remove('italic');
        }
    }

    valid() {
        this.lastValidValue = !this.empty 
            ? this.element.innerText
            : "";
    }

    reset() {
        this.element.innerHTML = this.lastValidValue;
        if (this.gaugeEmpty(this.element)) {
            this.setEmpty(true);
        }
    }

    #prepareForInput() {
        if (this.empty) {
            this.setEmpty(false);
            this.element.innerHTML = "";
        }
    }
}