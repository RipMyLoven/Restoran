const path = require('path');
const DataManager = require('./dataManager');

class UserService {
    constructor() {
        this.usersManager = new DataManager(path.join(__dirname, '..', 'data', 'users.json'));
    }

    getAll() {
        return this.usersManager.read() || { users: [] };
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

    delete(userId) {
        const users = this.usersManager.read() || { users: [] };
        const index = users.users.findIndex(u => u.id === userId);
        
        if (index === -1) {
            return { error: 'user not found' };
        }
        
        users.users.splice(index, 1);
        this.usersManager.write(users);
        return { success: true };
    }
}

module.exports = UserService;
