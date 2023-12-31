/**
 * Remove an element from the DOM.
 * 
 * @param {String} elementId    element to remove id 
 */
function deleteAction(elementId) {
    const element = document.querySelector(`#${elementId}`);
    if (element) {
        element.remove();
    }
}

/**
 * Create a alert that disappear after 5 seconds. 
 * @param {string} type     alert type, success or error
 * @param {string} text     the message to display
 */
function toastify(type = 'success', text = null) {
    // create container if not exist
    let container = document.querySelector('.toastify-container');
    if (!container) {
        container = document.createElement('div');
        container.classList = 'toastify-container';
        document.body.appendChild(container);

    }

    const toastifyId = "toastify-" + Date.now();
    const messages = {
        success: "Les données ont bien été sauvegardées.",
        error: "L'opération a échoué"
    };
    const validStatus = ['success', 'error'];
    type = (validStatus.includes(type)) ? type : 'success';

    const div = document.createElement('div');
    if (type === 'success') {
        div.classList = 'toastify toastify-success';
    } else {
        div.classList = 'toastify toastify-error';
    }
    div.id = toastifyId;
    const buttonClass = (type === 'success') ? 'btn-success' : 'btn-danger';
    let message = (text instanceof String) ? text : messages[type]
    div.innerHTML = `<div>${message}</div> <button type="button" class="btn btn-sm ${buttonClass}" onclick="deleteAction('${toastifyId}')"><i class="fa fa-times"></i></button>`;
    container.appendChild(div);

    setTimeout(function () {
        deleteAction(toastifyId);
    }, 3000);
}

/**
 * Create a sortable list.
 * 
 * @param {string} list     selector of the list
 * @param {string} path     the path used to send the updated data
 */
async function sortable(list, path, withReload = false) {
    const events = document.querySelector(list);
    if (!events) {
        return;
    }

    await renderSortable(events, path, withReload);
}

/**
 * @param {HTMLElement} element
 * @param {string}      path
 */
async function renderSortable(element, path, withReload) {
    await Sortable.create(element, {
        dataIdAttr: 'data-order',
        ghostClass: 'blue-background-class',
        onEnd: function (evt) {
            const data = [];

            for (let i = 0; i < element.children.length; i++) {
                const child = element.children[i];
                data.push({
                    id: child.getAttribute('id'),
                    position: i
                })
            }

            fetch(path, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error("Il y a eu une erreur et les données n'ont pas été sauvegardées.");
                    }
                })
                .then(response => {
                    toastify('success', JSON.stringify(response))
                    
                    if (withReload === true) {
                        location.reload();
                    }
                })
                .catch(error => toastify('error', error));
        }
    });
}

/**
 * Display a tab body from the get params in the url.
 * 
 * @returns
 */
function getTabTargetFromUrl() {
    const search = window.location.search.substring(1);

    if (search.length > 1) {
        const parameters = search.substring(1).split('=');
        if (parameters[0] !== "tab") {
            return;
        }
        const id = parameters[1];

        const target = document.querySelector(`#${id}`);

        if (target) {
            document.querySelectorAll('.active').forEach(item => {
                item.classList.remove('active');
                item.classList.remove('in');
            });

            target.classList.add("active");
            target.classList.add("in");
            const tabs = document.querySelector(`a[href="#${id}"]`).parentElement;
            tabs.classList.add('active');
        }
    }
}

/**
 * Transforms a string into a valid slug
 * 
 * @param {string} str 
 * @returns 
 */
function slugify(str) {
    str = str.trim();
    str = str.toLowerCase();
    str = str.replace(/[\s_-]+/g, '-');
    str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    str = str.replace(/[^\w\s-]/g, '');

    return str;
}

/**
 * Change the type of a password input to hide or show the password.
 * 
 * @param {String} targetId 
 */
function togglePassword(targetId) {
    const input = document.querySelector(`#${targetId}`);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

/**
 * Generate the fullname and slug value for characters in edition or creation form.
 */
function characterAction() {
    const firstname = document.querySelector('[data-type="firstname"]');
    const lastname = document.querySelector('[data-type="lastname"]');
    const fullname = document.querySelector('[data-type="fullname"]');

    if (firstname && lastname && fullname) {
        const button = document.querySelector('#fullnameAction');
        button.addEventListener('click', (e) => {
            e.preventDefault();
            fullname.value = firstname.value + ' ' + lastname.value;
        });
    }
}

/**
 * Initiate the slug generation action event.
 */
function slugAction() {
    const slugButtons = document.querySelectorAll('[data-action="slugify"]');

    slugButtons.forEach(element => {
        element.addEventListener('click', (e) => {
            e.preventDefault();
            const title = document.querySelector(element.dataset.source);
            const slugInput = document.querySelector(element.dataset.target);

            if (title) {
                const slug = slugify(title.value);
                slugInput.value = slug;
            }
        });
    });
}

/**
 * Change the type of a password input to hide or show the password.
 * 
 * @param {String} targetId 
 */
function togglePassword(targetId) {
    const input = document.querySelector(`#${targetId}`);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

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
                url: '/assets/DataTables/i18n/fr-FR.json',
            },
        });

        // datatable for sortable elements
        $(document).ready(function () {
            $('.data-table-sort').DataTable({
                paging: false,
                ordering:  false,
                language: {
                    url: '/assets/DataTables/i18n/fr-FR.json',
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