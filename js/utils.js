const jwt_location = 'auth_jwt';

function lorem() {
    return "Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias nesciunt aperiam illo itaque culpa praesentium eius repellat placeat, nobis quae dolore autem voluptate maiores consequuntur, id unde architecto excepturi delectus. Excepturi quae dolorem ullam voluptatem maiores et similique iste aperiam cupiditate quos aspernatur, vero, alias at culpa doloremque quo iusto."
}

function get_jwt() {
    return window.sessionStorage.getItem(jwt_location);
}

function set_jwt(jwt) {
    window.sessionStorage.setItem(jwt_location, jwt);
}

function drop_jwt() {
    window.sessionStorage.removeItem(jwt_location);
}

/**
 * Transform HTML Element to GeneratedElement
 * @template {HTMLElement} T
 * @param {T} elem 
 */
function t(elem) {
    this = elem;

    /**
     * 
     * @param  {...string} tokens 
     * @returns {t<T>}
     */
    this.addClasses = (...tokens) => {
        elem.classList.add(...tokens);
        return elem;
    }

    /**
     * 
     * @param  {...string} tokens 
     * @returns {t<T>}
     */
    this.removeClasses = (...tokens) => {
        elem.removeClasses(...tokens);
        return elem;
    }

    /**
     * 
     * @param  {...HTMLElement} nodes
     * @returns {t<T>}
     */
    this.appendAll = (...nodes) => {
        elem.append(...nodes);
        return elem;
    }

    /**
     * 
     * @param {string} id 
     * @returns {t<T>}
     */
    this.setId = id => {
        elem.id = id
        return elem;
    }

    /**
     * 
     * @param {Object} attrs 
     * @returns {t<T>}
     */
    this.setAttributes = attrs => {
        for (const [key, val] of Object.entries(attrs)) {
            elem.setAttribute(key, val)
        }
        return elem;
    }

    /**
     * 
     * @param {string} text 
     * @returns {t<T>}
     */
    this.setText = text => {
        elem.innerText = text;
        return elem;
    }
}


/**
 * HTML Element builder
 * @param {keyof HTMLElementTagNameMap} element_name 
 * @param {...string} classes
 * @returns {t<HTMLElement>}
 */
function g(element_name, ...classes) {
    let elem = document.createElement(element_name);
    elem.classList.add(...classes);

    return t(elem);
}

export { g, t, get_jwt, set_jwt, drop_jwt }