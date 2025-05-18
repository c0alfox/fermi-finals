import Editable from './components/editable.js';
import error_popup from './components/error_response_element.js';

import api_fetch from './lib/fetch.js';

const _abs = document.getElementById('abs');

let abs = new Editable(_abs, "Il progetto non ha un abstract");

abs.on('endEdit', async e => {
    let val = e.element.innerText.trim();
    /*
    return api_fetch('PUT', 'account', {
        'bio': val
    }).then(resp => {
        if (!resp.ok) {
            if (resp.status == 401) {
                window.location.assign('/app/auth/login.php');
            }
            e.reset();
            error_popup.setResponse(resp).show();
        } else {
            e.element.innerText = e.element.innerText.trim();
            e.valid();
        }
    });
    */
});
    