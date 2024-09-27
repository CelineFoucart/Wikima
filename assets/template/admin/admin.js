import * as bootstrap from 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './css/admin.css';
import 'fslightbox';
import "choices.js/public/assets/styles/choices.css";
import "../choicejs/choicejs.css";
import Choices from "choices.js";
import { togglePassword } from "./js/passwordAction";
import { slugAction } from "./js/slugAction";
import { characterAction } from "./js/characterAction";

(function () {
    'use strict'
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })

    const menus = document.querySelectorAll('.btn-menu');
    menus.forEach(menu => {
        menu.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const parent = menu.parentElement;
            if (parent) {
                parent.classList.toggle('menu-open');
            }
        })
    });

    characterAction();
    slugAction();
})()

// enable password toggle
document.querySelectorAll('[data-password]').forEach(element => {
    element.addEventListener('click', (e) => {
        togglePassword(element.dataset.target);
    })
});

// enable choice js
const elements = document.querySelectorAll('[data-choices]');
elements.forEach(element => {
    new Choices(element, {
        removeItems: true,
        removeItemButton: true,
        allowHTML: false,
        noResultsText: 'Aucun résultat',
        noChoicesText: 'Aucun élément à choisir',
        itemSelectText: 'Cliquez pour choisir',
        shouldSort: false,
    });
});

$(document).ready(function () {
    $(".dashboard-nav-dropdown-toggle").click(function (e) {
        e.preventDefault();
        $(this).closest(".dashboard-nav-dropdown")
            .toggleClass("show")
            .find(".dashboard-nav-dropdown")
            .removeClass("show");
        $(this).parent()
            .siblings()
            .removeClass("show");
    });
    
    $(document).ready(function () {
        // simple datatable
        $('.data-table').DataTable({
            language: {
                url: '/assets/plugins/DataTables/i18n/fr-FR.json',
            },
        });

        // datatable for sortable elements
        $(document).ready(function () {
            $('.data-table-sort').DataTable({
                paging: false,
                ordering:  false,
                language: {
                    url: '/assets/plugins/DataTables/i18n/fr-FR.json',
                },
            });
        });

        const options = {
            keyboard: true,
            size: 'fullscreen'
        };

        document.querySelectorAll('.lightbox-toggle').forEach(el => el.addEventListener('click', (e) => {
            e.preventDefault();
            const lightbox = new Lightbox(el, options);
            lightbox.show();
        }));
    });
});