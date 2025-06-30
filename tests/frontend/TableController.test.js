/**
 * @jest-environment jsdom
 */
import { expect, jest } from '@jest/globals';
import { Table } from '../../public/js/Table.js';
import { TableController } from "../../public/js/TableController.js";
import { Notifier } from "../../public/js/Notifier.js";
import { StatusBar } from "../../public/js/StatusBar.js";
import userEvent from '@testing-library/user-event';

describe('Player move', () => {
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
        tableObj = new Table(3, 3, 'tableContainer');
        tableController = new TableController(tableObj, new Notifier('notification-container'), statusBar);
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
        let td = document.querySelector('td[data-row="1"][data-col="0"]');
        await userEvent.click(td);
        const containerDiv = document.getElementById('notification-container');
        const notification = containerDiv. querySelector('.notification');
        expect(containerDiv).not.toBeNull();
        expect(notification).not.toBeNull();
        expect(notification.textContent).toBe('The X player won!');
        expect(global.fetch).toHaveBeenCalled();
    });

    it('shows the table is full and no winner', async () => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve({
                        "success": true,
                        "result" : {},
                        "winner": "",
                        "gameover": true
                })
            })
        );
        await tableObj.ready;
        let td = document.querySelector('td[data-row="1"][data-col="0"]');
        await userEvent.click(td);
        const containerDiv = document.getElementById('notification-container');
        const notification = containerDiv. querySelector('.notification');
        expect(containerDiv).not.toBeNull();
        expect(notification).not.toBeNull();
        expect(notification.textContent).toBe('The board is full! No winner.');
        expect(global.fetch).toHaveBeenCalled();
    });
})
