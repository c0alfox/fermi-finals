import Editable from '../editable.js';

let bio = new Editable(document.getElementById('bio'), "Nessuna biografia inserita");

bio.on("submit", e => {
    if (e.empty) {
        return;
    }

    let val = e.element.innerText.trim();
    window.console.log(val);
});