import api_fetch from "../lib/fetch";

/**
 * @type { Array<HTMLElement> }
 */
let buttons = document.getElementsByClassName('notif-close');
const attrName = "data-id";

/**
 * @param { MouseEvent } e 
 */
const clickListener = (e) => {
    let id = e.target.getAttribute(attrName);

    window.console.log(id);
};

for (let el of buttons) {
    if (!el.hasAttribute(attrName)) {
        window.console.warn("Element does not have required attribute");
        continue;
    }

    el.addEventListener('click', clickListener);
}