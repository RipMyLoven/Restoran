const fs = require('fs');
const path = require('path');

class DataManager {
    constructor(filePath) {
        this.filePath = filePath;
    }

    read() {
        try {
            if (fs.existsSync(this.filePath)) {
                const data = fs.readFileSync(this.filePath, 'utf8');
                return JSON.parse(data);
            }
            return null;
        } catch (error) {
            return null;
        }
    }

    write(data) {
        try {
            fs.writeFileSync(this.filePath, JSON.stringify(data, null, 2));
            return { success: true };
        } catch (error) {
            return { error: error.message };
        }
    }
}

module.exports = DataManager;
