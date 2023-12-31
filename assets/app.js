/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/basics.css';
import './styles/header.css';
import './styles/index.css';
import './styles/content.css';
import './styles/timeline.css';
import './styles/user-icon.css';
import './styles/idiom.css';
import './styles/forum.css';
import searchInput from './lib/search-input.js';
import updateNote from './lib/update-note.js';

// start the Stimulus application
import './bootstrap';

window.onload = () => {
    // Enable tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Enable updating note processed
    const notes = document.querySelectorAll('[data-action="update-processed"]');
    if (notes.length > 0) {
        notes.forEach(note => {
            note.addEventListener('click', (e) => {
                e.preventDefault();
                const id = note.dataset.id;
                if (id) {
                    updateNote(id, note);
                }
            })
        });
    }

    // Enable search input in navbar
    searchInput('#searchable-dropdown-input', '#searchable-dropdown');

    // Enable search input in map show
    searchInput('#search-positions-input', '#positions-container');
}
