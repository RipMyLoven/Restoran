const express = require('express');
const fs = require('fs');
const path = require('path');
const swaggerUi = require('swagger-ui-express');
const swaggerDocument = require('./swagger.json');

const restaurantRoutes = require('./routes/restaurant');
const orderRoutes = require('./routes/orders');
const userRoutes = require('./routes/users');
const archiveRoutes = require('./routes/archive');

const app = express();
const port = 3000;

app.use(express.json());
app.use(express.static('public'));
app.use('/view', express.static(path.join(__dirname, 'public', 'view')));
app.use('/swagger', swaggerUi.serve, swaggerUi.setup(swaggerDocument));

app.use('/api', restaurantRoutes);
app.use('/api/orders', orderRoutes);
app.use('/api/users', userRoutes);
app.use('/api/archive', archiveRoutes);
app.get('/', (_req, res) => res.redirect('/swagger'));
app.get('/view/', (_req, res) => res.redirect('/view/index.html'));
app.get('/view/manag', (_req, res) => { res.redirect('/view/manag.html'); });
app.get('/view/manag', (req, res) => { res.redirect('/view/manag.html'); });

app.listen(port, () => {
    console.log(`server running on http://localhost:${port}`);
});
