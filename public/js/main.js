import { Table } from './Table.js';
import { TableController } from './TableController.js';
import { Notifier } from './Notifier.js';
import { StatusBar } from './StatusBar.js';

document.addEventListener('DOMContentLoaded', () => {
    new TableController(
        new Table(3, 3, 'tableContainer'), 
        new Notifier('notification-container'),
        new StatusBar()
    );
});