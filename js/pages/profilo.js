
import { fullNavbar } from "../components/navbar.js";
import { fetch_user_profile } from "../utils.js";

const _navbar = fullNavbar();

const user_profile = fetch_user_profile()
    .then(resp => {
        if (resp.ok) {
            _navbar.loadRemoteContent(resp.data.user_data)
            return resp.data;
        }
    
        window.location.assign('login.html');
    });

window.addEventListener('load', ev => {
    document.body.appendChild(
        _navbar
    );
});

