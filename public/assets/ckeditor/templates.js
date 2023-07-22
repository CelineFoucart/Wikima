CKEDITOR.addTemplates("default",{
    imagesPath:CKEDITOR.getUrl(CKEDITOR.plugins.getPath("templates")+"templates/images/"),
    templates:[
        {
            title: 'Carte Bootstrap simple',
            image: 'card.png',
            description: 'Une carte bootstrap simple avec une image en haut.',
            html: '<div class="card">' +
                '<img src=" " class="card-img-top" alt=" ">' +
                '<div class="card-body">' +
                '<h5 class="card-title">Title</h5>' +
                '<p class="card-text">Text</p>' +
                '</div>' +
                '</div>'
        },
        {
            title: 'Carte Bootstrap avec liste',
            image: 'card-list.png',
            description: 'Une carte bootstrap avec une liste.',
            html: `<div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Title</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">item</li>
                    <li class="list-group-item">item</li>
                    <li class="list-group-item">item</li>
                </ul>
            </div>`
        },
        {
            title: 'Template pour une alerte bootstrap',
            image: 'alert.png',
            description: 'Un bloc alerte bootstrap.',
            html: `<div class="alert alert-success" role="alert">
                Message
            </div>`
        },
        {
            title: 'Grille bootstrap deux colonne',
            image: 'grid.png',
            description: 'Un template bootstrap avec deux colonnes.',
            html: `<div class="row align-items-start">
                <div class="col-md-6">
                    One
                </div>
                <div class="col-md-6">
                    two
                </div>
                <div class="col-md-6">
                    three
                </div>
                <div class="col-md-6">
                    four
                </div>
            </div>`
        },
        {
            title: 'Table striped bootstrap',
            image: 'table.png',
            description: 'Un tableau bootstrap avec ligne à alternance de couleur.',
            html: `<table class="table table-striped">
                <thead>
                    <tr>
                        <th>Heading</th>
                        <th>Heading</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>cell</td>
                        <td>cell</td>
                    </tr>
                    <tr>
                        <td>cell</td>
                        <td>cell</td>
                    </tr>
                </tbody>
            </table>`
        },
        {
            title: 'Figure',
            image: 'figure.png',
            description: 'Un bloc figure avec image et légende.',
            html: `<figure class="figure">
                <img src=" " class="figure-img img-fluid rounded" alt=" ">
                <figcaption class="figure-caption">A caption</figcaption>
            </figure>`
        },
        {
            title: 'Accordéon',
            image: 'accordion.png',
            description: 'Un accordéon bootstrap avec des éléments repliables.',
            html: `<div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Title
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            text
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Title
                            </button>
                        </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            text
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Title
                            </button>
                        </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            text
                        </div>
                    </div>
                </div>
            </div>`
        },
        {
            title: 'Un carousel',
            image: 'carousel.png',
            description: 'Un carousel bootstrap avec ligne à alternance de couleur.',
            html: `<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="..." class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="..." class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="..." class="d-block w-100" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>`
        },
        {
            title: 'Dropdown bootstrap',
            image: 'dropdown.png',
            description: 'Un élément de type Dropdown qui affiche ou cache une liste de liens.',
            html: `<div class="dropdown">
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown link
                </a>
            
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </div>`
        },
        {
            title: 'Barre de progression bootstrap',
            image: 'progress.png',
            description: 'Une barre de progression bootstrap.',
            html: `<div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
            </div>`
        },
        {
            title: 'Bloc flex bootstrap',
            image: 'flex.png',
            description: 'Un template pour un design flexbox.',
            html: `<div class="d-flex">
                <div>1</div>
                <div>2</div>
                <div>3</div>
            </div>`
        },
        {
            title: 'Bloc définition bootstrap',
            image: 'definition.png',
            description: 'Un template pour une série de définitions.',
            html: `<dl>
                <dt>Term</dt>
                <dd>Definition</dd>
                <dt>Term</dt>
                <dd>Definition</dd>
                <dd>Definition</dd>
                <dt>Term</dt>
                <dd>Definition</dd>
            </dl>`
        },
        {
            title:"Image et Titre",
            image:"template1.gif",
            description:"Une image principale avec un titre et un texte qui l'entoure.",
            html:'\x3ch3\x3e\x3cimg src\x3d" " alt\x3d"" style\x3d"margin-right: 10px" height\x3d"100" width\x3d"100" align\x3d"left" /\x3eType the title here\x3c/h3\x3e\x3cp\x3eType the text here\x3c/p\x3e'
        },
        {
            title:"Template à deux colonnes",
            image:"template2.gif",
            description:"Un template qui definit deux colonnes, chacune avec un titre et du texte.",
            html:'\x3ctable cellspacing\x3d"0" cellpadding\x3d"0" style\x3d"width:100%" border\x3d"0"\x3e\x3ctr\x3e\x3ctd style\x3d"width:50%"\x3e\x3ch3\x3eTitle 1\x3c/h3\x3e\x3c/td\x3e\x3ctd\x3e\x3c/td\x3e\x3ctd style\x3d"width:50%"\x3e\x3ch3\x3eTitle 2\x3c/h3\x3e\x3c/td\x3e\x3c/tr\x3e\x3ctr\x3e\x3ctd\x3eText 1\x3c/td\x3e\x3ctd\x3e\x3c/td\x3e\x3ctd\x3eText 2\x3c/td\x3e\x3c/tr\x3e\x3c/table\x3e\x3cp\x3eMore text goes here.\x3c/p\x3e'
        },
        {
            title:"Texte et Table",
            image:"template3.gif",
            description:"Un titre avec du texte et un tableau",
            html:'\x3cdiv style\x3d"width: 80%"\x3e\x3ch3\x3eTitle goes here\x3c/h3\x3e\x3ctable style\x3d"width:150px;float: right" cellspacing\x3d"0" cellpadding\x3d"0" border\x3d"1"\x3e\x3ccaption style\x3d"border:solid 1px black"\x3e\x3cstrong\x3eTable title\x3c/strong\x3e\x3c/caption\x3e\x3ctr\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3c/tr\x3e\x3ctr\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3c/tr\x3e\x3ctr\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3ctd\x3e\x26nbsp;\x3c/td\x3e\x3c/tr\x3e\x3c/table\x3e\x3cp\x3eType the text here\x3c/p\x3e\x3c/div\x3e'
        },
    ]
});