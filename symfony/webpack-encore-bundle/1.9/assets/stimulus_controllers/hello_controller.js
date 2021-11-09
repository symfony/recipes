import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any HTML element with a data-controller="hello" attribute will cause this controller to be executed. Example:
 * <div data-controller="hello"></div>
 * The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        this.element.textContent = 'Hello Stimulus! Edit me in assets/stimulus_controllers/hello_controller.js';
    }
}
