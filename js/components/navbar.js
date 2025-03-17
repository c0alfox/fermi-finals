import { drop_jwt, g } from "../utils.js";

function dropdownItem(text, link) {
    return g('li').appendAll(
        g('a').addClasses('dropdown-item')
            .setAttributes({
                'href': link
            })
            .setText(text)
    )
}

function dropdownDivider() {
    return g('li').appendAll(
        g('hr').addClasses('dropdown-divider')
    );
}

function dropdown(title = "", id = "navbarDropdown") {
    let _dropdown = g('div').addClasses('nav-item', 'dropdown');
    let _title = g('a')
        .addClasses('nav-link', 'dropdown-toggle')
        .setAttributes({
            'href': '#',
            'id': id,
            'role': 'button',
            'data-bs-toggle': 'dropdown',
            'aria-expanded': 'false'
        }).setText(title);
    let _list = g('ul')
        .addClasses('dropdown-menu', 'dropdown-menu-end')
        .setAttributes({'aria-labelledby': id});
    
    _dropdown.setTitle = (text) => {
        _title.setText(text);
        return _dropdown;
    }

    _dropdown.addItem = (item) => {
        _list.appendChild(item);
        return _dropdown;
    }

    _dropdown.addItems = (...items) => {
        _list.append(...items)
        return _dropdown;
    }

    _dropdown.clear = () => {
        _list.innerHTML = "";
        return _dropdown;
    }
    
    return _dropdown.appendAll(_title, _list);
}

export function navLink(text, link) {
    return g('li').addClasses('nav-item').appendAll(
        g('a')
            .addClasses('nav-link')
            .setAttributes({'href': link})
            .setText(text)
    )
}

export default function navbar(navbar_id = 'navbar') {
    let _navLinks = g('ul')
        .addClasses('navbar-nav', 'me-auto');

    let _dropdown = dropdown('Accedi o Registrati')
        .addItem(dropdownItem('Accedi', 'login.html'))
        .addItem(dropdownItem('Registrati', 'signup.html'));
    
    let _nav = g('nav').setId(navbar_id)
        .addClasses(
            'navbar',
            'navbar-expand-lg',
            'bg-primary',
            'bg-gradient'
        ).appendAll(
            g('div').addClasses('container').appendAll(
                g('div').addClasses('collapse', 'navbar-collapse')
                    .setId('navbarNav').appendAll(
                        _navLinks,
                        /*
                        g('form', 'd-flex', 'mx-auto', 'w-25')
                            .appendAll(
                                g('input', 'form-control', 'me-2'),
                                g('button', 'btn', 'btn-outline-light').setText('Cerca')
                            ),
                        */
                        _dropdown
                    ),
                g('button')
                    .addClasses('navbar-toggler')
                    .setAttributes({
                        'type': 'button',
                        'data-bs-toggle': 'collapse',
                        'data-bs-target': '#'+navbar_id,
                        'aria-controls': 'navbarNav',
                        'aria-expanded': 'false',
                        'aria-label': 'Toggle navigation'
                    })
                    .appendAll(
                        g('span').addClasses('navbar-toggler-icon')
                    )
            )
        )
    
    _nav.addLink = navlink => {
        _navLinks.appendChild(navlink);
        return _nav;
    }

    _nav.setActive = idx => {
        idx--;
        _navLinks.childNodes.forEach((elem, i) => {
            if (i == idx) {
                elem.childNodes[0].classList.add('active');
            } else {
                elem.childNodes[0].classList.remove('active');
            }
        });
        return _nav;
    }

    _nav.loadRemoteContent = userData => {
        if (!userData) return;
        
        let _logout = dropdownItem('Logout', 'index.html');
        _logout.addEventListener('click', drop_jwt);

        _dropdown
            .setTitle(`Benvenuto ${userData.Nome} ${userData.Cognome[0]}.`)
            .clear()
            .addItems(
                dropdownItem('Profilo', 'profilo.html'),
                dropdownDivider(),
                dropdownItem('Cambia account', 'login.html'),
                _logout
            );
    }

    return _nav;
}

export function fullNavbar(navbarId = 'navbar') {
    return navbar(navbarId)
        .addLink(navLink('Home', 'index.html'))
        .addLink(navLink('Cerca progetti', 'cerca.html'));
}