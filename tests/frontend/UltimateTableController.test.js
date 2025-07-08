/**
 * @jest-environment jsdom
 */
import { expect, jest } from '@jest/globals';
import { UltimateTable } from '../../public/js/UltimateTable.js';
import { UltimateTableController } from "../../public/js/UltimateTableController.js";
import { Notifier } from "../../public/js/Notifier.js";
import { StatusBar } from "../../public/js/StatusBar.js";
import userEvent from '@testing-library/user-event';

describe('Showing overlay in ultimate table block', () => {
    let tableObj;
    let tableController;
    let statusBar;

    beforeEach(() => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve([["", "", ""],["", "", ""],["", "", ""]])
            })
        );
        document.body.innerHTML = '<div id="statusBar">Waiting for AI response, please wait...</div>';
        document.body.innerHTML += '<div id="overlay"></div>';
        document.body.innerHTML += '<div data-api-url="http://localhost:8080" id="tableContainer"></div>';
        document.body.innerHTML += '<div id="notification-container"></div>';
        statusBar = new StatusBar();
        tableObj = new UltimateTable(3, 3, 'tableContainer', 'table-1');
        tableController = new UltimateTableController(tableObj, new Notifier('notification-container'), statusBar);
    });

    it('shows X overlay with red color in the block when player win', async () => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve({
                        "success": true,
                        "result" : {"row":0, "col":0},
                        "winner": "X",
                        "gameover": true
                })
            })
        );
        await tableObj.ready;
        const container = document.getElementById('tableContainer');
        const block = container.querySelector('.inner-block');
        const table = block.querySelector('table');
        let td = table.querySelector('td[data-row="1"][data-col="0"]');
        await userEvent.click(td);
        const overlay = block.querySelector('.overlay');
        expect(overlay.getAttribute('data-value')).toBe(overlay.textContent);
        expect(overlay.textContent).toBe('X');
    });
});
