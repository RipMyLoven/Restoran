const express = require('express');
const UserService = require('../services/userService');

const router = express.Router();
const userService = new UserService();

router.get('/', (req, res) => {
    res.json(userService.getAll());
});

router.post('/', (req, res) => {
    res.json(userService.create(req.body));
});

router.delete('/:id', (req, res) => {
    res.json(userService.delete(req.params.id));
});

module.exports = router;
