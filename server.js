const express = require('express');
const fs = require('fs');
const path = require('path');
const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('./swagger.json');

const app = express();
const port = 3000;
//fix
app.use(express.json());
app.use(express.static('public'));
app.use('/swagger', swaggerUi.serve, swaggerUi.setup(swaggerDocument));

class RestoranManager {
    constructor() {
        this.jsonFile = path.join(__dirname, 'data', 'restoran_data.json');
    }

    readJsonData() {
        try {
            if (fs.existsSync(this.jsonFile)) {
                const data = fs.readFileSync(this.jsonFile, 'utf8');
                return JSON.parse(data);
            }
            return { error: 'data not found' };
        } catch (error) {
            return { error: error.message };
        }
    }

    getInfo() {
        const data = this.readJsonData();
        return data.restoran_info || { error: 'info not found' };
    }

    getMenu(kategooria = null, maxHind = null, vegan = false) {
        const data = this.readJsonData();
        let menu = data.menüü || {};
        
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
        const data = this.readJsonData();
        return data.laudade_info || { error: 'tables not found' };
    }

    getStats() {
        const data = this.readJsonData();
        return data.statistika || { error: 'stats not found' };
    }

    getStaff() {
        const data = this.readJsonData();
        return data.teenindajad || { error: 'staff not found' };
    }
}

const restoran = new RestoranManager();

app.get('/api/info', (req, res) => {
    res.json(restoran.getInfo());
});

app.get('/api/menu', (req, res) => {
    const kategooria = req.query.kategooria;
    const maxHind = req.query.maxHind ? parseFloat(req.query.maxHind) : null;
    const vegan = req.query.vegan === 'true';
    res.json(restoran.getMenu(kategooria, maxHind, vegan));
});

app.get('/api/laudad', (req, res) => {
    res.json(restoran.getTables());
});

app.get('/api/statistika', (req, res) => {
    res.json(restoran.getStats());
});

app.get('/api/teenindajad', (req, res) => {
    res.json(restoran.getStaff());
});

app.get('/', (req, res) => {
    res.redirect('/api-docs');
});

app.listen(port, () => {
    console.log(`server running on http://localhost:${port}`);
});
