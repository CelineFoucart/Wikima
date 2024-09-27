/**
 * Change the type of a password input to hide or show the password.
 * 
 * @param {String} targetId 
 */
export function togglePassword(targetId) {
    const input = document.querySelector(`#${targetId}`);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}