function filterTable(tableId, searchValue) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector("tbody");
    const rows = tbody.querySelectorAll("tr");

    searchValue = searchValue.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchValue)) {
        } else {
        }
    });
}

const tableSortState = {};

function sortTableByHeader(tableId, columnIndex) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    if (!tableSortState[tableId]) {
        tableSortState[tableId] = {};
    }

    const currentSort = tableSortState[tableId][columnIndex];
    const isDesc = currentSort === 'asc';
    tableSortState[tableId][columnIndex] = isDesc ? 'desc' : 'asc';

    table.querySelectorAll(".sort-indicator").forEach(indicator => {
        indicator.textContent = "";
    });

    const currentHeader = table.querySelector(`thead tr th:nth-child(${columnIndex + 1}) .sort-indicator`);
    if (currentHeader) {
        currentHeader.textContent = isDesc ? " ↓" : " ↑";
    }

    rows.sort((a, b) => {
        let aValue = a.cells[columnIndex].textContent.trim();
        let bValue = b.cells[columnIndex].textContent.trim();

        const aSort = a.cells[columnIndex].getAttribute("data-sort");
        const bSort = b.cells[columnIndex].getAttribute("data-sort");

        if (aSort && bSort) {
            aValue = parseFloat(aSort);
            bValue = parseFloat(bSort);
        } else {
            const aClean = aValue.replace(/[€L,\s]/g, "");
            const bClean = bValue.replace(/[€L,\s]/g, "");

            const aNum = parseFloat(aClean);
            const bNum = parseFloat(bClean);

            if (!isNaN(aNum) && !isNaN(bNum)) {
                aValue = aNum;
                bValue = bNum;
            }
        }

        let result;
        if (typeof aValue === "number" && typeof bValue === "number") {
            result = aValue - bValue;
        } else {
            result = aValue.toString().localeCompare(bValue.toString(), 'et');
        }

        return isDesc ? -result : result;
    });

    rows.forEach(row => tbody.appendChild(row));
}

function sortTable(tableId, columnIndex) {
    sortTableByHeader(tableId, parseInt(columnIndex));
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("th[onclick]").forEach(th => {
        th.title = "Kliki sortimiseks";
    });
});