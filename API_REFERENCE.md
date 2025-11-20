# API Endpoints Quick Reference

## Base URL
`https://localhost:5001/api`

---

## 📍 Restaurants

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/restaurants` | Get all restaurants |
| GET | `/restaurants/{id}` | Get restaurant by ID |
| POST | `/restaurants` | Create new restaurant |
| PUT | `/restaurants/{id}` | Update restaurant |
| DELETE | `/restaurants/{id}` | Delete restaurant |

**Example POST body:**
```json
{
  "name": "Italian Bistro",
  "tableCount": 15,
  "allergyTags": "gluten,lactose,nuts",
  "dietTags": "vegan,vegetarian,halal"
}
```

---

## 🪑 Tables

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/tables` | Get all tables |
| GET | `/tables/{id}` | Get table by ID |
| POST | `/tables` | Create new table |
| PUT | `/tables/{id}` | Update table |
| DELETE | `/tables/{id}` | Delete table |

**Table Status:**
- 0 = Available
- 1 = Occupied
- 2 = Reserved

---

## 🍕 Menu Items

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/menuitems` | Get all menu items |
| GET | `/menuitems/{id}` | Get menu item by ID |
| POST | `/menuitems` | Create new menu item |
| PUT | `/menuitems/{id}` | Update menu item |
| DELETE | `/menuitems/{id}` | Delete menu item |

**Example POST body:**
```json
{
  "name": "Margherita Pizza",
  "description": "Classic Italian pizza",
  "price": 12.99,
  "category": "Main Course",
  "allergens": "gluten,lactose",
  "dietaryInfo": "vegetarian",
  "restaurantId": 1,
  "isAvailable": true
}
```

---

## 📋 Orders

| Method | Endpoint | Description | User |
|--------|----------|-------------|------|
| GET | `/orders` | Get all orders | All |
| GET | `/orders/{id}` | Get order by ID | All |
| POST | `/orders` | Create new order | Waiter |
| PUT | `/orders/{id}` | Update order | Waiter |
| DELETE | `/orders/{id}` | Delete order | Manager |
| POST | `/orders/{id}/send-to-kitchen` | Send to kitchen | Waiter |
| POST | `/orders/{id}/mark-ready` | Mark as ready | Chef |
| POST | `/orders/{id}/mark-served` | Mark as served | Waiter |
| POST | `/orders/{id}/complete` | Complete order | Waiter |
| GET | `/orders/kitchen` | Get kitchen orders | Chef |
| GET | `/orders/ready` | Get ready orders | Waiter |

**Order Status Flow:**
```
New → SentToKitchen → InProgress → Ready → Served → Completed
                                            ↓
                                        Cancelled
```

**Example POST body:**
```json
{
  "tableId": 1,
  "restaurantId": 1,
  "customerName": "John Doe",
  "specialRequirements": "No onions",
  "orderItems": [
    {
      "menuItemId": 1,
      "quantity": 2,
      "priceAtOrder": 12.99,
      "specialInstructions": "Extra cheese"
    }
  ]
}
```

---

## 💰 Bills

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/bills` | Get all bills |
| GET | `/bills/{id}` | Get bill by ID |
| GET | `/bills/order/{orderId}` | Get bill by order ID |
| POST | `/bills/generate/{orderId}` | Generate bill for order |
| POST | `/bills/{id}/pay` | Pay bill |
| PUT | `/bills/{id}` | Update bill |
| DELETE | `/bills/{id}` | Delete bill |

**Generate Bill:**
- Automatically calculates subtotal, tax (20%), and total

**Pay Bill:**
```json
{
  "paymentMethod": "card"
}
```

---

## 📊 Statistics (Archive)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/statistics/orders/archived` | Get all completed/cancelled orders |
| GET | `/statistics/orders/by-date?startDate=2024-01-01&endDate=2024-12-31` | Get orders by date range |
| GET | `/statistics/revenue/today` | Get today revenue |
| GET | `/statistics/popular-items?top=10` | Get top popular items |
| GET | `/statistics/tables/usage` | Get table usage statistics |
| GET | `/statistics/orders/average-time` | Get average order processing time |

**Example Response (Revenue Today):**
```json
{
  "date": "2024-11-20",
  "totalOrders": 25,
  "totalRevenue": 523.75,
  "totalTax": 104.75,
  "totalSubtotal": 419.00,
  "averageOrderValue": 20.95
}
```

---

## 🔄 Typical Workflow

### 1. Setup Restaurant
```bash
POST /api/restaurants
```

### 2. Add Menu Items
```bash
POST /api/menuitems
```

### 3. Create Order (Waiter)
```bash
POST /api/orders
```

### 4. Send to Kitchen (Waiter)
```bash
POST /api/orders/1/send-to-kitchen
```

### 5. Check Kitchen Orders (Chef)
```bash
GET /api/orders/kitchen
```

### 6. Mark Ready (Chef)
```bash
POST /api/orders/1/mark-ready
```

### 7. Check Ready Orders (Waiter)
```bash
GET /api/orders/ready
```

### 8. Serve Order (Waiter)
```bash
POST /api/orders/1/mark-served
```

### 9. Generate Bill (Waiter)
```bash
POST /api/bills/generate/1
```

### 10. Pay Bill (Customer)
```bash
POST /api/bills/1/pay
```

### 11. View Statistics (Manager)
```bash
GET /api/statistics/revenue/today
GET /api/statistics/popular-items
```

---

## 🧪 Testing

Use **API_Tests.http** file for quick testing with VS Code REST Client extension.

Or use **Swagger UI** at `https://localhost:5001`

---

## 📌 Important Notes

- All timestamps are in UTC
- Tax rate is fixed at 20%
- Tables are auto-created when restaurant is created
- Order automatically completes when bill is paid
- Completed orders are archived for statistics
