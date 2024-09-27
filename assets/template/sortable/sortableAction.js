import Sortable from 'sortablejs';

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
    div.innerHTML = `<div>${message}</div>`;
    
    const button = document.createElement('button');
    button.type = 'button';
    button.classList = `btn btn-sm ${buttonClass}`;
    button.innerHTML = '<i class="fa fa-times"></i>';
    button.addEventListener('click', (e) => {
        deleteAction(toastifyId);
    })

    div.appendChild(button);
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
export async function sortable(element) {
    const path = element.dataset.route;
    const withReload = element.dataset.reload == 1;
    await renderSortable(element, path, withReload);
}

/**
 * @param {HTMLElement} element
 * @param {string}      path
 */
export async function renderSortable(element, path, withReload) {
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

document.querySelectorAll('[data-sortable]').forEach(element => {
    sortable(element);
});
