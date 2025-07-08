/**
 * @jest-environment jsdom
 */
import { jest } from '@jest/globals';
import { UltimateTable } from '../../public/js/UltimateTable.js';
import { isValidURL } from './utils/validators.js';
import { screen, within } from '@testing-library/dom';

describe("Ultimate game table class functions testing", () => {
    let tableObj;
    let tableId = 'table-1';

    beforeEach(() => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve([["", "", ""],["", "", ""],["", "", ""]])
            })
        );
        document.body.innerHTML = '<div data-api-url="http://localhost:8080" id="tableContainer"></div>';
        tableObj = new UltimateTable(3, 3, 'tableContainer', tableId);
    });

    it("should create an ultimatetable inside the container", async () => {
        await tableObj.ready;

        const container = document.getElementById('tableContainer');
        const block = container.querySelector('.inner-block');
        const table = block.querySelector('table');
        const rows = within(table).getAllByRole('row');

        expect(isValidURL(tableObj.getTableApiUrl())).toBe(true);
        expect(block.id).toBe(tableId);
        expect(rows).toHaveLength(3);
        rows.forEach(row => {
            const cells = within(row).getAllByRole('cell');
            expect(cells).toHaveLength(3);
            const hasEmpty = cells.some(cell => cell.textContent.trim() === '');
            expect(hasEmpty).toBe(true);
        });
    });

    it("should be show an overlay when the table is full", async () => {
        await tableObj.ready;
        tableObj.showOverlay('O');
        const container = document.getElementById('tableContainer');
        const block = container.querySelector('.inner-block');
        const overlay = block.querySelector('.overlay');
        expect(overlay.getAttribute('data-value')).toBe(overlay.textContent);
        expect(overlay.textContent).toBe('O');
    });
});
