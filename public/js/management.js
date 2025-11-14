const API_BASE = 'http://localhost:3000/api';

let menuData = {};
let orderItems = [];

async function loadMenuData() {
    const res = await fetch(`${API_BASE}/menu`);
    menuData = await res.json();
}

function showSection(section) {
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    document.getElementById(section).style.display = 'block';
    
    if (section === 'orders') {
        loadOrders();
        loadTablesForSelect();
        loadStaffForSelect();
    }
    if (section === 'kitchen') loadKitchenOrders();
    if (section === 'waiter') loadWaiterOrders();
    if (section === 'tables') loadTables();
    if (section === 'users') loadUsers();
}

async function loadTablesForSelect() {
    const res = await fetch(`${API_BASE}/tables`);
    const tables = await res.json();
    const select = document.getElementById('tableId');
    
    select.innerHTML = '<option value="">Select Table</option>' +
        tables.map(t => `<option value="${t.id}">Table ${t.number} (${t.kohtade_arv} seats)</option>`).join('');
}

async function loadStaffForSelect() {
    const res = await fetch(`${API_BASE}/staff`);
    const staff = await res.json();
    const select = document.getElementById('waiterId');
    
    select.innerHTML = '<option value="">Select Waiter</option>' +
        staff.map(s => `<option value="${s.id}">${s.nimi} (${s.amet})</option>`).join('');
}

function addMenuItem() {
    const container = document.getElementById('menuItems');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'menu-item-select';
    
    let allItems = [];
    Object.entries(menuData).forEach(([category, items]) => {
        items.forEach(item => {
            allItems.push({
                id: item.id,
                name: item.nimi,
                price: item.hind,
                category: category
            });
        });
    });
    
    itemDiv.innerHTML = `
        <select class="item-select" onchange="updateItemPrice(this)">
            <option value="">Select Item</option>
            ${allItems.map(item => 
                `<option value="${item.id}" data-price="${item.price}" data-name="${item.name}">
                    ${item.name} - ${item.price}€
                </option>`
            ).join('')}
        </select>
        <input type="number" class="item-quantity" min="1" value="1" placeholder="Qty">
        <button type="button" onclick="this.parentElement.remove()">Remove</button>
    `;
    
    container.appendChild(itemDiv);
}

function updateItemPrice(select) {
    const option = select.options[select.selectedIndex];
    if (option.value) {
        const quantity = select.parentElement.querySelector('.item-quantity');
        quantity.focus();
    }
}

async function loadOrders() {
    const res = await fetch(`${API_BASE}/orders`);
    const data = await res.json();
    const list = document.getElementById('ordersList');
    
    if (!data.orders || data.orders.length === 0) {
        list.innerHTML = '<p>No orders</p>';
        return;
    }
    
    list.innerHTML = data.orders.map(order => `
        <div class="order-item">
            <h4>Order ${order.id}</h4>
            <p>Table: ${order.tableId} | Status: ${order.status}</p>
            <p>Created: ${new Date(order.created).toLocaleString()}</p>
            <button onclick="updateOrder('${order.id}', {status: 'cancelled'})">Cancel</button>
            <button onclick="archiveOrder('${order.id}')">Archive</button>
        </div>
    `).join('');
}

async function loadKitchenOrders() {
    const res = await fetch(`${API_BASE}/orders`);
    const data = await res.json();
    const list = document.getElementById('kitchenOrders');
    
    const kitchenOrders = data.orders.filter(o => 
        o.status === 'sent_to_kitchen' || o.status === 'received'
    );
    
    if (kitchenOrders.length === 0) {
        list.innerHTML = '<p>No orders for kitchen</p>';
        return;
    }
    
    list.innerHTML = kitchenOrders.map(order => `
        <div class="order-item">
            <h4>Order ${order.id}</h4>
            <p>Table: ${order.tableId}</p>
            <p>Items: ${order.items ? order.items.length : 0}</p>
            <button onclick="markReady('${order.id}')">Mark Ready</button>
        </div>
    `).join('');
}

async function loadWaiterOrders() {
    const res = await fetch(`${API_BASE}/orders`);
    const data = await res.json();
    const list = document.getElementById('waiterOrders');
    
    const readyOrders = data.orders.filter(o => o.status === 'ready');
    
    if (readyOrders.length === 0) {
        list.innerHTML = '<p>No orders ready</p>';
        return;
    }
    
    list.innerHTML = readyOrders.map(order => `
        <div class="order-item">
            <h4>Order ${order.id}</h4>
            <p>Table: ${order.tableId}</p>
            <button onclick="markServed('${order.id}')">Mark Served</button>
            <button onclick="generateInvoice('${order.id}')">Generate Invoice</button>
        </div>
    `).join('');
}

