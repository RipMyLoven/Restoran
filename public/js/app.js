async function loadInfo() {
    const info = await fetchInfo();
    const content = document.getElementById('infoContent');
    
    if (info.error) {
        content.innerHTML = `<p>Error: ${info.error}</p>`;
        return;
    }

    document.getElementById('restaurantName').textContent = info.nimi;
    
    content.innerHTML = `
        <p><strong>Address:</strong> ${info.aadress}</p>
        <p><strong>Phone:</strong> ${info.telefon}</p>
        <p><strong>Description:</strong> ${info.kirjeldus}</p>
        <h3>Opening hours:</h3>
        <ul>
            ${Object.entries(info.avamisajad).map(([day, hours]) => 
                `<li>${day}: ${hours}</li>`
            ).join('')}
        </ul>
    `;
}

async function loadMenu(kategooria = null) {
    const menu = await fetchMenu(kategooria);
    const content = document.getElementById('menuContent');
    
    if (menu.error) {
        content.innerHTML = `<p>Error: ${menu.error}</p>`;
        return;
    }

    let html = '';
    for (const [cat, items] of Object.entries(menu)) {
        html += `<div class="menu-section">`;
        html += `<h3>${cat}</h3>`;
        
        items.forEach(item => {
            html += `<div class="menu-item">`;
            html += `<h4>${item.nimi} <span class="price">${item.hind}€</span></h4>`;
            html += `<p>${item.kirjeldus}</p>`;
            if (item.dieet) {
                const badges = [];
                if (item.dieet.vegan) badges.push('Vegan');
                if (item.dieet.vegetarian) badges.push('Vegetarian');
                if (item.dieet.gluteenivaba) badges.push('Gluten-free');
                if (badges.length > 0) {
                    html += `<small>${badges.join(', ')}</small>`;
                }
            }
            html += `</div>`;
        });
        
        html += `</div>`;
    }
    
    content.innerHTML = html;
}

async function loadTables() {
    const tables = await fetchTables();
    const content = document.getElementById('tablesContent');
    
    if (tables.error) {
        content.innerHTML = `<p>Error: ${tables.error}</p>`;
        return;
    }

    let html = '<table><thead><tr><th>Number</th><th>Seats</th><th>Area</th><th>Status</th></tr></thead><tbody>';
    
    tables.forEach(table => {
        html += `<tr>
            <td>${table.number}</td>
            <td>${table.kohtade_arv}</td>
            <td>${table.ala}</td>
            <td>${table.staatus}</td>
        </tr>`;
    });
    
    html += '</tbody></table>';
    content.innerHTML = html;
}

async function loadStats() {
    const stats = await fetchStats();
    const content = document.getElementById('statsContent');
    
    if (stats.error) {
        content.innerHTML = `<p>Error: ${stats.error}</p>`;
        return;
    }

    content.innerHTML = `
        <p><strong>Active orders:</strong> ${stats.aktiivsed_tellimused}</p>
        <p><strong>Completed orders:</strong> ${stats.lõpetatud_tellimused}</p>
        <p><strong>Total orders:</strong> ${stats.kokku_tellimusi}</p>
        <p><strong>Revenue:</strong> ${stats.päeva_käive}€</p>
        <p><strong>Average order:</strong> ${stats.keskmine_tellimuse_summa}€</p>
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    loadInfo();
    loadMenu();
    loadTables();
    loadStats();
});
