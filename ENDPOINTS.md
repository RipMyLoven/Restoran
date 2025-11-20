# Complete API Endpoints List

## Total: 41 endpoints

### Restaurants (5 endpoints)
- GET    /api/restaurants
- GET    /api/restaurants/{id}
- POST   /api/restaurants
- PUT    /api/restaurants/{id}
- DELETE /api/restaurants/{id}

### Tables (5 endpoints)
- GET    /api/tables
- GET    /api/tables/{id}
- POST   /api/tables
- PUT    /api/tables/{id}
- DELETE /api/tables/{id}

### MenuItems (5 endpoints)
- GET    /api/menuitems
- GET    /api/menuitems/{id}
- POST   /api/menuitems
- PUT    /api/menuitems/{id}
- DELETE /api/menuitems/{id}

### Orders (11 endpoints)
- GET    /api/orders
- GET    /api/orders/{id}
- POST   /api/orders
- PUT    /api/orders/{id}
- DELETE /api/orders/{id}
- POST   /api/orders/{id}/send-to-kitchen
- POST   /api/orders/{id}/mark-ready
- POST   /api/orders/{id}/mark-served
- POST   /api/orders/{id}/complete
- GET    /api/orders/kitchen
- GET    /api/orders/ready

### Bills (9 endpoints)
- GET    /api/bills
- GET    /api/bills/{id}
- POST   /api/bills
- PUT    /api/bills/{id}
- DELETE /api/bills/{id}
- GET    /api/bills/order/{orderId}
- POST   /api/bills/generate/{orderId}
- POST   /api/bills/{id}/pay

### Statistics (6 endpoints)
- GET    /api/statistics/orders/archived
- GET    /api/statistics/orders/by-date
- GET    /api/statistics/revenue/today
- GET    /api/statistics/popular-items
- GET    /api/statistics/tables/usage
- GET    /api/statistics/orders/average-time
