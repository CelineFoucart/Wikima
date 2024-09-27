/**
 * Generate the fullname and slug value for characters in edition or creation form.
 */
export function characterAction() {
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
