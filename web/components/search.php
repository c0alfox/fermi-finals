<script type="module" src="/static/js/components/search.js"></script>

<?php
require_once "components_prelude.php";

require_once "search/element.php";
require_once "search/header.php";

require_once "$root/functions/suggestions.php";

function search() { ?>
<div class="d-flex position-relative search-container mx-auto" style="min-width: 280px; max-width: 720px; flex-grow: 1;">
    <form class="input-group" action="/app/search.php">
        <input 
            name="q"
            type="text" 
            id="searchInput" 
            class="form-control border-end-0 border-primary-subtle" 
            placeholder="Cerca nel sito..." 
            aria-label="Cerca nel sito" 
            autocomplete="off"
        >
        <button class="btn border-primary-subtle bg-primary-subtle border-start-0" type="button" id="searchButton">
            <i class="icon i-search"></i>
        </button>
    </form>
    
    <div id="searchSuggestions" class="z-1 d-none search-dropdown position-absolute top-100 start-0 mt-1 shadow-sm border border-primary-subtle rounded bg-white w-100">
        <div>
        <?php
            Search\header("Progetti", true);

            $prjs = Suggestions\projects()->data;
        ?>
            <div id="projectsData">
            <?php
                if ($prjs['count'] == 0) {
                    Search\empty_element();
                } else {
                    foreach ($prjs['content'] as $p) {
                        Search\project_element(
                            $p['project_id'],
                            $p['title'],
                            $p['name'],
                            $p['surname'],
                            strtotime($p['project_datetime']),
                            $p['abstract']
                        );
                    }
                }
            ?>
            </div>
            <?php
                Search\header("Utenti");

                $users = Suggestions\users()->data;
            ?>
            <div id="usersData">
            <?php
                if ($users['count'] == 0) {
                    Search\empty_element();
                } else {
                    foreach($users['content'] as $user) {
                        $title = $user['name'] . ' ' . $user['surname'];
                        Search\user_element(
                            $user['user_id'],
                            $user['name'],
                            $user['surname'],
                            strtotime($user['user_datetime']),
                            $user['bio']
                        );
                    }
                }
            ?>
            </div>
        </div>
        <div class="p-2 text-center border-top">
            <small>Premi Invio per cercare o Esc per chiudere</small>
        </div>
    </div>
</div>
<?php } ?>