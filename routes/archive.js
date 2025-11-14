const express = require('express');
const OrderService = require('../services/orderService');

const router = express.Router();
const orderService = new OrderService();

router.get('/', (req, res) => {
    res.json(orderService.getArchive());
});

module.exports = router;
