import axiosInstance from './axios';
import type { Order, OrderStatus } from '../types';

export const ordersApi = {
  getAll: async (): Promise<Order[]> => {
    const response = await axiosInstance.get('/orders');
    return response.data;
  },

  getById: async (id: number): Promise<Order> => {
    const response = await axiosInstance.get(`/orders/${id}`);
    return response.data;
  },

  create: async (orderData: Partial<Order>): Promise<Order> => {
    const response = await axiosInstance.post('/orders', orderData);
    return response.data;
  },

  update: async (id: number, orderData: Partial<Order>): Promise<Order> => {
    const response = await axiosInstance.put(`/orders/${id}`, orderData);
    return response.data;
  },

  updateStatus: async (id: number, status: OrderStatus): Promise<Order> => {
    const response = await axiosInstance.patch(`/orders/${id}/status`, { status });
    return response.data;
  },

  delete: async (id: number): Promise<void> => {
    await axiosInstance.delete(`/orders/${id}`);
  },
};
