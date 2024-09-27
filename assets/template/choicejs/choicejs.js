import "choices.js/public/assets/styles/choices.css";
import "./choicejs.css";
import Choices from "choices.js";

const elements = document.querySelectorAll('[data-choices]');
elements.forEach(element => {
    new Choices(element, {
        removeItems: true,
        removeItemButton: true,
        allowHTML: false,
        noResultsText: 'Aucun résultat',
        noChoicesText: 'Aucun élément à choisir',
        itemSelectText: 'Cliquez pour choisir'
    });
});

document.querySelectorAll('[name="search_terms"]').forEach(element => {
    element.removeAttribute('name');
});