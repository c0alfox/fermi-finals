import navbar from "../components/navbar.js";
import { navLink } from "../components/navbar.js";

import { fetch_user_profile } from "../utils.js";

const _navbar = navbar()
    .addLink(navLink('Home', 'index.html'))
    .addLink(navLink('Cerca progetti', 'cerca.html'));

const user_profile = fetch_user_profile()
    .then(resp => {
        if (resp.ok) _navbar.loadRemoteContent(resp.data.user_data)
        return resp.data;
    })

window.addEventListener('load', ev => {
    document.body.appendChild(
        _navbar.setActive(1)
    );
});

