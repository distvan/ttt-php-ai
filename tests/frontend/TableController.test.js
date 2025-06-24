/**
 * @jest-environment jsdom
 */
import { jest } from '@jest/globals';
import { Table } from '../../public/js/Table.js';
import { TableController } from "../../public/js/TableController.js";
import { Notifier } from "../../public/js/Notifier.js";
import { screen, within } from '@testing-library/dom';
import userEvent from '@testing-library/user-event';

describe('Player move', () => {
    let tableObj;
    let tableController;

    beforeEach(() => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve([["", "", ""],["", "", ""],["", "", ""]])
            })
        );
        document.body.innerHTML = '<div data-api-url="http://localhost:8080" id="tableContainer"></div>';
        document.body.innerHTML += '<div id="notification-container"></div>';
        tableObj = new Table(3, 3, 'tableContainer');
        tableController = new TableController(tableObj, new Notifier('notification-container'))
    });

    it('clicks on a cell and marks as X and get AI answer', async () => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve({
                        "success": true,
                        "result" : {"col":1, "row":1},
                        "winner": null,
                        "gameover": false
                })
            })
        );
        
        await tableObj.ready;
        const td = document.querySelector('td[data-row="2"][data-col="1"]');
        await userEvent.click(td);
        const player = tableObj.getCell(2, 1);
        
        expect(player).toBeInstanceOf(HTMLTableCellElement);
        expect(player.tagName).toBe('TD');
        expect(player.textContent.trim()).toBe('X');

        const playerAI = tableObj.getCell(1, 1);
        expect(playerAI).toBeInstanceOf(HTMLTableCellElement);
        expect(playerAI.tagName).toBe('TD');
        expect(playerAI.textContent.trim()).toBe('O');

        expect(global.fetch).toHaveBeenCalled();
    });

    it('shows who is the winner', async () => {

    });
})