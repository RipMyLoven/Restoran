const express = require('express');
const UserService = require('../services/userService');

const router = express.Router();
const userService = new UserService();

router.post('/', (req, res) => {
    res.json(userService.create(req.body));
});

module.exports = router;
