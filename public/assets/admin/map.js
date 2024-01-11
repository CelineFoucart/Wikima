function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
        // if present, the header is where you move the DIV from:
        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
        // otherwise, move the DIV from anywhere inside the DIV:
        elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        // stop moving when mouse button is released:
        document.onmouseup = null;
        document.onmousemove = null;
    }
}

const controller = new AbortController();
const { signal } = controller;

class MapMarker {
    container = null;
    coordinates = {
        width: null,
        height: null,
        offsetTop: null,
        offsetLeft: null
    }
    editModal = null;
    showModal = null;
    positionY = null;
    positionX = null;
    newPosition = null;
    placeElement = null;
    placeChoice = null;

    constructor(selector) {
        this.container = document.querySelector(selector);
        if (!this.container) {
            console.error("This container does not exist")
        } else {
            this.coordinates = {
                width: this.container.clientWidth,
                height: this.container.clientHeight,
                offsetTop: this.container.getBoundingClientRect().top + window.scrollY,
                offsetLeft: this.container.getBoundingClientRect().left + window.scrollX
            }
        }

        this.editModal = new bootstrap.Modal(document.querySelector('#editModal'), {
            keyboard: false
        });

        this.showModal = new bootstrap.Modal(document.querySelector('#showModal'), {
            keyboard: false
        });

        // init color input
        Coloris({
            el: '#color',
            swatches: ['#7952b3', '#dc3545', '#92400e', '#198754', '#3c8dbc', '#212529']
        });
        Coloris.setInstance('#color', { theme: 'polaroid' });
        this.setChoice();
    }

    setChoice() {
        if (this.placeChoice === null) {
            this.placeElement = document.querySelector('#placeSelect');
            this.placeChoice = new Choices(this.placeElement, {
                removeItems: true,
                removeItemButton: true,
                allowHTML: false,
                noResultsText: 'Aucun résultat',
                noChoicesText: 'Aucun élément à choisir',
                itemSelectText: 'Cliquez pour choisir',
            });
        }

        this.placeChoice.setChoices(async () => {
            try {
                const items = await fetch('/api/admin/place/all');
                return items.json();
            } catch (err) {
                console.error(err);
            }
        }, 'id', 'title', true);
    }

    run() {
        if (!this.container) {
            console.error("The container cannot be null.");
            return;
        }

        this.container.addEventListener('click', (e) => {
            this.addMarker(e)
        });

        document.querySelectorAll('.marker').forEach(element => {
            element.addEventListener('click', (e) => {
                e.preventDefault();
                this.openMarkerModal(e);
            });
        });
    }

    addMarker(e) {
        const width = (e.width !== undefined) ? e.width : 1
        this.positionY = (((e.offsetY) * 100) / this.coordinates.height).toFixed(2);
        this.positionX = (((e.pageX - width - this.coordinates.offsetLeft)  * 100) / this.coordinates.width).toFixed(2);

        this.newPosition = document.createElement('div');
        this.newPosition.classList = ('marker fas fa-map-marker');
        this.newPosition.style.top = `${this.positionY}%`;
        this.newPosition.style.left = `${this.positionX}%`;

        const positionYInput = document.querySelector('#positionY');
        const positionXInput = document.querySelector('#positionX');
        positionYInput.value = this.positionY;
        positionXInput.value = this.positionX;

        positionYInput.addEventListener('input', (e) => {
            this.positionY = positionYInput.value;
            this.newPosition.style.top = `${this.positionY}%`;
        }, { signal })

        positionXInput.addEventListener('input', (e) => {
            this.positionX = positionXInput.value;
            this.newPosition.style.left = `${this.positionX}%`;
        }, { signal })
        
        this.container.parentElement.appendChild(this.newPosition);
        document.querySelector('#title').value = "";
        document.querySelector('#description').value = "";

        const btnCancelled = document.querySelectorAll('.btn-cancel');
        btnCancelled.forEach(button => {
            button.addEventListener('click', (e) => {
                this.newPosition.remove();
            }, { signal });
        })

        this.editModal.show();
    }

    async saveMarker(form) {
        // récupérer les données du formulaire
        const data = {};

        form.querySelectorAll('input, textarea').forEach(input => {
            data[input.id] = input.value;
        });

        // ajouter le lieu si un lieu a été choisi.
        const option = this.placeElement.querySelector('option')
        if (option !== null) {
            data.placeId = parseInt(option.value);
            data.placeName = option.innerHTML;
        } else {
            data.placeId = null;
            data.placeName = null;
        }
    
        data.points = [this.positionY, this.positionX];

        // Générer l'élément avec les informations
        this.newPosition.dataset.title=data.title;
        this.newPosition.dataset.description=data.description;
        this.newPosition.dataset.placeId=data.placeId;
        this.newPosition.dataset.placeName=data.placeName;
        this.newPosition.dataset.marker=data.marker;
        this.newPosition.style.color = data.color;
        this.newPosition.setAttribute('data-bs-toggle', 'tooltip');
        this.newPosition.title = data.title;
        this.newPosition.classList = ('marker ' + data.marker);

        // faire la requête et ajouter l'élément sur la carte
        try {
            const mapId = this.container.dataset.map;            
            const response = await fetch(`/admin/api/map/${mapId}/append`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json;charset=utf-8'},
                body: JSON.stringify(data)
            })

            if (response.ok) {
                new bootstrap.Tooltip(this.newPosition);
                this.newPosition.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openMarkerModal(e)
                });

