export type UserRole = {
  Admin: 0,
  Manager: 1,
  Waiter: 2,
  Cook: 3,
  Customer: 4,
}

export interface User {
  id: number;
  username: string;
  email: string;
  firstName: string;
  lastName: string;
  phoneNumber: string;
  role: UserRole;
  restaurantId?: number;
  isActive: boolean;
  createdAt: string;
  lastLoginAt?: string;
}

export interface LoginDto {
  username: string;
  password: string;
}

export interface RegisterDto {
  username: string;
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  phoneNumber: string;
  restaurantId?: number;
}

export interface AuthResponse {
  token: string;
  refreshToken: string;
  user: User;
}

export type OrderStatus = {
  New: 0,
  SentToKitchen: 1,
  InProgress: 2,
  Ready: 3,
  Served: 4,
  Completed: 5,
  Cancelled: 6,
}

export interface OrderItem {
  id: number;
  orderId: number;
  menuItemId: number;
  quantity: number;
  unitPrice: number;
  totalPrice: number;
  specialRequirements?: string;
}

export interface Order {
  id: number;
  tableId: number;
  restaurantId: number;
  assignedWaiterId?: number;
  assignedCookId?: number;
  status: OrderStatus;
  specialRequirements: string;
  customerName: string;
  createdAt: string;
  sentToKitchenAt?: string;
  readyAt?: string;
  servedAt?: string;
  completedAt?: string;
  orderItems: OrderItem[];
}

export interface MenuItem {
  id: number;
  restaurantId: number;
  name: string;
  description: string;
  price: number;
  category: string;
  imageUrl?: string;
  isAvailable: boolean;
  preparationTime: number;
}

export interface Table {
  id: number;
  restaurantId: number;
  tableNumber: string;
  capacity: number;
  isOccupied: boolean;
  currentOrderId?: number;
}

export interface Restaurant {
  id: number;
  name: string;
  address: string;
  phoneNumber: string;
  email: string;
  isActive: boolean;
}

export interface Notification {
  id: number;
  orderId: number;
  message: string;
  createdAt: string;
  isRead: boolean;
}
