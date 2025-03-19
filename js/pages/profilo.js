import API from "../api/wrapper.js";
import { g } from "../utils.js";

import { fullNavbar } from "../components/navbar.js";
import Skeleton from "../components/skeleton.js";

const _navbar = fullNavbar();

const user_profile = API.user_profile()
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
        ),
    );
});