                const position = await response.json();
                this.newPosition.dataset.id = position.id;
    
                toastify('success', "La position a été ajoutée");
                this.editModal.hide();
                controller.abort();
                location.reload()
            } else {
                toastify('error', "Le formulaire n'était pas valide.");
            }

        } catch (error) {
            toastify('error', "Le formulaire n'était pas valide.");
        }
    }

    openMarkerModal(e) {
        const elementId = e.target.dataset.id;
        const editLink = `/admin/map-position/${elementId}/edit`;

        document.querySelector('#advanced-edit-btn').href = editLink;
        const placeContainer = document.querySelector('#place-linked');
        let place = '';

        if (e.target.dataset.placeid && e.target.dataset.placename) {
            place = `<a href="/admin/place/${e.target.dataset.placeid}/show">${e.target.dataset.placename}</a>`;
        } else {
            place = '<span class="fst-italic">aucun lieu</span>';
        }

        placeContainer.innerHTML = `
            <span class="fw-bold">Lieu associé :</span> ${place}
            <a href="${editLink}" id="advanced-edit-btn" data-bs-toggle="tooltip" title="Modifier">
                <i class="fas fa-pencil-alt fa-fw" aria-hidden="true"></i>
            </a>
        `

        const topInput =  document.querySelector('#positionYEdit');
        const leftInput =  document.querySelector('#positionXEdit');
        topInput.value = parseFloat(e.target.style.top).toFixed(2);
        leftInput.value = parseFloat(e.target.style.left).toFixed(2);

        topInput.addEventListener('input', () => {
            e.target.style.top = `${topInput.value}%`;
        }, { signal })

        leftInput.addEventListener('input', () => {
            e.target.style.left = `${leftInput.value}%`;
        }, { signal });

        const titleInput = document.querySelector('#titleEdit');
        titleInput.value = e.target.dataset.title;

        const descriptionInput = document.querySelector('#descriptionEdit');
        descriptionInput.value = e.target.dataset.description;

        descriptionInput.addEventListener('input', () => {
            e.target.dataset.description = descriptionInput.value;
        }, { signal });

        const markerInput = document.querySelector('#markerEdit');
        markerInput.value = e.target.dataset.marker;

        titleInput.addEventListener('input', () => {
            e.target.dataset.title = titleInput.value;
            e.target.title = titleInput.value;
            new bootstrap.Tooltip(e.target);
        }, { signal });

        this.showModal.show();

        document.querySelector('#edit-btn').addEventListener('click', async (e) => {
            e.preventDefault();
            let error = false;

            const btnIcon = document.querySelector('#edit-btn i');
            btnIcon.classList = 'fas fa-spinner fa-spin fa-fw';

            if (!titleInput.checkValidity()) {
                titleInput.classList.add('is-invalid');
                error = true;
            } else {
                titleInput.classList.remove('is-invalid');
            }

            if (!markerInput.checkValidity()) {
                markerInput.classList.add('is-invalid');
                error = true;
            } else {
                markerInput.classList.remove('is-invalid');
            }

            if (!descriptionInput.checkValidity()) {
                descriptionInput.classList.add('is-invalid');
                error = true;
            } else {
                descriptionInput.classList.remove('is-invalid');
            }

            if (!descriptionInput.checkValidity()) {
                descriptionInput.classList.add('is-invalid');
                error = true;
            } else {
                descriptionInput.classList.remove('is-invalid');
            }

            if (!topInput.checkValidity()) {
                topInput.classList.add('is-invalid');
                error = true;
            } else {
                topInput.classList.remove('is-invalid');
            }

            if (!leftInput.checkValidity()) {
                leftInput.classList.add('is-invalid');
                error = true;
            } else {
                leftInput.classList.remove('is-invalid');
            }

            if (error === true) {
                return;
            }
            
            const data = {
                title: titleInput.value,
                description: descriptionInput.value,
                marker: markerInput.value,
                points: [topInput.value, leftInput.value]
            }
            
            const response = await fetch(`/admin/api/map-position/${elementId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json;charset=utf-8'},
                body: JSON.stringify(data)
            });

            btnIcon.classList = 'fas fa-save fa-fw';

            if (response.ok) {
                this.showModal.hide();
                controller.abort();
                location.reload();
            } else {
                toastify('error', "Le formulaire n'était pas valide.");
            }

        })

        document.querySelector('#delete-btn').addEventListener('click', async (e) => {
            e.preventDefault();
            const response = await fetch(`/admin/api/map-position/${elementId}/delete`, {
                method: 'DELETE'
            });

            if (response.ok) {
                controller.abort();
                location.reload();
            } else {
                toastify('error', "La suppression a échoué.");
            }
        })
    }
}

const marker = new MapMarker('#map');
marker.run();

// form validation
const btnIcon = document.querySelector('#save-btn i');
const form = document.querySelector('.needs-validation');

form.addEventListener('submit', async function (event) {
    event.preventDefault()
    event.stopPropagation() 
    btnIcon.classList = 'fas fa-spinner fa-spin fa-fw';

    if (!form.checkValidity()) {
        form.classList.add('was-validated')
    } else {
        await marker.saveMarker(form);
        form.classList.remove('was-validated')
    }

    btnIcon.classList = 'fas fa-save fa-fw';
}, false)

dragElement(document.getElementById("showModal"));
dragElement(document.getElementById("editModal"));