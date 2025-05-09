import T from "./t.js";

/**
 * HTML Element builder
 * @param {keyof HTMLElementTagNameMap} element_name 
 * @param {...string} classes
 * @returns {T<HTMLElement>}
 */
export default function g(element_name, ...classes) {
    let elem = document.createElement(element_name);
    elem.classList.add(...classes);

    return T(elem);
}
