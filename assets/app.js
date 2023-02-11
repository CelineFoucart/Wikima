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

// start the Stimulus application
import './bootstrap';

function updateNote(id, note) {
    const route = `/api/admin/note/${id}/processed`;
    fetch(route, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => {
            if (!response.ok) {
                alert("L'opération a échoué");
            } else {
                if (note.classList.contains("bg-success")) {
                    note.classList.remove('bg-success');
                    note.classList.add('bg-danger');
                    note.innerHTML = "A Traiter";
                } else {
                    note.classList.remove('bg-danger');
                    note.classList.add('bg-success');
                    note.innerHTML = "Traité";
                }
            }
        })
        .catch(error => alert("L'opération a échoué.") );
}

window.onload = () => {
    // Enabled tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Enabled updating note processed
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
}
