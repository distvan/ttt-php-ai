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
    constructor(table, notifier, statusBar) {
        this.table = table;
        this.notifier = notifier;
        this.statusBar = statusBar
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
        const movingData = new URLSearchParams();
        movingData.append("rowIndex", tdElement.dataset.row);
        movingData.append("colIndex", tdElement.dataset.col);

        this.statusBar.showLoading();
        fetch(this.table.getTableApiUrl() + "/mark?table="+this.table.getTableId(), {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: movingData.toString()
        })
        .then(response => response.json())
        .then(json => {
            this.statusBar.hideLoading();
            if (json.winner !== "" && json.winner !== null && json.gameover) {
                this.table.emit('aiMove', json.result);
                this.onWin(json.winner);
            } else if(!json.success) {
                this.notifier.show('Error: ' + json.result);
            } else if(json.gameover  && (json.winner == "" || json.winner == null)) {
                this.onDraw();
            }
            else {
                this.table.emit('aiMove', json.result);
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
        const td = this.table.getCell(parseInt(data.row), parseInt(data.col));
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
            const td = this.table.getCell(parseInt(element.row), parseInt(element.col));
            td.style.backgroundColor = 'red';
        });
    }

    /**
     * onWin
     * it is called when the board has a winner
     *
     * @param {string} winner
     */
    onWin(winner) {
        this.notifier.show('The ' + winner + ' player won!');
    }

    /**
     * onDraw
     * It is caled when the board has no winner but the board is full
     */
    onDraw() {
        this.notifier.show('The board is full! No winner.');
    }
}
