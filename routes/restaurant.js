const express = require('express');
const RestaurantService = require('../services/restaurantService');

const router = express.Router();
const restaurantService = new RestaurantService();

router.get('/info', (req, res) => {
    res.json(restaurantService.getInfo());
});

router.put('/info', (req, res) => {
    res.json(restaurantService.updateInfo(req.body));
});

router.get('/menu', (req, res) => {
    const kategooria = req.query.kategooria;
    const maxHind = req.query.maxHind ? parseFloat(req.query.maxHind) : null;
    const vegan = req.query.vegan === 'true';
    res.json(restaurantService.getMenu(kategooria, maxHind, vegan));
});

router.get('/tables', (req, res) => {
    res.json(restaurantService.getTables());
});

router.put('/tables/:id', (req, res) => {
    res.json(restaurantService.updateTable(req.params.id, req.body));
});

router.get('/stats', (req, res) => {
    res.json(restaurantService.getStats());
});

router.get('/staff', (req, res) => {
    res.json(restaurantService.getStaff());
});

module.exports = router;
