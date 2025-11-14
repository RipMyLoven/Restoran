const path = require('path');
const DataManager = require('./dataManager');

class RestaurantService {
    constructor() {
        this.dataManager = new DataManager(path.join(__dirname, '..', 'data', 'restoran_data.json'));
    }

    getInfo() {
        const data = this.dataManager.read();
        return data?.restoran_info || { error: 'info not found' };
    }

    updateInfo(info) {
        const data = this.dataManager.read() || {};
        data.restoran_info = { ...data.restoran_info, ...info };
        return this.dataManager.write(data);
    }

    getMenu(kategooria = null, maxHind = null, vegan = false) {
        const data = this.dataManager.read();
        let menu = data?.menüü || {};
        
        if (kategooria && menu[kategooria]) {
            menu = { [kategooria]: menu[kategooria] };
        }

        if (maxHind || vegan) {
            Object.keys(menu).forEach(cat => {
                if (Array.isArray(menu[cat])) {
                    menu[cat] = menu[cat].filter(item => {
                        if (maxHind && item.hind > maxHind) return false;
                        if (vegan && item.dieet && !item.dieet.vegan) return false;
                        return true;
                    });
                }
            });
        }

        return menu;
    }

    getTables() {
        const data = this.dataManager.read();
        return data?.laudade_info || [];
    }

    updateTable(tableId, updates) {
        const data = this.dataManager.read() || {};
        const tables = data.laudade_info || [];
        const index = tables.findIndex(t => t.id === tableId);
        
        if (index === -1) return { error: 'table not found' };
        
        tables[index] = { ...tables[index], ...updates };
        data.laudade_info = tables;
        return this.dataManager.write(data);
    }

    getStats() {
        const data = this.dataManager.read();
        return data?.statistika || { error: 'stats not found' };
    }

    getStaff() {
        const data = this.dataManager.read();
        return data?.teenindajad || [];
    }
}

module.exports = RestaurantService;
