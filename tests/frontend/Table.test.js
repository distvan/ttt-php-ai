/**
 * @jest-environment jsdom
 */
import { jest } from '@jest/globals';
import { Table } from '../../public/js/Table.js';
import { isValidURL } from './utils/validators.js';
import { screen, within } from '@testing-library/dom';

describe("Game table class functions testing", () => {
    let tableObj;

    beforeEach(() => {
        global.fetch = jest.fn(() =>
            Promise.resolve({
                json: () => Promise.resolve([["", "", ""],["", "", ""],["", "", ""]])
            })
        );
        document.body.innerHTML = '<div data-api-url="http://localhost:8080" id="tableContainer"></div>';
        tableObj = new Table(3, 3, 'tableContainer');
    });

    it("should create a table inside the container", async () => {
        await tableObj.ready;
        expect(isValidURL(tableObj.getTableApiUrl())).toBe(true);
        const table = screen.getByRole('table');
        const rows = within(table).getAllByRole('row');
        expect(rows).toHaveLength(3);
        rows.forEach(row => {
            const cells = within(row).getAllByRole('cell');
            expect(cells).toHaveLength(3);
            const hasEmpty = cells.some(cell => cell.textContent.trim() === '');
            expect(hasEmpty).toBe(true);
        });
    });

    it("should be a valid position in the table", () => {
        let cell = tableObj.getCell(1, 1);
        expect(cell).toHaveProperty('tagName', 'TD');
    });

    it("should not be a valid position in the table", () => {
        let cell = tableObj.getCell(5, 5);
        expect(cell).toBeNull();
    });
})