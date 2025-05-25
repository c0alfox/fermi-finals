import api_fetch from "../lib/fetch.js";

/**
 * @type { Array<HTMLElement> }
 */
const buttons = document.getElementsByClassName('notif-close');
const badge = document.getElementById('notification-badge');
let badgeValue = Number(badge.innerText);

const attrName = "data-id";

const updateBadge = (newVal) => {
    badgeValue = newVal;
    if (newVal == 0) {
        badge.classList.add('d-none');
    } else {
        badge.innerText = newVal;
        badge.classList.remove('d-none');
    }
}

const deletion = (parent) => {
    updateBadge(badgeValue - 1);
    parent.remove();
}

/**
 * @param { MouseEvent } e 
 */
const clickListener = async (e) => {
    const target = e.currentTarget;
    let id = target.getAttribute(attrName);

    let resp = await api_fetch('DELETE', `notifs/delete?id=${id}`);
    if (resp.ok) {
        deletion(target.parentNode.parentNode);
    } else {
        window.console.error(resp);
    }
};

for (let el of buttons) {
    if (!el.hasAttribute(attrName)) {
        window.console.warn("Element does not have required attribute");
        continue;
    }

    el.addEventListener('click', clickListener);
}