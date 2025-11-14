const path = require('path');
const DataManager = require('./dataManager');

class OrderService {
    constructor() {
        this.ordersManager = new DataManager(path.join(__dirname, '..', 'data', 'orders.json'));
        this.archiveManager = new DataManager(path.join(__dirname, '..', 'data', 'archive.json'));
    }

    getAll() {
        return this.ordersManager.read() || { orders: [] };
    }

    getById(orderId) {
        const data = this.getAll();
        return data.orders.find(o => o.id === orderId) || { error: 'order not found' };
    }

    create(order) {
        const orders = this.getAll();
        const newOrder = {
            id: `order_${Date.now()}`,
            status: 'received',
            created: new Date().toISOString(),
            statusHistory: [{ status: 'received', time: new Date().toISOString() }],
            ...order
        };
        orders.orders.push(newOrder);
        this.ordersManager.write(orders);
        return newOrder;
    }

    update(orderId, updates) {
        const orders = this.getAll();
        const index = orders.orders.findIndex(o => o.id === orderId);
        
        if (index === -1) return { error: 'order not found' };
        
        const order = orders.orders[index];
        
        if (updates.status && updates.status !== order.status) {
            order.statusHistory.push({
                status: updates.status,
                time: new Date().toISOString()
            });
        }
        
        orders.orders[index] = { ...order, ...updates };
        this.ordersManager.write(orders);
        return orders.orders[index];
    }

    notifyKitchen(orderId) {
        return this.update(orderId, { 
            status: 'sent_to_kitchen', 
            kitchenNotified: new Date().toISOString() 
        });
    }

    markReady(orderId) {
        return this.update(orderId, { 
            status: 'ready', 
            readyTime: new Date().toISOString() 
        });
    }

    markServed(orderId) {
        return this.update(orderId, { 
            status: 'served', 
            servedTime: new Date().toISOString() 
        });
    }

    generateInvoice(orderId) {
        const order = this.getById(orderId);
        if (order.error) return order;

        const subtotal = order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = subtotal * 0.20;
        const service = subtotal * 0.10;
        const total = subtotal + tax + service;

        const invoice = {
            orderId: order.id,
            tableId: order.tableId,
            items: order.items,
            subtotal: subtotal.toFixed(2),
            tax: tax.toFixed(2),
            service: service.toFixed(2),
            total: total.toFixed(2),
            generated: new Date().toISOString()
        };

        this.update(orderId, { invoice });
        return invoice;
    }

    archive(orderId) {
        const orders = this.getAll();
        const index = orders.orders.findIndex(o => o.id === orderId);
        
        if (index === -1) return { error: 'order not found' };
        
        const order = orders.orders[index];
        orders.orders.splice(index, 1);
        this.ordersManager.write(orders);

        const archive = this.archiveManager.read() || { orders: [] };
        archive.orders.push({
            ...order,
            archivedAt: new Date().toISOString()
        });
        this.archiveManager.write(archive);
        
        return { success: true, order };
    }

    getArchive() {
        return this.archiveManager.read() || { orders: [] };
    }
}

module.exports = OrderService;
