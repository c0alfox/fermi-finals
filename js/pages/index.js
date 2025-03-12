import navbar from "../components/navbar.js";
import { navLink } from "../components/navbar.js";

window.addEventListener('load', ev => {
    document.body.appendChild(
        navbar()
            .addLink(navLink('Home', 'index.html'))
            .addLink(navLink('Cerca progetti', 'cerca.html'))
            .setActive(1)
    );
})