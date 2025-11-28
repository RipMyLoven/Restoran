<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <html lang="et">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Restoran - Lisa andmeid</title>
            <link rel="stylesheet" href="css/styles.css"/>
            <style>
                main { max-width: 600px; margin: 20px auto; }
                section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 4px; }
                h3 { margin-top: 0; color: #2c3e50; }
                .form-group { margin-bottom: 15px; }
                label { display: block; margin-bottom: 5px; font-weight: bold; }
                input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box; font-size: 14px; }
                textarea { resize: vertical; min-height: 80px; }
                button { padding: 10px 20px; margin-right: 10px; border: none; border-radius: 3px; cursor: pointer; font-size: 14px; font-weight: bold; }
                button[type="submit"] { background: #27ae60; color: white; }
                button[type="submit"]:hover { background: #229954; }
                button[type="reset"] { background: #95a5a6; color: white; }
                button[type="reset"]:hover { background: #7f8c8d; }
            </style>
        </head>
        <body>
            <header>
                <h1>Lisa andmeid</h1>
                <nav>
                    <a href="?format=html">Avaleht</a>
                    <a href="?view=add">Lisa andmeid</a>
                    <a href="?format=json">JSON</a>
                </nav>
            </header>
            
            <main>
                <section>
                    <h3>Lisa toit</h3>
                    <form id="addFoodForm" method="POST" action="restoran.php">
                        <input type="hidden" name="action" value="add_food"/>
                        <div class="form-group">
                            <label for="food_name">Toidu nimetus *</label>
                            <input type="text" id="food_name" name="nimetus" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="food_type">Toidu liik *</label>
                            <select id="food_type" name="liik" required="required">
                                <option value="praad">Praad</option>
                                <option value="supp">Supp</option>
                                <option value="salat">Salat</option>
                                <option value="pasta">Pasta</option>
                                <option value="magustoit">Magustoit</option>
                                <option value="eelroog">Eelroog</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="food_ingredients">Koostis *</label>
                            <textarea id="food_ingredients" name="koostis" required="required"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="food_calories">Kalorsus (kcal) *</label>
                            <input type="number" id="food_calories" name="kalorsus" min="1" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="food_price">Hind (€) *</label>
                            <input type="number" id="food_price" name="hind" step="0.01" min="0.01" required="required"/>
                        </div>
                        <button type="submit">Lisa toit</button>
                        <button type="reset">Tühjenda</button>
                    </form>
                </section>
                
                <section>
                    <h3>Lisa jook</h3>
                    <form id="addDrinkForm" method="POST" action="restoran.php">
                        <input type="hidden" name="action" value="add_drink"/>
                        <div class="form-group">
                            <label for="drink_name">Joogi nimetus *</label>
                            <input type="text" id="drink_name" name="nimetus" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="drink_type">Joogi liik *</label>
                            <select id="drink_type" name="liik" required="required">
                                <option value="alkohoolne">Alkohoolne</option>
                                <option value="alkoholivaba">Alkoholivaba</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="drink_producer">Tootja *</label>
                            <input type="text" id="drink_producer" name="tootja" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="drink_volume">Maht (l) *</label>
                            <input type="number" id="drink_volume" name="maht" step="0.01" min="0.01" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="drink_alcohol">Alkoholi protsent (%) *</label>
                            <input type="number" id="drink_alcohol" name="alkoholiProtsent" step="0.1" min="0" max="100" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="drink_price">Hind (€) *</label>
                            <input type="number" id="drink_price" name="hind" step="0.01" min="0.01" required="required"/>
                        </div>
                        <button type="submit">Lisa jook</button>
                        <button type="reset">Tühjenda</button>
                    </form>
                </section>
                
                <section>
                    <h3>Lisa tellimus</h3>
                    <form id="addOrderForm" method="POST" action="restoran.php">
                        <input type="hidden" name="action" value="add_order"/>
                        <div class="form-group">
                            <label for="order_table">Laua ID *</label>
                            <input type="number" id="order_table" name="laud_id" min="1" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="order_waiter">Teenindaja ID *</label>
                            <input type="number" id="order_waiter" name="teenindaja_id" min="1" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="client_name">Kliendi nimi *</label>
                            <input type="text" id="client_name" name="klient_nimi" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="client_phone">Kliendi telefon *</label>
                            <input type="tel" id="client_phone" name="klient_telefon" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="order_time">Tellimuse aeg *</label>
                            <input type="datetime-local" id="order_time" name="tellimuse_aeg" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="order_status">Staatus *</label>
                            <select id="order_status" name="staatus" required="required">
                                <option value="ootel">Ootel</option>
                                <option value="valmistamisel">Valmistamisel</option>
                                <option value="valmis">Valmis</option>
                                <option value="tarnitud">Tarnitud</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="order_priority">Prioriteet *</label>
                            <select id="order_priority" name="prioriteet" required="required">
                                <option value="madal">Madal</option>
                                <option value="keskmine">Keskmine</option>
                                <option value="kõrge">Kõrge</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="order_total">Kogusumma (€) *</label>
                            <input type="number" id="order_total" name="kogusumma" step="0.01" min="0.01" required="required"/>
                        </div>
                        <button type="submit">Lisa tellimus</button>
                        <button type="reset">Tühjenda</button>
                    </form>
                </section>
            </main>

            <script src="script.js"></script>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
