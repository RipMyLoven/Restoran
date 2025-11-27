<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <!DOCTYPE html>
        <html lang="et">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Restoran - Lisa andmeid</title>
            <link rel="stylesheet" href="css/styles.css"/>
            <style>
                .add-container {
                    max-width: 800px;
                    margin: 0 auto;
                }
                .form-section {
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    padding: 25px;
                    margin-bottom: 25px;
                }
                .form-section h3 {
                    color: #2c3e50;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #2c3e50;
                }
                .form-group {
                    margin-bottom: 15px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: bold;
                    color: #555;
                }
                .form-group input,
                .form-group select,
                .form-group textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 14px;
                    box-sizing: border-box;
                }
                .form-group textarea {
                    min-height: 80px;
                    resize: vertical;
                }
                .btn-submit {
                    background: #27ae60;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 4px;
                    font-size: 16px;
                    cursor: pointer;
                    font-weight: bold;
                }
                .btn-submit:hover {
                    background: #229954;
                }
                .btn-reset {
                    background: #95a5a6;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 4px;
                    font-size: 16px;
                    cursor: pointer;
                    margin-left: 10px;
                }
                .btn-reset:hover {
                    background: #7f8c8d;
                }
                .info-box {
                    background: #e8f4f8;
                    border-left: 4px solid #3498db;
                    padding: 15px;
                    margin-bottom: 20px;
                    border-radius: 4px;
                }
                .current-data {
                    background: #fafafa;
                    border: 1px solid #ddd;
                    padding: 15px;
                    border-radius: 4px;
                    margin-top: 15px;
                    font-size: 13px;
                }
                .current-data h4 {
                    margin-bottom: 10px;
                    color: #2c3e50;
                }
                .data-item {
                    padding: 5px 0;
                    border-bottom: 1px solid #eee;
                }
                .data-item:last-child {
                    border-bottom: none;
                }
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
            
            <main class="add-container">
                <div class="info-box">
                    <strong>Info:</strong> Kasutage allpool olevaid vorme uute toitude, jookide ja tellimuste lisamiseks restorani süsteemi.
                </div>
                
                <div id="message-box" style="display:none; padding: 15px; margin-bottom: 20px; border-radius: 4px;"></div>
                
                <script>
                    // Показать сообщение, если есть
                    const urlParams = new URLSearchParams(window.location.search);
                    const status = urlParams.get('status');
                    const message = urlParams.get('message');
                    if (status &amp;&amp; message) {
                        const msgBox = document.getElementById('message-box');
                        msgBox.textContent = decodeURIComponent(message);
                        msgBox.style.display = 'block';
                        if (status === 'success') {
                            msgBox.style.background = '#d4edda';
                            msgBox.style.color = '#155724';
                            msgBox.style.border = '1px solid #c3e6cb';
                        } else {
                            msgBox.style.background = '#f8d7da';
                            msgBox.style.color = '#721c24';
                            msgBox.style.border = '1px solid #f5c6cb';
                        }
                    }
                </script>
                
                <!-- Lisa toit -->
                <div class="form-section">
                    <h3>🍽️ Lisa toit</h3>
                    <form id="addFoodForm" method="POST" action="restoran.php">
                        <input type="hidden" name="action" value="add_food"/>
                        
                        <div class="form-group">
                            <label for="food_name">Toidu nimetus *</label>
                            <input type="text" id="food_name" name="nimetus" required="required" placeholder="Näiteks: Ukraina borš"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="food_type">Toidu liik *</label>
                            <select id="food_type" name="liik" required="required">
                                <option value="">-- Vali liik --</option>
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
                            <textarea id="food_ingredients" name="koostis" required="required" placeholder="Koostisosade loetelu komadega eraldatuna"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="food_calories">Kalorsus (kcal) *</label>
                            <input type="number" id="food_calories" name="kalorsus" min="1" required="required" placeholder="350"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="food_price">Hind (€) *</label>
                            <input type="number" id="food_price" name="hind" step="0.01" min="0.01" required="required" placeholder="12.50"/>
                        </div>
                        
                        <button type="submit" class="btn-submit">Lisa toit</button>
                        <button type="reset" class="btn-reset">Tühjenda</button>
                    </form>
                    
                    <div class="current-data">
                        <h4>Praegused toidud:</h4>
                        <xsl:for-each select="restoran/menyy/tooted/toit">
                            <div class="data-item">
                                <strong><xsl:value-of select="nimetus"/></strong> - 
                                <xsl:value-of select="@liik"/> - 
                                <xsl:value-of select="@hind"/>€
                            </div>
                        </xsl:for-each>
                    </div>
                </div>
                
                <!-- Lisa jook -->
                <div class="form-section">
                    <h3>🥤 Lisa jook</h3>
                    <form id="addDrinkForm" method="POST" action="restoran.php">
                        <input type="hidden" name="action" value="add_drink"/>
                        
                        <div class="form-group">
                            <label for="drink_name">Joogi nimetus *</label>
                            <input type="text" id="drink_name" name="nimetus" required="required" placeholder="Näiteks: Apelsinimahl"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="drink_type">Joogi liik *</label>
                            <select id="drink_type" name="liik" required="required">
                                <option value="">-- Vali liik --</option>
                                <option value="alkohoolne">Alkohoolne</option>
                                <option value="alkoholivaba">Alkoholivaba</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="drink_producer">Tootja *</label>
                            <input type="text" id="drink_producer" name="tootja" required="required" placeholder="Tootja nimi"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="drink_volume">Maht (l) *</label>
                            <input type="number" id="drink_volume" name="maht" step="0.01" min="0.01" required="required" placeholder="0.33"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="drink_alcohol">Alkoholi protsent (%) *</label>
                            <input type="number" id="drink_alcohol" name="alkoholiProtsent" step="0.1" min="0" max="100" required="required" placeholder="0"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="drink_price">Hind (€) *</label>
                            <input type="number" id="drink_price" name="hind" step="0.01" min="0.01" required="required" placeholder="3.50"/>
                        </div>
                        
                        <button type="submit" class="btn-submit">Lisa jook</button>
                        <button type="reset" class="btn-reset">Tühjenda</button>
                    </form>
                    
                    <div class="current-data">
                        <h4>Praegused joogid:</h4>
                        <xsl:for-each select="restoran/menyy/tooted/jook">
                            <div class="data-item">
                                <strong><xsl:value-of select="nimetus"/></strong> - 
                                <xsl:value-of select="@liik"/> - 
                                <xsl:value-of select="hind"/>€
                            </div>
                        </xsl:for-each>
                    </div>
                </div>
                
                <!-- Lisa tellimus -->
                <div class="form-section">
                    <h3>📋 Lisa tellimus</h3>
                    <form id="addOrderForm" method="POST" action="restoran.php">
                        <input type="hidden" name="action" value="add_order"/>
                        
                        <div class="form-group">
                            <label for="order_table">Laua ID *</label>
                            <input type="number" id="order_table" name="laud_id" min="1" required="required" placeholder="1"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="order_waiter">Teenindaja ID *</label>
                            <input type="number" id="order_waiter" name="teenindaja_id" min="1" required="required" placeholder="1"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_name">Kliendi nimi *</label>
                            <input type="text" id="client_name" name="klient_nimi" required="required" placeholder="Ivan Petrov"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_phone">Kliendi telefon *</label>
                            <input type="tel" id="client_phone" name="klient_telefon" required="required" placeholder="+372 5xxx xxxx"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="order_time">Tellimuse aeg *</label>
                            <input type="datetime-local" id="order_time" name="tellimuse_aeg" required="required"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="order_status">Staatus *</label>
                            <select id="order_status" name="staatus" required="required">
                                <option value="">-- Vali staatus --</option>
                                <option value="ootel">Ootel</option>
                                <option value="valmistamisel">Valmistamisel</option>
                                <option value="valmis">Valmis</option>
                                <option value="tarnitud">Tarnitud</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="order_priority">Prioriteet *</label>
                            <select id="order_priority" name="prioriteet" required="required">
                                <option value="">-- Vali prioriteet --</option>
                                <option value="madal">Madal</option>
                                <option value="keskmine">Keskmine</option>
                                <option value="kõrge">Kõrge</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="order_total">Kogusumma (€) *</label>
                            <input type="number" id="order_total" name="kogusumma" step="0.01" min="0.01" required="required" placeholder="25.50"/>
                        </div>
                        
                        <button type="submit" class="btn-submit">Lisa tellimus</button>
                        <button type="reset" class="btn-reset">Tühjenda</button>
                    </form>
                    
                    <div class="current-data">
                        <h4>Praegused tellimused:</h4>
                        <xsl:for-each select="restoran/tellimused/tellimus">
                            <div class="data-item">
                                <strong>Tellimus #<xsl:value-of select="@id"/></strong> - 
                                <xsl:value-of select="kliendi_info/nimi"/> - 
                                Laud: <xsl:value-of select="@laud_id"/> - 
                                <xsl:value-of select="@staatus"/> - 
                                <xsl:value-of select="kogusumma"/>€
                            </div>
                        </xsl:for-each>
                    </div>
                </div>
            </main>
            
            <footer>
                <p>© 2025 Restorani Haldussüsteem</p>
            </footer>
            
            <script src="script.js"></script>
            <script>
                // Установить текущее время по умолчанию
                document.addEventListener('DOMContentLoaded', function() {
                    var now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    document.getElementById('order_time').value = now.toISOString().slice(0,16);
                });
            </script>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
