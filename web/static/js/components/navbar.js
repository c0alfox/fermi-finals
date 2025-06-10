import api_fetch from "../lib/fetch.js";
import g from "../lib/g.js";

/****************************
 *    Fetching from DOM     *
 ****************************/

/**
 * @type { Array<HTMLElement> }
 */
const buttons = document.getElementsByClassName('notif-close');
const badge = document.getElementById('notification-badge');
const notificationContainer = document.getElementById('notification-container');

let badgeValue = Number(badge.innerText);

const attrName = "data-id";

/****************************
 *    WebSocket Connection  *
 ****************************/

let id_resp = await api_fetch('GET', 'user/get_id');
let user_id = id_resp.data.user_id;

let ws = new WebSocket(`/ws/notifs/${user_id}/`);

ws.onopen = () => {
    window.console.log("Notification connection established");
}

ws.onclose = () => {
    window.console.log("Notification connection terminated");
}

/**
 * @param {MessageEvent} m 
 */
ws.onmessage = m => {
    let body = JSON.parse(m.data);
    body['notification_datetime'] = new Date(Date.parse(body['notification_datetime']));

    notificationContainer.prepend(notification(body));
    updateBadge(badgeValue + 1);
}

/****************************
 *  Notification Component  *
 ****************************/

/**
 * 
 * @param {Date} date 
 */
const date_format = (date) => {
    let y = date.getFullYear();
    let m = date.getMonth().toString().padStart(2, '0');
    let d = date.getDate().toString().padStart(2, '0');

    let hrs = date.getHours().toString().padStart(2, '0');
    let min = date.getMinutes().toString().padStart(2, '0');

    return `${d}/${m}/${y} ${hrs}:${min}`;
}


/**
 * @typedef {Object} Notification
 * @property {Number} notification_id
 * @property {string} title
 * @property {string?} action_link
 * @property {string?} description
 * @property {Number} user_id
 * @property {Date} notification_datetime
 */

/**
 * 
 * @param {Notification} notif 
 */
const notification = (notif) => {
    let date = date_format(notif.notification_datetime);

    let notif_footer = g('div', 'd-flex', 'align-items-center', 'justify-content-between', 'mt-2').appendAll(
        g('p', 'small', 'text-muted', 'mb-0').setText(date)
    );

    if (notif.action_link !== null) {
        notif_footer.appendAll(
            g('a', 'btn', 'btn-sm', 'btn-primary').setAttributes({
                'href': notif.action_link
            }).setText('Vai')
        )
    }

    return g('div', 'notification-item', 'p-3').appendAll(
        g('div', 'd-flex', 'justify-content-between', 'align-items-start', 'mb-2').appendAll(
            g('h6', 'fw-bold', 'mb-0').setText(notif.title),
            g('button', 'btn-close', 'notif-close').setAttributes({
                'type': 'button',
                'aria-label': 'Close',
                'data-id': notif.notification_id
            })
        ),
        g('p', 'notification-content', 'text-muted', 'mb-2').setText(notif.description),
        notif_footer
    )
}


/****************************
 *       Badge Helpers      *
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