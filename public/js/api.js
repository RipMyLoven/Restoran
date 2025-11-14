const API_BASE = 'http://localhost:3000/api';

async function fetchInfo() {
    const res = await fetch(`${API_BASE}/info`);
    return await res.json();
}

async function fetchMenu(kategooria = null, maxHind = null, vegan = false) {
    let url = `${API_BASE}/menu?`;
    if (kategooria) url += `kategooria=${kategooria}&`;
    if (maxHind) url += `maxHind=${maxHind}&`;
    if (vegan) url += `vegan=true&`;
    
    const res = await fetch(url);
    return await res.json();
}

async function fetchTables() {
    const res = await fetch(`${API_BASE}/laudad`);
    return await res.json();
}

async function fetchStats() {
    const res = await fetch(`${API_BASE}/statistika`);
    return await res.json();
}

async function fetchStaff() {
    const res = await fetch(`${API_BASE}/teenindajad`);
    return await res.json();
}
