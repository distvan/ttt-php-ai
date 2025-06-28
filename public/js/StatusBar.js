export class StatusBar {
    constructor() {
        this.statusBar = document.getElementById('statusBar');
        this.overlay = document.getElementById('overlay');
    }

    showLoading() {
        this.statusBar.style.display = 'block';
        this.overlay.style.display = 'block';
    }

    hideLoading() {
        this.statusBar.style.display = 'none';
        this.overlay.style.display = 'none';
    }
}