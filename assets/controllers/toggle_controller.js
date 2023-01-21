import { Controller } from '@hotwired/stimulus';

/*
 * toggle_controller is a Stimulus controller.
 *
 * Any element with a data-controller="toggle" attribute will cause
 * this controller to be executed.
 */
export default class extends Controller {

    static targets = ["action", "content"];

    toggle() {
        let showIcon = '<i class="bi bi-chevron-down"></i>';
        let hideIcon = '<i class="bi bi-chevron-up"></i>';

        if (this.actionTarget.innerHTML === showIcon) {
            this.actionTarget.innerHTML = hideIcon;
            this.contentTarget.style.display = "block";
        } else {
            this.actionTarget.innerHTML = showIcon;
            this.contentTarget.style.display = "none";
        }

    }
}
