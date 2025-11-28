function sortTable(table, columnIndex) {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const header = table.querySelectorAll("th")[columnIndex];

    const currentDirection = header.getAttribute("data-sort-direction") || "asc";
    const newDirection = currentDirection === "asc" ? "desc" : "asc";

    table.querySelectorAll("th").forEach(th => {
        th.removeAttribute("data-sort-direction");
        th.classList.remove("sorted-asc", "sorted-desc");
    });

    header.setAttribute("data-sort-direction", newDirection);
    header.classList.add(newDirection === "asc" ? "sorted-asc" : "sorted-desc");

    rows.sort((a, b) => {
        let aValue = a.cells[columnIndex].textContent.trim();
        let bValue = b.cells[columnIndex].textContent.trim();

        const aNum = parseFloat(aValue.replace(/[€,\s]/g, '').replace(',', '.'));
        const bNum = parseFloat(bValue.replace(/[€,\s]/g, '').replace(',', '.'));

        let result;
        if (!isNaN(aNum) && !isNaN(bNum)) {
            result = aNum - bNum;
        } else {
            result = aValue.localeCompare(bValue, 'et', { sensitivity: 'base' });
        }

        return newDirection === "asc" ? result : -result;
    });

    rows.forEach(row => tbody.appendChild(row));
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".data-table th").forEach((th, index) => {
        th.style.cursor = "pointer";
        th.style.userSelect = "none";
        th.title = "Kliki sortimiseks";

        th.addEventListener("click", function () {
            const table = this.closest("table");
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            sortTable(table, columnIndex);
        });
    });
});