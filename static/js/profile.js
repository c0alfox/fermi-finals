import Editable from './components/editable.js';
import error_popup from './components/error_response_element.js';

import api_fetch from './lib/fetch.js';

const _bio = document.getElementById('bio');

let bio = new Editable(_bio, "Nessuna biografia inserita");

bio.on('endEdit', async e => {
    let val = e.element.innerText.trim();
    return api_fetch('PUT', 'account', {
        'bio': val
    }).then(resp => {
        if (!resp.ok) {
            e.reset();
            error_popup.setResponse(resp).show();
        } else {
            e.element.innerText = e.element.innerText.trim();
            e.valid();
        }
    });
});