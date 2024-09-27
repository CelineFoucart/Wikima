import "@melloware/coloris/dist/coloris.css";
import Coloris from "@melloware/coloris";
import './color.css';
Coloris.init();

document.querySelectorAll('.color-input').forEach(element => {
    Coloris({
        el: element,
        swatches: [
            '#7952b3', // violet
            '#d946ef', // fuchsia
            '#d63384', // pink
            '#dc3545', // red
            '#92400e', // brown
            '#fd7e14', // orange
            '#ffc107', // yellow
            '#198754', // green
            '#3c8dbc', // blue
            '#93c5fd', // blue 300
            '#6c757d', // grey
            '#212529', // dark
        ]
    });

    Coloris.setInstance(element, { theme: 'polaroid' });
})
