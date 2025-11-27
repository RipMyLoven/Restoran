<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <!DOCTYPE html>
        <html lang="et">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Restoran - Haldus</title>
            <link rel="stylesheet" href="css/styles.css"/>
        </head>
        <body>
            <header>
                <h1>Restorani Haldus</h1>
                <nav>
                    <a href="?format=html">Avaleht</a>
                    <a href="?view=manage">Haldus</a>
                    <a href="?format=json">JSON</a>
                </nav>
            </header>
            
            <main class="manage-page">
                <section class="manage-section">
                    <h2>Lisa Uus Toit</h2>
                    <form id="addFoodForm" class="manage-form">
                        <div class="form-group">
                            <label for="foodName">Nimetus:</label>
                            <input type="text" id="foodName" name="nimetus" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="foodType">Liik:</label>
                            <select id="foodType" name="liik" required="required">
                                <option value="praad">Praad</option>
                                <option value="supp">Supp</option>
                                <option value="salat">Salat</option>
                                <option value="pasta">Pasta</option>
                                <option value="magustoit">Magustoit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="foodIngredients">Koostis:</label>
                            <textarea id="foodIngredients" name="koostis" required="required"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="foodCalories">Kalorsus:</label>
                            <input type="number" id="foodCalories" name="kalorsus" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="foodPrice">Hind (€):</label>
                            <input type="number" id="foodPrice" name="hind" step="0.01" required="required"/>
                        </div>
                        <button type="submit" class="btn btn-primary">Lisa Toit</button>
                    </form>
                </section>
                
                <section class="manage-section">
                    <h2>Lisa Uus Tellimus</h2>
                    <form id="addOrderForm" class="manage-form">
                        <div class="form-group">
                            <label for="orderTable">Laud ID:</label>
                            <input type="number" id="orderTable" name="laud_id" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="orderWaiter">Teenindaja ID:</label>
                            <input type="number" id="orderWaiter" name="teenindaja_id" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="clientName">Kliendi Nimi:</label>
                            <input type="text" id="clientName" name="klient_nimi" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="clientPhone">Kliendi Telefon:</label>
                            <input type="tel" id="clientPhone" name="klient_telefon" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="orderTime">Tellimuse Aeg:</label>
                            <input type="datetime-local" id="orderTime" name="tellimuse_aeg" required="required"/>
                        </div>
                        <div class="form-group">
                            <label for="orderStatus">Staatus:</label>
                            <select id="orderStatus" name="staatus" required="required">
                                <option value="ootel">Ootel</option>
                                <option value="valmistamisel">Valmistamisel</option>
                                <option value="valmis">Valmis</option>
                                <option value="tarnitud">Tarnitud</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="orderPriority">Prioriteet:</label>
                            <select id="orderPriority" name="prioriteet" required="required">
                                <option value="madal">Madal</option>
                                <option value="keskmine">Keskmine</option>
                                <option value="kõrge">Kõrge</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="orderTotal">Kogusumma (€):</label>
                            <input type="number" id="orderTotal" name="kogusumma" step="0.01" required="required"/>
                        </div>
                        <button type="submit" class="btn btn-primary">Lisa Tellimus</button>
                    </form>
                </section>
                
                <section class="manage-section">
                    <h2>Otsi Tellimusi Staatuse Järgi</h2>
                    <form id="searchOrdersForm" class="manage-form">
                        <div class="form-group">
                            <label for="searchStatus">Staatus:</label>
                            <select id="searchStatus" name="search_status">
                                <option value="">Kõik</option>
                                <option value="ootel">Ootel</option>
                                <option value="valmistamisel">Valmistamisel</option>
                                <option value="valmis">Valmis</option>
                                <option value="tarnitud">Tarnitud</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Otsi</button>
                    </form>
                    <div id="searchResults" class="search-results"></div>
                </section>
                
                <section class="manage-section">
                    <h2>Praegused Toidud</h2>
                    <div class="items-list">
                        <xsl:for-each select="restoran/menyy/tooted/toit">
                            <div class="list-item">
                                <strong><xsl:value-of select="nimetus"/></strong> - 
                                <xsl:value-of select="@liik"/> - 
                                <xsl:value-of select="@hind"/>€ - 
                                <xsl:value-of select="kalorsus"/> kcal
                            </div>
                        </xsl:for-each>
                    </div>
                </section>
                
                <section class="manage-section">
                    <h2>Praegused Tellimused</h2>
                    <div class="items-list">
                        <xsl:for-each select="restoran/tellimused/tellimus">
                            <div class="list-item">
                                <strong>Tellimus #<xsl:value-of select="@id"/></strong> - 
                                <xsl:value-of select="kliendi_info/nimi"/> - 
                                Laud: <xsl:value-of select="@laud_id"/> - 
                                Staatus: <xsl:value-of select="@staatus"/> - 
                                Summa: <xsl:value-of select="kogusumma"/>€
                            </div>
                        </xsl:for-each>
                    </div>
                </section>
            </main>
            
            <footer>
                <p>© 2025 Restorani Haldussüsteem</p>
            </footer>
            
            <script src="script.js"></script>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
