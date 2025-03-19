import '../api/wrapper.js';
import { g } from "../utils.js";

/**
 * A Bootstrap modal wrapper providing simplified interface for popups
 * @typedef Popup
 * @property {(elem: HTMLElement, callback: Function, id?: string) => Popup} addAction - Adds action button to modal footer
 * @property {(elem: HTMLElement) => Popup} removeAction - Removes an action button from modal footer
 * @property {() => Popup} clearActions - Removes all action buttons
 * @property {(title: string) => Popup} setTitle - Sets the modal title
 * @property {(...elements: HTMLElement[]) => Popup} setBody - Adds elements to modal body
 * @property {() => Popup} clearBody - Removes all content from modal body
 * @property {() => Popup} show - Shows the modal
 * @property {() => Popup} hide - Hides the modal
 * @property {() => Popup} toggle - Toggles modal visibility
 * @property {() => Popup} clear - Resets modal (clears title, body, and actions)
 */


/**
 * Popup constructor
 * @param {string} [popup_id = "default__popup"]
 * @returns {PopupType}
 */
export default function Popup(popup_id = 'default__popup') {
    let bootstrap_modal = null;

    let _title = g('h5', 'modal-title');
    let _body = g('div', 'modal-body');

    let footer_id = popup_id + '__footer';
    let _footer = g('div', 'modal-footer').setId(footer_id);

    let _content = g('div', 'modal-content');

    let _modal = g('div', 'modal')
        .setAttributes({
            'id': popup_id,
            'tabindex': -1
        })
        .appendAll(
            g('div',
                'modal-dialog', 'modal-dialog-centered', 'modal-dialog-scrollable'
            ).setAttributes({ 'tabindex': -1 }).appendAll(
                _content.appendAll(
                    g('div', 'modal-header').appendAll(
                        _title,
                        g('button', 'btn-close').setAttributes({
                            'data-bs-dismiss': 'modal',
                            'aria-label': 'Close'
                        })
                    ),
                    _body
                ),
            )
        );

    let actions = new Set();

    let removeFooter = () => {
        let footer;
        if (footer = document.getElementById(footer_id)) {
            _content.removeChild(footer);
        }
    };

    let loadFooter = () => {
        let footer;
        if (footer = document.getElementById(footer_id)) {
            footer.replaceWith(_footer);
        } else {
            _content.appendChild(_footer);
        }
    };

    let refreshFooter = () => {
        if (actions.size == 0) {
            removeFooter();
        } else {
            loadFooter();
        }
    };

    /**
     * Add an action to the footer list
     * @param {HTMLElement} elem 
     * @param {Function} callback 
     * @param {string} id 
     */
    this.addAction = (elem, callback, id = null) => {
        let action_id = id ?? `${footer_id}__action${actions.length + 1}`;
        elem.id = action_id;
        elem.addEventListener('click', callback);
        actions.add(action_id);
        _footer.appendChild(elem);
        refreshFooter();
        return this;
    };

    this.removeAction = (elem) => {
        _footer.removeChild(elem);
        actions.delete(elem.id);
        refreshFooter();
        return this;
    };

    this.clearActions = () => {
        actions = new Set();
        removeFooter();
        return this;
    };

    this.setTitle = (title) => {
        _title.setText(title);
        return this;
    };

    this.setBody = (...elements) => {
        _body.appendAll(...elements);
        return this;
    };

    this.clearBody = () => {
        while (_body.firstChild) {
            _body.removeChild(_body.firstChild);
        }
        return this;
    };

    this.show = () => {
        if (bootstrap_modal === null) {
            window.console.warn('Bootstrap modal not initialized');
            return;
        }

        bootstrap_modal.show();
        return this;
    }

    this.hide = () => {
        if (bootstrap_modal === null) {
            window.console.warn('Bootstrap modal not initialized');
            return;
        }

        bootstrap_modal.hide();
        return this;
    }

    this.toggle = () => {
        if (bootstrap_modal === null) {
            window.console.warn('Bootstrap modal not initialized');
            return;
        }

        bootstrap_modal.toggle();
        return this;
    }

    this.clear = () => {
        this.clearBody();
        this.clearActions();
        _title.textContent = "";
        return this;
    }
    
    window.addEventListener('load', () => {
        document.body.appendChild(_modal);
        bootstrap_modal = new bootstrap.Modal(document.getElementById(popup_id), {});
    });
}

/**
 * 
 * @param {string} [popup_id="error__popup"]
 */
export function ErrorResponsePopup(popup_id = "error__popup") {
    /**
     * 
     * @param {import('../api/wrapper.js').APIResponse} response
     * @returns { ErrorResponsePopup }
     */
    this.setResponse = (response) => {
        this.clear()
            .setTitle(`Errore ${response.status}`)
            .setBody(g('p').setText(`${response.data.message} (err. ${response.status})`));
        return this;
    }
}