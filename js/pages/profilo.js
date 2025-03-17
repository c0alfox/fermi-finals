import { fullNavbar } from "../components/navbar.js";
import { fetch_user_profile, g } from "../utils.js";
import Skeleton from "../components/skeleton.js";

const _navbar = fullNavbar();

/**
 * @type {Promise<{
 *   message: string,
 *   user_data: {
 *     Email: string,
 *     Nome: string,
 *     Cognome: string,
 *     DataOraUtente: string,
 *     Bio: string|null,
 *     NumProgetti: number
 *   },
 *   projects: Array<any>
 * }>}
 */
const user_profile = fetch_user_profile()
    .then(resp => {
        if (resp.ok) {
            _navbar.loadRemoteContent(resp.data.user_data)
            return resp.data;
        }

        window.location.assign('login.html');
    });

window.addEventListener('load', ev => {
    document.body.append(
        _navbar,
        g('h1', 'text-center', 'pt-3').appendAll(
            g('span').setText('Profilo di '),
            Skeleton(g('span').setText('Lorem Ipsum'))
                .onFulfilled(user_profile.then(data => data.user_data), (elem, data) => {
                    elem.setText(data.Nome, data.Cognome);
                })
        )
    );
});

