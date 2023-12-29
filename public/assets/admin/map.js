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
        this.positionY = ((e.pageY - this.coordinates.offsetTop) * 100) / this.coordinates.height;
        this.positionX = ((e.pageX - e.width - this.coordinates.offsetLeft)  * 100) / this.coordinates.width;
        document.querySelector('#title').value = "";
        document.querySelector('#description').value = "";
        this.editModal.show();
    }

    async saveMarker(form) {
        // récupérer les données du formulaire
        const data = {};

        form.querySelectorAll('input, textarea').forEach(input => {
            data[input.id] = input.value;
        });

        data.placeId = null;
        data.placeName = null;
        data.points = [this.positionY, this.positionX];

        // Générer l'élément avec les informations
        const newPosition = document.createElement('div');
        newPosition.dataset.title=data.title;
        newPosition.dataset.description=data.description;
        newPosition.dataset.placeId=data.placeId;
        newPosition.dataset.placeName=data.placeName;
        newPosition.dataset.marker=data.marker;
        newPosition.style.color = data.color;
        newPosition.setAttribute('data-bs-toggle', 'tooltip');
        newPosition.title = data.title;
        newPosition.classList = ('marker ' + data.marker);
        newPosition.style.top = `${this.positionY}%`;
        newPosition.style.left = `${this.positionX}%`;

        // faire la requête et ajouter l'élément sur la carte
        try {
            const mapId = this.container.dataset.map;            
            const response = await fetch(`/admin/api/map/${mapId}/append`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json;charset=utf-8'},
                body: JSON.stringify(data)
            })

            if (response.ok) {
                this.container.parentElement.appendChild(newPosition);
                new bootstrap.Tooltip(newPosition);
                newPosition.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openMarkerModal(e)
                });
    
                toastify('success', "La position a été ajoutée");
                this.editModal.hide();
                location.reload()
            } else {
                toastify('error', "Le formulaire n'était pas valide.");
            }

        } catch (error) {
            toastify('error', "Le formulaire n'était pas valide.");
        }

        
    }

    openMarkerModal(e) {
        document.querySelector('#pointer-icon').classList = e.target.dataset.icon;
        document.querySelector('#pointer-title').innerHTML = e.target.dataset.title;

        const description = e.target.dataset.description;
        if (description !== null && description.length > 0) {
            document.querySelector('#pointer-description').innerHTML = description;
        } else {
            document.querySelector('#pointer-description').innerHTML = "Aucune description";
        }
        this.showModal.show();
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