<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8" doctype-public="html"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <meta charset="UTF-8"/>
                <title>Restoran Data</title>
                <link rel="stylesheet" href="css/styles.css"/>
            </head>
            <body class="data-view">
                <nav class="main-nav">
                    <div class="nav-container">
                        <a href="?format=html" class="nav-brand">Restoran</a>
                        <div class="nav-links">
                            <a href="?format=html" class="active">Andmed</a>
                            <a href="?format=json" target="_blank">JSON</a>
                        </div>
                    </div>
                </nav>
                
                <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                    <h1>Restorani Andmed</h1>
                    
                    <div class="menu-section">
                        <h2>Toidud</h2>
                        
                        <div class="table-controls">
                            <input type="text" class="search-input" id="food-search" placeholder="Otsi toitu..." onkeyup="filterTable('food-table', this.value)"/>
                        </div>
                        
                        <table id="food-table">
                            <thead>
                                <tr>
                                    <th onclick="sortTable('food-table', '0')">Nimetus <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('food-table', '1')">Liik <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('food-table', '2')">Hind <span class="sort-indicator"></span></th>
                                    <th>Koostis</th>
                                    <th onclick="sortTable('food-table', '4')">Kalorid <span class="sort-indicator"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="restoran/menyy/tooted/toit">
                                    <tr>
                                        <td><xsl:value-of select="nimetus"/></td>
                                        <td><xsl:value-of select="@liik"/></td>
                                        <td data-sort="{@hind}">€<xsl:value-of select="@hind"/></td>
                                        <td><xsl:value-of select="koostis"/></td>
                                        <td data-sort="{kalorsus}"><xsl:value-of select="kalorsus"/></td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="menu-section">
                        <h2>Joogid</h2>
                        
                        <div class="table-controls">
                            <input type="text" class="search-input" id="drinks-search" placeholder="Otsi jooki..." onkeyup="filterTable('drinks-table', this.value)"/>
                        </div>
                        
                        <table id="drinks-table">
                            <thead>
                                <tr>
                                    <th onclick="sortTable('drinks-table', '0')">Nimetus <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('drinks-table', '1')">Liik <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('drinks-table', '2')">Maht <span class="sort-indicator"></span></th>
                                    <th>Tootja</th>
                                    <th>Alkohol %</th>
                                    <th onclick="sortTable('drinks-table', '5')">Hind <span class="sort-indicator"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="restoran/menyy/tooted/jook">
                                    <tr>
                                        <td><xsl:value-of select="nimetus"/></td>
                                        <td><xsl:value-of select="@liik"/></td>
                                        <td data-sort="{@maht}"><xsl:value-of select="@maht"/>L</td>
                                        <td><xsl:value-of select="tootja"/></td>
                                        <td><xsl:value-of select="alkoholiProtsent"/></td>
                                        <td data-sort="{hind}">€<xsl:value-of select="hind"/></td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="menu-section">
                        <h2>Teenindus</h2>
                        
                        <h4>Lauad</h4>
                        <table id="tables-table">
                            <thead>
                                <tr>
                                    <th onclick="sortTable('tables-table', '0')">Kohtade arv <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('tables-table', '1')">Staatus <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('tables-table', '2')">Asukoht <span class="sort-indicator"></span></th>
                                    <th>Reserveeritud</th>
                                    <th>Erinoued</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="restoran/teenindus/lauad/laud">
                                    <tr>
                                        <td data-sort="{@kohtade_arv}"><xsl:value-of select="@kohtade_arv"/></td>
                                        <td><xsl:value-of select="@staatus"/></td>
                                        <td><xsl:value-of select="@asukoht"/></td>
                                        <td><xsl:value-of select="reserveeritud"/></td>
                                        <td><xsl:value-of select="erinoued"/></td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                        
                        <h4>Teenindajad</h4>
                        <table id="staff-table">
                            <thead>
                                <tr>
                                    <th onclick="sortTable('staff-table', '0')">Nimi <span class="sort-indicator"></span></th>
                                    <th>Telefon</th>
                                    <th onclick="sortTable('staff-table', '2')">Staatus <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('staff-table', '3')">Kogemus <span class="sort-indicator"></span></th>
                                    <th>Tööaeg</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="restoran/teenindus/teenindajad/teenindaja">
                                    <tr>
                                        <td><xsl:value-of select="nimi"/></td>
                                        <td><xsl:value-of select="telefon"/></td>
                                        <td><xsl:value-of select="@staatus"/></td>
                                        <td data-sort="{@kogemus}"><xsl:value-of select="@kogemus"/> aastad</td>
                                        <td><xsl:value-of select="tööaeg"/></td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="menu-section">
                        <h2>Tellimused</h2>
                        
                        <table id="orders-table">
                            <thead>
                                <tr>
                                    <th onclick="sortTable('orders-table', '0')">Laud <span class="sort-indicator"></span></th>
                                    <th>Teenindaja</th>
                                    <th onclick="sortTable('orders-table', '2')">Klient <span class="sort-indicator"></span></th>
                                    <th>Telefon</th>
                                    <th onclick="sortTable('orders-table', '4')">Aeg <span class="sort-indicator"></span></th>
                                    <th onclick="sortTable('orders-table', '5')">Staatus <span class="sort-indicator"></span></th>
                                    <th>Prioriteet</th>
                                    <th onclick="sortTable('orders-table', '7')">Summa <span class="sort-indicator"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="restoran/tellimused/tellimus">
                                    <tr>
                                        <td data-sort="{@laud_id}"><xsl:value-of select="@laud_id"/></td>
                                        <td><xsl:value-of select="@teenindaja_id"/></td>
                                        <td><xsl:value-of select="kliendi_info/nimi"/></td>
                                        <td><xsl:value-of select="kliendi_info/telefon"/></td>
                                        <td><xsl:value-of select="tellimuse_aeg"/></td>
                                        <td><strong><xsl:value-of select="@staatus"/></strong></td>
                                        <td><xsl:value-of select="@prioriteet"/></td>
                                        <td data-sort="{kogusumma}">€<xsl:value-of select="kogusumma"/></td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <script src="script.js"></script>
                <script>
                    function filterTable(tableId, searchValue) {
                        console.log("filterTable called:", tableId, searchValue);
                        const table = document.getElementById(tableId);
                        const tbody = table.querySelector("tbody");
                        const rows = tbody.querySelectorAll("tr");
                        
                        searchValue = searchValue.toLowerCase();
                        
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (text.includes(searchValue)) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        });
                    }
                    
                    const tableSortState = {};
                    
                    function sortTable(tableId, columnIndex) {
                        console.log("sortTable called:", tableId, columnIndex);
                        sortTableByHeader(tableId, parseInt(columnIndex));
                    }
                    
                    function sortTableByHeader(tableId, columnIndex) {
                        console.log("sortTableByHeader called:", tableId, columnIndex);
                        const table = document.getElementById(tableId);
                        const tbody = table.querySelector("tbody");
                        const rows = Array.from(tbody.querySelectorAll("tr"));
                        
                        if (!tableSortState[tableId]) {
                            tableSortState[tableId] = {};
                        }
                        
                        const currentSort = tableSortState[tableId][columnIndex];
                        const isDesc = currentSort === "asc";
                        tableSortState[tableId][columnIndex] = isDesc ? "desc" : "asc";
                        
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
                            
                            if (aSort &amp;&amp; bSort) {
                                aValue = parseFloat(aSort);
                                bValue = parseFloat(bSort);
                            } else {
                                const aClean = aValue.replace(/[€L,\s]/g, "");
                                const bClean = bValue.replace(/[€L,\s]/g, "");
                                
                                const aNum = parseFloat(aClean);
                                const bNum = parseFloat(bClean);
                                
                                if (!isNaN(aNum) &amp;&amp; !isNaN(bNum)) {
                                    aValue = aNum;
                                    bValue = bNum;
                                }
                            }
                            
                            let result;
                            if (typeof aValue === "number" &amp;&amp; typeof bValue === "number") {
                                result = aValue - bValue;
                            } else {
                                result = aValue.toString().localeCompare(bValue.toString(), "et");
                            }
                            
                            return isDesc ? -result : result;
                        });
                        
                        rows.forEach(row => tbody.appendChild(row));
                    }
                    
                    document.addEventListener("DOMContentLoaded", function () {
                        console.log("DOM loaded, setting up tables...");
                        document.querySelectorAll("th[onclick]").forEach(th => {
                            console.log("Found sortable header:", th);
                            th.style.cursor = "pointer";
                            th.title = "Kliki sortimiseks";
                        });
                    });
                </script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
