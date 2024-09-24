import Toastify from 'toastify-js';

export function createToastify(message, type = 'success')
{
    const background = (type === 'success') ? 'green' : 'red';
    
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: {
            background: background,
            color: 'white'
        },
    }).showToast();
}