<?php
require_once "components_prelude.php";
require_once "search/element.php";
require_once "search/header.php";

function search() { ?>
<div class="d-flex position-relative search-container mx-auto" style="min-width: 280px; max-width: 720px; flex-grow: 1;">
    <div class="input-group">
        <input 
            type="text" 
            id="searchInput" 
            class="form-control border-end-0 border-primary-subtle" 
            placeholder="Cerca nel sito..." 
            aria-label="Cerca nel sito" 
            autocomplete="off"
        >
        <button class="btn btn-outline-primary bg-gradent border-start-0" type="button" id="searchButton">
            <i class="icon i-search"></i>
        </button>
    </div>
    
    <div id="searchSuggestions" class="search-dropdown position-absolute top-100 start-0 mt-1 shadow-sm border rounded bg-white w-100">
        <div>
        <?php
            Search\header("Progetti");
            Search\element("Progetto di prova", time(), "DESCRIZIONE DEL PROGETTO PERCHÉ IL PROGETTO È BELLO QUANDO È PROGETTO");
            Search\header("Utenti");
            Search\element("Giuseppe Pappalardo", time(), "Biografia del profilo croppata a 100 caratteri");
        ?>
        </div>
        <div class="p-2 text-center border-top">
            <small>Premi Invio per cercare o Esc per chiudere</small>
        </div>
    </div>
</div>
<?php } ?>