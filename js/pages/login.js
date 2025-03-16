import { api_fetch, g } from "../utils.js";
import { ErrorResponsePopup } from "../components/popup.js";

let popup = new ErrorResponsePopup();

window.addEventListener('load', () => {
    document.getElementById('login-form').addEventListener('submit', e => {
        e.preventDefault();

        api_fetch('POST', 'login.php', {
            'email': document.getElementById('email').value,
            'password': document.getElementById('password').value,
            'permissions': 0b11111
        }).then(resp => {
            if (resp.ok) {
                location.assign('index.html');
                return;
            }
            popup.setResponse(resp).show();
        });
    })
})