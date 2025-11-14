const API_BASE = 'http://localhost:3000/api';

function showSection(section) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(section).style.display = 'block';
    
    if (section === 'staff') loadStaff();
    if (section === 'restaurant') loadRestaurantSettings();
    if (section === 'archive') loadArchive();
    if (section === 'stats') loadStats();
}

async function loadStaff() {
    const res = await fetch(`${API_BASE}/users`);
    const data = await res.json();
    const list = document.getElementById('staffList');
    
    if (!data.users || data.users.length === 0) {
        list.innerHTML = '<p>No staff</p>';
        return;
    }
    
    const roleColors = {
        waiter: '#3498db',
        kitchen: '#e67e22',
        admin: '#e74c3c'
    };
    
    list.innerHTML = '<div class="staff-grid">' +
        data.users.map(user => `
            <div class="staff-card" style="border-left: 4px solid ${roleColors[user.role]}">
                <h4>${user.name}</h4>
                <p><strong>Role:</strong> ${user.role}</p>
                <p><strong>ID:</strong> ${user.id}</p>
                <p><strong>Created:</strong> ${new Date(user.created).toLocaleDateString()}</p>
                <button onclick="deleteStaff('${user.id}')">Delete</button>
            </div>
        `).join('') +
        '</div>';
}

async function deleteStaff(userId) {
    if (!confirm('Delete this staff member?')) return;
    
    const res = await fetch(`${API_BASE}/users/${userId}`, {
        method: 'DELETE'
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Staff deleted');
        loadStaff();
    }
}

document.getElementById('staffForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const staff = {
        name: document.getElementById('staffName').value,
        role: document.getElementById('staffRole').value
    };
    
    const res = await fetch(`${API_BASE}/users`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(staff)
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Staff added: ' + result.name);
        loadStaff();
        e.target.reset();
    }
});

async function loadRestaurantSettings() {
    const res = await fetch(`${API_BASE}/info`);
    const info = await res.json();
    
    if (!info.error) {
        document.getElementById('restName').value = info.nimi || '';
        document.getElementById('restAddress').value = info.aadress || '';
        document.getElementById('restPhone').value = info.telefon || '';
        document.getElementById('restDescription').value = info.kirjeldus || '';
    }
}

document.getElementById('restaurantForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const updates = {
        nimi: document.getElementById('restName').value,
        aadress: document.getElementById('restAddress').value,
        telefon: document.getElementById('restPhone').value,
        kirjeldus: document.getElementById('restDescription').value
    };
    
    const res = await fetch(`${API_BASE}/info`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updates)
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Settings updated');
    }
});

async function loadArchive() {
    const res = await fetch(`${API_BASE}/archive`);
    const data = await res.json();
    const list = document.getElementById('archiveList');
    
    if (!data.orders || data.orders.length === 0) {
        list.innerHTML = '<p>No archived orders</p>';
        return;
    }
    
    list.innerHTML = '<table><thead><tr><th>Order ID</th><th>Table</th><th>Items</th><th>Total</th><th>Archived</th></tr></thead><tbody>' +
        data.orders.map(order => `
            <tr>
                <td>${order.id}</td>
                <td>${order.tableId}</td>
                <td>${order.items ? order.items.length : 0}</td>
                <td>${order.invoice ? order.invoice.total + '€' : 'N/A'}</td>
                <td>${new Date(order.archivedAt).toLocaleString()}</td>
            </tr>
        `).join('') +
        '</tbody></table>';
}

async function loadStats() {
    const res = await fetch(`${API_BASE}/stats`);
    const stats = await res.json();
    const content = document.getElementById('statsContent');
    
    if (stats.error) {
        content.innerHTML = '<p>No statistics available</p>';
        return;
    }

    content.innerHTML = `
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Active Orders</h3>
                <p class="stat-number">${stats.aktiivsed_tellimused || 0}</p>
            </div>
            <div class="stat-card">
                <h3>Completed Orders</h3>
                <p class="stat-number">${stats.lõpetatud_tellimused || 0}</p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p class="stat-number">${stats.kokku_tellimusi || 0}</p>
            </div>
            <div class="stat-card">
                <h3>Daily Revenue</h3>
                <p class="stat-number">${stats.päeva_käive || 0}€</p>
            </div>
            <div class="stat-card">
                <h3>Average Order</h3>
                <p class="stat-number">${stats.keskmine_tellimuse_summa || 0}€</p>
            </div>
        </div>
        
        <h3>Popular Dishes</h3>
        <div class="popular-dishes">
            ${Object.entries(stats.populaarsemad_toidud || {}).map(([dish, count]) => `
                <div class="dish-stat">
                    <span>${dish}</span>
                    <span>${count} orders</span>
                </div>
            `).join('')}
        </div>
    `;
}

showSection('staff');
