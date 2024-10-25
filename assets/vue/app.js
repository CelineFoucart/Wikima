import { registerVueControllerComponents } from '@symfony/ux-vue';
import '@root/bootstrap.js';
import { createPinia } from 'pinia';
import "toastify-js/src/toastify.css";
import 'floating-vue/dist/style.css';
import FloatingVue from 'floating-vue';

registerVueControllerComponents(require.context('./views', true, /\.vue$/));
document.addEventListener('vue:before-mount', (event) => {
    const {
        componentName, // The Vue component's name
        component, // The resolved Vue component
        props, // The props that will be injected to the component
        app, // The Vue application instance
    } = event.detail;

    const pinia = createPinia();

    app.use(pinia).use(FloatingVue);
});