async function loadTables() {
    const res = await fetch(`${API_BASE}/tables`);
    const data = await res.json();
    const list = document.getElementById('tablesList');
    
    if (!data || data.length === 0) {
        list.innerHTML = '<p>No tables</p>';
        return;
    }
    
    list.innerHTML = '<table><thead><tr><th>ID</th><th>Number</th><th>Seats</th><th>Area</th><th>Status</th><th>Actions</th></tr></thead><tbody>' +
        data.map(table => `
            <tr>
                <td>${table.id}</td>
                <td>${table.number}</td>
                <td>${table.kohtade_arv}</td>
                <td>${table.ala}</td>
                <td>${table.staatus}</td>
                <td>
                    <button onclick="updateTableStatus('${table.id}', 'vaba')">Free</button>
                    <button onclick="updateTableStatus('${table.id}', 'hõivatud')">Occupied</button>
                </td>
            </tr>
        `).join('') +
        '</tbody></table>';
}

async function loadUsers() {
    const res = await fetch(`${API_BASE}/users`);
    const data = await res.json();
    const list = document.getElementById('usersList');
    
    if (!data.users || data.users.length === 0) {
        list.innerHTML = '<p>No users</p>';
        return;
    }
    
    list.innerHTML = '<table><thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead><tbody>' +
        data.users.map(user => `
            <tr>
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.role}</td>
                <td>${new Date(user.created).toLocaleString()}</td>
                <td><button onclick="deleteUser('${user.id}')">Delete</button></td>
            </tr>
        `).join('') +
        '</tbody></table>';
}

document.getElementById('orderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const tableId = document.getElementById('tableId').value;
    const waiterId = document.getElementById('waiterId').value;
    
    const items = [];
    document.querySelectorAll('.menu-item-select').forEach(div => {
        const select = div.querySelector('.item-select');
        const quantity = div.querySelector('.item-quantity');
        
        if (select.value) {
            const option = select.options[select.selectedIndex];
            items.push({
                itemId: option.value,
                name: option.dataset.name,
                price: parseFloat(option.dataset.price),
                quantity: parseInt(quantity.value)
            });
        }
    });
    
    if (items.length === 0) {
        alert('Add at least one item');
        return;
    }
    
    const order = { tableId, waiterId, items };
    
    const res = await fetch(`${API_BASE}/orders`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(order)
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Order created: ' + result.id);
        await notifyKitchen(result.id);
        loadOrders();
        e.target.reset();
        document.getElementById('menuItems').innerHTML = '';
        orderItems = [];
    }
});

document.getElementById('userForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const user = {
        name: document.getElementById('userName').value,
        role: document.getElementById('userRole').value
    };
    
    const res = await fetch(`${API_BASE}/users`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(user)
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('User created: ' + result.id);
        loadUsers();
        e.target.reset();
    }
});

async function notifyKitchen(orderId) {
    await fetch(`${API_BASE}/orders/${orderId}/kitchen`, { method: 'POST' });
}

async function markReady(orderId) {
    const res = await fetch(`${API_BASE}/orders/${orderId}/ready`, { method: 'POST' });
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Order marked as ready');
        loadKitchenOrders();
    }
}

async function markServed(orderId) {
    const res = await fetch(`${API_BASE}/orders/${orderId}/served`, { method: 'POST' });
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Order marked as served');
        loadWaiterOrders();
    }
}

async function generateInvoice(orderId) {
    const res = await fetch(`${API_BASE}/orders/${orderId}/invoice`, { method: 'POST' });
    const invoice = await res.json();
    
    if (invoice.error) {
        alert('Error: ' + invoice.error);
    } else {
        alert(`Invoice generated\nSubtotal: ${invoice.subtotal}\nTax: ${invoice.tax}\nService: ${invoice.service}\nTotal: ${invoice.total}`);
    }
}

async function archiveOrder(orderId) {
    const res = await fetch(`${API_BASE}/orders/${orderId}/archive`, { method: 'POST' });
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('Order archived');
        loadOrders();
    }
}

async function updateOrder(orderId, updates) {
    const res = await fetch(`${API_BASE}/orders/${orderId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updates)
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        loadOrders();
    }
}

async function updateTableStatus(tableId, status) {
    const res = await fetch(`${API_BASE}/tables/${tableId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ staatus: status })
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        loadTables();
    }
}

async function deleteUser(userId) {
    if (!confirm('Delete this user?')) return;
    
    const res = await fetch(`${API_BASE}/users/${userId}`, {
        method: 'DELETE'
    });
    
    const result = await res.json();
    
    if (result.error) {
        alert('Error: ' + result.error);
    } else {
        alert('User deleted');
        loadUsers();
    }
}

showSection('orders');
loadMenuData();
