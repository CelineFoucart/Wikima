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
 * Initiate the slug generation action event.
 */
export function slugAction() {
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
