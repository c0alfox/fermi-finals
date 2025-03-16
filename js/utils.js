const api_root = "http://localhost/TPSI/Progetto/api/v1/";
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

async function api_fetch(request_method, endpoint, request_body, options) {
    const resp = await fetch(api_root + endpoint, {
        method: request_method,
        headers: {
            'Authorization': `Bearer ${get_jwt()}`,
            ...(options?.headers ?? {})
        },
        body: JSON.stringify(request_body),
        ...options
    });

    let {jwt, ...json} = await resp.json();
    set_jwt(jwt ?? get_jwt());

    return { status: resp.status, ok: resp.ok, data: json };
}

async function fetch_user_profile() {
    return await api_fetch('GET', 'account.php');
}

async function fetch_user_data() {
    const resp = await fetch_user_profile();
    return resp.data?.user_data ?? null;
}

/**
 * @typedef {Object} Generated
 * @prop {GFunction} addClasses
 * @prop {GFunction} removeClasses
 * @prop {GFunction} appendAll
 * @prop {GFunction} setId
 * @prop {GFunction} setAttributes
 * @prop {GFunction} setText
 */

/**
 * @typedef {Generated & HTMLElement} GeneratedElement
 */

/**
 * @callback GFunction
 * @returns {GeneratedElement}
 */

/**
 * Transform HTML Element to GeneratedElement
 * @param {HTMLElement} elem 
 * @returns {GeneratedElement}
 */
function t(elem) {
    elem.addClasses = (...tokens) => {
        elem.classList.add(...tokens);
        return elem;
    }

    elem.removeClasses = (...tokens) => {
        elem.removeClasses(...tokens);
        return elem;
    }

    elem.appendAll = (...nodes) => {
        elem.append(...nodes);
        return elem;
    }

    elem.setId = id => {
        elem.id = id
        return elem;
    }

    elem.setAttributes = attrs => {
        for (const [key, val] of Object.entries(attrs)) {
            elem.setAttribute(key, val)
        }
        return elem;
    }

    elem.setText = text => {
        elem.innerText = text;
        return elem;
    }

    return elem;
}


/**
 * HTML Element builder
 * @param {keyof HTMLElementTagNameMap} element_name 
 * @param {...string} classes 
 * @returns {GeneratedElement & HTMLElement}
 */
function g(element_name, ...classes) {
    let elem = document.createElement(element_name);
    elem.classList.add(...classes);

    return t(elem);
}

export { g, t, drop_jwt, api_fetch, fetch_user_profile, fetch_user_data }