<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <html lang="et">
        <head>
            <meta charset="UTF-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>Restoran - Avaleht</title>
            <link rel="stylesheet" href="css/styles.css"/>
        </head>
        <body>
            <header>
                <h1>Tere tulemast restorani süsteemi</h1>
                <nav>
                    <a href="?format=html">Menüü vaade</a>
                    <a href="?view=add">Lisa andmeid</a>
                    <a href="?format=json">JSON formaat</a>
                </nav>
            </header>
            
            <main>
                <section class="menu-section">
                    <h2>Menüü - Toidud</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nimetus</th>
                                <th>Liik</th>
                                <th>Koostis</th>
                                <th>Kalorsus (kcal)</th>
                                <th>Hind (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="restoran/menyy/tooted/toit">
                                <tr>
                                    <td><xsl:value-of select="nimetus"/></td>
                                    <td><xsl:value-of select="@liik"/></td>
                                    <td><xsl:value-of select="koostis"/></td>
                                    <td><xsl:value-of select="kalorsus"/></td>
                                    <td><xsl:value-of select="@hind"/></td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </section>
                
                <section class="menu-section">
                    <h2>Menüü - Joogid</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nimetus</th>
                                <th>Liik</th>
                                <th>Tootja</th>
                                <th>Maht (L)</th>
                                <th>Alkohol (%)</th>
                                <th>Hind (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="restoran/menyy/tooted/jook">
                                <tr>
                                    <td><xsl:value-of select="nimetus"/></td>
                                    <td><xsl:value-of select="@liik"/></td>
                                    <td><xsl:value-of select="tootja"/></td>
                                    <td><xsl:value-of select="@maht"/></td>
                                    <td><xsl:value-of select="alkoholiProtsent"/></td>
                                    <td><xsl:value-of select="hind"/></td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </section>
                
                <section class="service-section">
                    <h2>Lauad</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kohti</th>
                                <th>Staatus</th>
                                <th>Asukoht</th>
                                <th>Reserveeritud</th>
                                <th>Erinõuded</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="restoran/teenindus/lauad/laud">
                                <tr>
                                    <td><xsl:value-of select="@kohtade_arv"/></td>
                                    <td><xsl:value-of select="@staatus"/></td>
                                    <td><xsl:value-of select="@asukoht"/></td>
                                    <td><xsl:value-of select="reserveeritud"/></td>
                                    <td><xsl:value-of select="erinoued"/></td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </section>
                
                <section class="service-section">
                    <h2>Teenindajad</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nimi</th>
                                <th>Telefon</th>
                                <th>Staatus</th>
                                <th>Kogemus (aastat)</th>
                                <th>Tööaeg</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="restoran/teenindus/teenindajad/teenindaja">
                                <tr>
                                    <td><xsl:value-of select="nimi"/></td>
                                    <td><xsl:value-of select="telefon"/></td>
                                    <td><xsl:value-of select="@staatus"/></td>
                                    <td><xsl:value-of select="@kogemus"/></td>
                                    <td><xsl:value-of select="tööaeg"/></td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </section>
                
                <section class="orders-section">
                    <h2>Tellimused</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Laud</th>
                                <th>Klient</th>
                                <th>Telefon</th>
                                <th>Aeg</th>
                                <th>Staatus</th>
                                <th>Prioriteet</th>
                                <th>Summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:for-each select="restoran/tellimused/tellimus">
                                <tr>
                                    <td><xsl:value-of select="@laud_id"/></td>
                                    <td><xsl:value-of select="kliendi_info/nimi"/></td>
                                    <td><xsl:value-of select="kliendi_info/telefon"/></td>
                                    <td><xsl:value-of select="tellimuse_aeg"/></td>
                                    <td><xsl:value-of select="@staatus"/></td>
                                    <td><xsl:value-of select="@prioriteet"/></td>
                                    <td><xsl:value-of select="kogusumma"/>€</td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
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
