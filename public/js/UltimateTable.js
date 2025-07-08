import { Table } from "./Table.js";

/**
 * Class representing an ultimatle table
 */
export class UltimateTable extends Table {

    /**
     * generateTable
     * It creates an ultimate table based on the json input data
     *
     * @param {string} jsonData
     */
    generateTable(jsonData) {
        const block = document.createElement('div');
        block.classList.add('inner-block');
        block.setAttribute('id', this.tableId);
        const table = document.createElement('table');
        jsonData.forEach((rowData, rowIndex) => {
          const tr = document.createElement('tr');
          const rowCells = [];
          rowData.forEach((cellData, colIndex )=> {
            const td = document.createElement("td");
            td.dataset.row = rowIndex;
            td.dataset.col = colIndex;
            td.textContent = cellData;
            rowCells.push(td);
            td.addEventListener('click', () =>  this.emit('cellClicked', td));
            tr.appendChild(td);
          });
          this.cells.push(rowCells);
          table.appendChild(tr);
        });
        block.appendChild(table);
        this.container.appendChild(block);
    }
    /**
     * It shows an overlay with an input value
     * @param {string} value
     */
    showOverlay(value) {
        const block = this.container.querySelector('#' + this.tableId);
        const overlay = document.createElement('div');
        overlay.classList.add('overlay');
        overlay.setAttribute('data-value', value);
        overlay.textContent = value;
        block.appendChild(overlay);
    }
}
