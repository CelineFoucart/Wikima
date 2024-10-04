import { enableBasicEditor } from "./js/basiceditor";
import { enableFullEditor } from "./js/fulleditor";

const forFullEditor = document.querySelectorAll('[data-fulleditor]');
forFullEditor.forEach(element => {
    const headings = element.dataset.fulleditor;
    const withScript = element.dataset.advanced ? true : false;
    enableFullEditor(element.id, headings, withScript);
});

const forBasicEditor = document.querySelectorAll('[data-basiceditor]');
forBasicEditor.forEach(element => {
    enableBasicEditor(element.id);
})
