import api_fetch from "../lib/fetch.js";
import g from "../lib/g.js";

const searchBar = document.getElementById('searchInput');

const suggestionBox = document.getElementById('searchSuggestions');
const projectsData = document.getElementById('projectsData');
const usersData = document.getElementById('usersData');

const emptyElement = g('div', 'search-item', 'p-3')
    .appendAll(g('p', 'text-center', 'italic', 'mb-0')
        .setText('Nessun risultato trovato')
    );

const projectGenerator = (project) => 
    g('a', 'search-link').setAttributes({
        'href': `/app/project/details.php?id=${project.project_id}`
    }).appendAll(
        g('div', 'search-item', 'px-3', 'py-2', 'border-bottom').appendAll(
            g('div', 'clearfix').appendAll(
                g('div', 'float-start').appendAll(
                    g('h5', 'mb-0').setText(project.title),
                    g('span', 'small', 'text-muted').setText(`Di ${project.name} ${project.surname}`)
                ),
                g('small', 'text-muted', 'float-end').setText(
                    (new Date(project.project_datetime)).toLocaleDateString('it-IT')
                )
            ),
            g('div', 'ms-4', 'ps-1', 'mt-1').appendAll(
                project.abstract == null
                    ? g('small', 'text-muted', 'italic').setText("L'utente non ha una biografia")
                    : g('small', 'text-muted').setText(project.abstract)
            )
        )
    );

const userGenerator = (user) => 
    g('a', 'search-link').setAttributes({
        'href': `/app/user/profile.php?id=${user.user_id}`
    }).appendAll(
        g('div', 'search-item', 'px-3', 'py-2', 'border-bottom').appendAll(
            g('div', 'clearfix').appendAll(
                g('h5', 'float-start', 'mb-0').setText(
                    `${user.name} ${user.surname}`
                ),
                g('small', 'text-muted', 'float-end').setText(
                    (new Date(user.user_datetime)).toLocaleDateString('it-IT')
                )
            ),
            g('div', 'ms-4', 'ps-1', 'mt-1').appendAll(
                user.bio == null
                    ? g('small', 'text-muted', 'italic').setText("L'utente non ha una biografia")
                    : g('small', 'text-muted').setText(user.bio)
            )
        )
    );


let searchRequestTimeout = null;

searchBar.addEventListener('input', e => {
    if (searchRequestTimeout !== null) {
        window.clearTimeout(searchRequestTimeout);
    }

    searchRequestTimeout = window.setTimeout(async () => {
        let result = await api_fetch(
            'GET',
            `suggestions/search?q=${encodeURIComponent(e.target.value)}`
        );

        projectsData.innerHTML = "";

        if (result.data.projects.count == 0) {
            projectsData.appendChild(emptyElement.cloneNode(true));
        } else {
            result.data.projects.content.forEach(el => {
                projectsData.appendChild(projectGenerator(el));
            });
        }

        usersData.innerHTML = "";
        if (result.data.users.count == 0) {
            usersData.appendChild(emptyElement.cloneNode(true));
        } else {
            result.data.users.content.forEach(el => {
                usersData.appendChild(userGenerator(el));
            });
        }
        
        searchRequestTimeout = null;
    }, 250);
});

let blurTimeout = null;
let focusTimeout = null;

searchBar.addEventListener('focus', () => {
    window.clearTimeout(blurTimeout);
    suggestionBox.classList.remove('d-none');
    focusTimeout = window.setTimeout(() => {
        suggestionBox.classList.add('active');
        focusTimeout = null;
    }, 20);
});

searchBar.addEventListener('blur', () => {
    window.clearTimeout(focusTimeout);
    suggestionBox.classList.remove('active');
    blurTimeout = window.setTimeout(() => {
        suggestionBox.classList.add('d-none');
        blurTimeout = null;
    }, 200);
})