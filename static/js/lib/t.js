const TProto = {
    

};

/**
 * Transform HTML Element into GeneratedElement
 * @template {HTMLElement} E
 * @param {E} elem 
 * @returns {T<E>}
 */
export default function T(elem) {
    /**
     * 
     * @param  {...string} tokens 
     * @returns {T<E>}
     */
    elem.addClasses = (...tokens) => {
        elem.classList.add(...tokens);
        return elem;
    },

    /**
     * 
     * @param  {...string} tokens 
     * @returns {T<E>}
     */
    elem.removeClasses = (...tokens) => {
        elem.removeClasses(...tokens);
        return elem;
    }

    /**
     * 
     * @param  {...HTMLElement} nodes
     * @returns {T<E>}
     */
    elem.appendAll = (...nodes) => {
        elem.append(...nodes);
        return elem;
    }

    /**
     * 
     * @param {string} id 
     * @returns {T<E>}
     */
    elem.setId = id => {
        elem.id = id
        return elem;
    }

    /**
     * 
     * @param {Object} attrs 
     * @returns {T<E>}
     */
    elem.setAttributes = attrs => {
        for (const [key, val] of Object.entries(attrs)) {
            elem.setAttribute(key, val)
        }
        return elem;
    }

    /**
     * 
     * @param {string} text 
     * @returns {T<E>}
     */
    elem.setText = text => {
        elem.innerText = text;
        return elem;
    }
    return elem;
}