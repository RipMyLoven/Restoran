const path = require('path');
const DataManager = require('./dataManager');

class UserService {
    constructor() {
        this.usersManager = new DataManager(path.join(__dirname, '..', 'data', 'users.json'));
    }

    create(user) {
        const users = this.usersManager.read() || { users: [] };
        const newUser = {
            id: `user_${Date.now()}`,
            ...user,
            created: new Date().toISOString()
        };
        users.users.push(newUser);
        this.usersManager.write(users);
        return newUser;
    }
}

module.exports = UserService;
