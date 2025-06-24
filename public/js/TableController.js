/**
 * Class representing table controller
 */
export class TableController {
    /**
     * Constructor
     *
     * @param {Table} table 
     * @param {Notifier} notifier 
     */
    constructor(table, notifier) {
        this.table = table;
        this.notifier = notifier;
        table.on('cellClicked', (cell) => this.playerMove(cell));
        table.on('aiMove', (data) => this.aiMove(data));
    }

    /**
     * Handle Player moving on the table
     * and ask AI 
     * 
     * @param {HTMLElement} tdElement 
     * @returns 
     */
    playerMove(tdElement) {
        if(tdElement.textContent !== '') {
            return;
        }
        tdElement.textContent='X';
        this.notifier.show('It is AI turn now.');
        
        const movingData = new URLSearchParams();
        movingData.append("rowIndex", tdElement.dataset.row);
        movingData.append("colIndex", tdElement.dataset.col);

        fetch(this.table.getTableApiUrl() + "/mark", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: movingData.toString()
        })
        .then(response => response.json())
        .then(json => {
            this.table.emit('aiMove', json.result);
            if (json.winner !== "" && json.winner !== null) {
                this.notifier.show('The ' + json.winner + ' player win!');
            }
        });
    }

    /**
     * Put the AI move into the table using cell position
     * 
     * @param {json} data 
     * example: {col:1, row:1}
     */
    aiMove(data) {
        const td = this.table.getCell(parseInt(data.col), parseInt(data.row));
        td.textContent='O';
    }

    /**
     * Highlight the winner cells in the table
     * 
     * @param {json} cells 
     * for example: [{col:0,row:0}, {col:0,row:1}, {col:0,row:2}]
     */
    highlightWinnerCells(cells) {
        cells.forEach(element => {
            const td = this.table.getCell(parseInt(element.col), parseInt(element.row));
            td.style.backgroundColor = 'red';
        });
    }
}