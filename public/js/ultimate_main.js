import { UltimateTable } from './UltimateTable.js';
import { UltimateTableController } from './UltimateTableController.js';
import { Notifier } from './Notifier.js';
import { StatusBar } from './StatusBar.js';

document.addEventListener('DOMContentLoaded', () => {
    const TABLE_DIMENSION = 3;
    let notifier = new Notifier('notification-container');
    let statusBar = new StatusBar();
    for (let count=1; count<=9; count++) {
        new UltimateTableController(new UltimateTable(TABLE_DIMENSION, TABLE_DIMENSION, 'tableContainer', `table-${count}`), notifier, statusBar);
    }
});
