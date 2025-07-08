import { Notifier } from "./Notifier.js";
import { StatusBar } from "./StatusBar.js";
import { TableController } from "./TableController.js";

/**
 * Class representing an ultimate table controller
 */
export class UltimateTableController extends TableController
{
    /**
     * Constructor
     *
     * @param {Table} table
     * @param {Notifier} notifier
     * @param {StatusBar} statusBar
     */
    constructor(table, notifier, statusBar) {
        super(table, notifier, statusBar);
        this.table = table;
    }

    /**
     * It is called when the board has a winner
     *
     * @param {string} winner
     */
    onWin(winner) {
        super.onWin(winner);
        this.table.showOverlay(winner);
    }

    /**
     * It is called when the board has no winner but the board is full
     */
    onDraw() {
        super.onDraw();
        this.table.showOverlay('=');
    }
}
