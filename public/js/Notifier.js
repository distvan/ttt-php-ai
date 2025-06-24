/**
 * Class Representing a Notifier
 */
export class Notifier {
    /**
    * Constructor
    *
    * @param {string} containerId 
    */
    constructor(containerId = 'notification-container') {
        this.container = document.getElementById(containerId);
    }

    /**
    * Show user notification
    * 
    * @param {string} message 
    * @param {int} duration 
    */
    show(message, duration = 3000) {
        const el = document.createElement('div');
        el.className = 'notification';
        el.textContent = message;

        this.container.appendChild(el);

        // Trigger animation
        requestAnimationFrame(() => {
            el.classList.add('show');
        });

        // Remove after timeout
        setTimeout(() => {
            el.classList.remove('show');
            el.addEventListener('transitionend', () => el.remove());
        }, duration);
    }
}