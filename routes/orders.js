const express = require('express');
const OrderService = require('../services/orderService');

const router = express.Router();
const orderService = new OrderService();

router.get('/', (req, res) => {
    res.json(orderService.getAll());
});

router.get('/:id', (req, res) => {
    res.json(orderService.getById(req.params.id));
});

router.post('/', (req, res) => {
    const order = orderService.create(req.body);
    res.json(order);
});

router.put('/:id', (req, res) => {
    res.json(orderService.update(req.params.id, req.body));
});

router.post('/:id/kitchen', (req, res) => {
    res.json(orderService.notifyKitchen(req.params.id));
});

router.post('/:id/ready', (req, res) => {
    res.json(orderService.markReady(req.params.id));
});

router.post('/:id/served', (req, res) => {
    res.json(orderService.markServed(req.params.id));
});

router.post('/:id/invoice', (req, res) => {
    res.json(orderService.generateInvoice(req.params.id));
});

router.post('/:id/archive', (req, res) => {
    res.json(orderService.archive(req.params.id));
});

module.exports = router;
