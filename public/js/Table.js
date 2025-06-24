import { EventEmitter } from "./EventEmitter.js";

/**
 * Class representing of a HTML table
 */
export class Table extends EventEmitter {
    /**
     * Initialize the table
     *
     * @param {number} rows the number of rows in the table
     * @param {number} cols the number of rows in the table
     * @param {string} containerId 
     */
    constructor(rows, cols, containerId) {
        super();
        this.rows = rows;
        this.cols = cols;
        this.container = document.getElementById(containerId);
        this.cells = [];
        this.ready = this.loadAndRender();
    }

    /**
     * loadAndRender
     */
    async loadAndRender() {
      const res = await fetch(this.container.dataset.apiUrl + "/init-board", {method: "GET", headers: {}});
      const data = await res.json();
      this.generateTable(data);
    }

    /**
     * Get Api Url
     *
     * @returns {string} apiUrl
     */
    getTableApiUrl() {
      return this.container.dataset.apiUrl;
    }

    /**
     * Create html table from json input data
     * 
     * @param {JSON} jsonData 
     */
    generateTable(jsonData) {
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
        this.container.appendChild(table);
    }

    /**
     * Getting a cell from the position
     * 
     * @param {number} row 
     * @param {number} col 
     * @returns {string | null}
     */
    getCell(row, col) {
        if (this.isValidPosition(row, col)) {
          return this.cells[row][col];
        }
        return null;
    }

    /**
     * Checking the position of cell, if valid it returns true
     * 
     * @param {number} row 
     * @param {number} col 
     * @returns {boolean}
     */
    isValidPosition(row, col) {
        return row >= 0 && row < this.rows && col >= 0 && col < this.cols;
    }
}