/**
 * This file defines required functions for the administration interface of the wiki.
 * 
 * @author Celine Foucart.
 */

/**
 * Remove an element from the DOM.
 * 
 * @param {string} elementId    element to remove id 
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
        success: "Les données ont bien été sauvegardée",
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
async function sortable(list, path) {
    const events = document.querySelector(list);
    if (!events) {
        return;
    }
    await Sortable.create(events, {
        dataIdAttr: 'data-order',
        ghostClass: 'blue-background-class',
        onEnd: function (evt) {
            const data = [];

            for (let i = 0; i < events.children.length; i++) {
                const element = events.children[i];
                data.push({
                    id: element.getAttribute('id'),
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
                .then(response => toastify('success', JSON.stringify(response)))
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
 * Create a button for an action in a form.
 * 
 * @returns HTMLButtonElement
 */
function createButton() {
    const button = document.createElement('button');
    button.classList = "btn btn-default break";
    button.innerHTML = '<i class="fas fa-sync-alt"></i>';
    button.title = "Générer";
    button.type = "button";

    return button;
}

/**
 * Generate the fullname and slug value for characters in edition or creation form.
 */
function characterAction() {
    const firstname = document.querySelector('[data-type="firstname"]');
    const lastname = document.querySelector('[data-type="lastname"]');
    const fullname = document.querySelector('[data-type="fullname"]');

    if (firstname && lastname && fullname) {
        const button = createButton();
        fullname.after(button);
        fullname.style.width = 'calc(100% - 40px)';
        fullname.parentElement.style.display = "flex";

        button.addEventListener('click', (e) => {
            e.preventDefault();
            fullname.value = firstname.value + ' ' + lastname.value;
        });

        const slugInput = document.querySelector('[data-target="slug-character"]');
        console.log(slugInput)
        if (slugInput) {
            const slugButton = createButton();
            slugInput.after(slugButton);
            slugInput.style.width = 'calc(100% - 40px)';
            slugInput.parentElement.style.display = "flex";
            slugInput.parentElement.style.flexWrap = "wrap";

            slugButton.addEventListener('click', (e) => {
                e.preventDefault();
                const slug = slugify(firstname.value + ' ' + lastname.value);
                slugInput.value = slug;
            });
        }
    }
}

/**
 * Initiate the slug generation action event.
 */
function slugAction() {
    const slugInput = document.querySelector('[data-target="slug"]');

    if (!slugInput) {
        return;
    }

    const button = createButton();
    slugInput.after(button);
    slugInput.parentElement.style.display = "flex";
    slugInput.parentElement.style.flexWrap = "wrap";
    slugInput.style.width = 'calc(100% - 40px)';

    button.addEventListener('click', (e) => {
        e.preventDefault();
        const title = document.querySelector('[data-action="slug"]');
        if (title) {
            const slug = slugify(title.value);
            slugInput.value = slug;
        }
    });
}

window.addEventListener('load', () => {
    characterAction();
    slugAction();
});
