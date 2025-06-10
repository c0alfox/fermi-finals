import api_fetch from "../lib/fetch.js";

/****************************
 *    Fetching from DOM     *
 ****************************/

/**
 * @type { Array<HTMLElement> }
 */
const buttons = document.getElementsByClassName('notif-close');
const badge = document.getElementById('notification-badge');
let badgeValue = Number(badge.innerText);

const attrName = "data-id";

/****************************
 *    WebSocket Connection  *
 ****************************/

let id_resp = await api_fetch('GET', 'user/get_id');
let user_id = id_resp.data.user_id;

let ws = new WebSocket(`/ws/recv/${user_id}/`);

// Send a message every 20 seconds to keep the websocket connection alive
const keepAliveInterval = 20_000;
let i = 1;
setInterval(() => {
    ws.send(`User #${user_id} Keep Alive #${i}`);
    i++;
}, keepAliveInterval);

ws.onmessage = m => {
    window.console.log(m);
}

// TODO: Implement WebSocket notification handling here

/****************************
 *     Helper Functions     *
 ****************************/

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

/****************************
 *    Loading Listeners     *
 ****************************/

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