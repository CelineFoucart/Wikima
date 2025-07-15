import { Offcanvas } from 'bootstrap';

document.addEventListener("DOMContentLoaded", function(){
    const myOffcanvas = document.getElementById('offcanvasList');
    const bsOffcanvas = new Offcanvas(myOffcanvas);

    document.querySelectorAll('.offcanvas-btn').forEach(element => {
        element.addEventListener('click',function (e){
            e.preventDefault();
            e.stopPropagation();
            bsOffcanvas.toggle();
        });
    });
});