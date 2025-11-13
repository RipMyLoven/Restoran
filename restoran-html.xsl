<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <title>Restoran - <xsl:value-of select="restoran/info/@nimi"/></title>
                <meta charset="UTF-8"/>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
                    .container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .header { text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; }
                    .section { margin: 30px 0; padding: 20px; border-radius: 8px; background-color: #fafafa; }
                    .section h2 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
                    .menu-item { background: white; margin: 10px 0; padding: 15px; border-radius: 5px; border-left: 4px solid #667eea; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                    .price { color: #e74c3c; font-weight: bold; font-size: 1.2em; }
                    .allergens { color: #e67e22; font-style: italic; }
                    .nutritional { font-size: 0.9em; color: #7f8c8d; }
                    .table-info { display: inline-block; background: #ecf0f1; padding: 10px; margin: 5px; border-radius: 5px; }
                    .status-busy { color: #e74c3c; font-weight: bold; }
                    .status-free { color: #27ae60; font-weight: bold; }
                    .status-reserved { color: #f39c12; font-weight: bold; }
                    .order-item { background: #fff; margin: 10px 0; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
                    .opening-hours { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
                    .day { background: white; padding: 10px; border-radius: 5px; text-align: center; }
                </style>
            </head>
            <body>
                <div class="container">
                    <xsl:apply-templates select="restoran"/>
                </div>
            </body>
        </html>
    </xsl:template>
    
    <xsl:template match="restoran">
        <div class="header">
            <h1><xsl:value-of select="info/@nimi"/></h1>
            <p><xsl:value-of select="info/@aadress"/></p>
            <p>Telefon: <xsl:value-of select="info/@telefon"/></p>
            <p><xsl:value-of select="info/kirjeldus"/></p>
        </div>
        
        <!-- Avamisajad -->
        <div class="section">
            <h2>Avamisajad</h2>
            <div class="opening-hours">
                <div class="day">
                    <strong>Esmaspäev:</strong><br/>
                    <xsl:value-of select="info/avamisajad/esmaspäev"/>
                </div>
                <div class="day">
                    <strong>Teisipäev:</strong><br/>
                    <xsl:value-of select="info/avamisajad/teisipäev"/>
                </div>
                <div class="day">
                    <strong>Kolmapäev:</strong><br/>
                    <xsl:value-of select="info/avamisajad/kolmapäev"/>
                </div>
                <div class="day">
                    <strong>Neljapäev:</strong><br/>
                    <xsl:value-of select="info/avamisajad/neljapäev"/>
                </div>
                <div class="day">
                    <strong>Reede:</strong><br/>
                    <xsl:value-of select="info/avamisajad/reede"/>
                </div>
                <div class="day">
                    <strong>Laupäev:</strong><br/>
                    <xsl:value-of select="info/avamisajad/laupäev"/>
                </div>
                <div class="day">
                    <strong>Pühapäev:</strong><br/>
                    <xsl:value-of select="info/avamisajad/pühapäev"/>
                </div>
            </div>
        </div>
        
        <!-- Menüü -->
        <div class="section">
            <h2>Menüü</h2>
            <xsl:apply-templates select="menüü"/>
        </div>
        
        <!-- Laudade info -->
        <div class="section">
            <h2>Laudade olukord</h2>
            <xsl:apply-templates select="töökorraldus/laudad"/>
        </div>
        
        <!-- Tellimused -->
        <div class="section">
            <h2>Aktiivsed tellimused</h2>
            <xsl:apply-templates select="töökorraldus/tellimused"/>
        </div>
        
        <!-- Teenindajad -->
        <div class="section">
            <h2>Meie meeskond</h2>
            <xsl:apply-templates select="töökorraldus/teenindajad"/>
        </div>
    </xsl:template>
    
    <xsl:template match="menüü">
        <h3>Eelroogad</h3>
        <xsl:apply-templates select="eelroogad/toit"/>
        
        <h3>Põhiroogad</h3>
        <xsl:apply-templates select="toidud/toit"/>
        
        <h3>Magustoidud</h3>
        <xsl:apply-templates select="magustoidud/toit"/>
        
        <h3>Joogid</h3>
        <xsl:apply-templates select="joogid/jook"/>
    </xsl:template>
    
    <xsl:template match="toit">
        <div class="menu-item">
            <h4><xsl:value-of select="@nimi"/> - <span class="price">€<xsl:value-of select="@hind"/></span></h4>
            <p><xsl:value-of select="kirjeldus"/></p>
            <p><strong>Valmimisaeg:</strong> <xsl:value-of select="@valmimisaeg"/> min</p>
            <xsl:if test="@allergeenid">
                <p class="allergens"><strong>Allergeenid:</strong> <xsl:value-of select="@allergeenid"/></p>
            </xsl:if>
            <xsl:if test="koostisosad">
                <p><strong>Koostis:</strong> 
                    <xsl:for-each select="koostisosad/koostisosa">
                        <xsl:value-of select="."/>
                        <xsl:if test="position() != last()">, </xsl:if>
                    </xsl:for-each>
                </p>
            </xsl:if>
            <xsl:if test="toiteväärtus">
                <p class="nutritional">
                    <strong>Toiteväärtus:</strong> 
                    <xsl:value-of select="toiteväärtus/@kalorsus"/>kcal, 
                    valgud: <xsl:value-of select="toiteväärtus/@valgud"/>g, 
                    rasv: <xsl:value-of select="toiteväärtus/@rasv"/>g, 
                    süsivesikud: <xsl:value-of select="toiteväärtus/@süsivesikud"/>g
                </p>
            </xsl:if>
            <xsl:if test="spetsiaalne_dieet">
                <p>
                    <xsl:if test="spetsiaalne_dieet/@vegan='jah'">🌱 Vegan </xsl:if>
                    <xsl:if test="spetsiaalne_dieet/@vegetarian='jah'">🥬 Vegetaarne </xsl:if>
                    <xsl:if test="spetsiaalne_dieet/@gluteenivaba='jah'">🌾 Gluteenivaba</xsl:if>
                </p>
            </xsl:if>
        </div>
    </xsl:template>
    
    <xsl:template match="jook">
        <div class="menu-item">
            <h4><xsl:value-of select="@nimi"/> - <span class="price">€<xsl:value-of select="@hind"/></span></h4>
            <p><xsl:value-of select="kirjeldus"/></p>
            <p><strong>Maht:</strong> <xsl:value-of select="@maht"/></p>
            <xsl:if test="@alkohol">
                <p><strong>Alkoholisisaldus:</strong> <xsl:value-of select="@alkohol"/></p>
            </xsl:if>
            <xsl:if test="kofeiini_sisaldus">
                <p><strong>Kofeiinisisaldus:</strong> <xsl:value-of select="kofeiini_sisaldus"/></p>
            </xsl:if>
        </div>
    </xsl:template>
    
    <xsl:template match="laudad">
        <xsl:for-each select="laud">
            <div class="table-info">
                <strong>Laud <xsl:value-of select="@number"/></strong> (<xsl:value-of select="@kohtade_arv"/> kohta)<br/>
                Staatus: 
                <xsl:choose>
                    <xsl:when test="@staatus='vaba'">
                        <span class="status-free">Vaba</span>
                    </xsl:when>
                    <xsl:when test="@staatus='broneeritud'">
                        <span class="status-reserved">Broneeritud</span>
                    </xsl:when>
                    <xsl:when test="@staatus='hõivatud'">
                        <span class="status-busy">Hõivatud</span>
                    </xsl:when>
                </xsl:choose><br/>
                <xsl:value-of select="asukoht"/><br/>
                <xsl:if test="broneering">
                    Broneerija: <xsl:value-of select="broneering/@klient"/>
                </xsl:if>
            </div>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="tellimused">
        <xsl:for-each select="tellimus[tellimusestaatus/@praegune != 'lõpetatud']">
            <div class="order-item">
                <h4>Tellimus <xsl:value-of select="@id"/> - Laud <xsl:value-of select="substring(@laua_nr, 2)"/></h4>
                <p><strong>Staatus:</strong> <xsl:value-of select="tellimusestaatus/@praegune"/></p>
                <p><strong>Klientide arv:</strong> <xsl:value-of select="@klientide_arv"/></p>
                <xsl:if test="@erivajadused">
                    <p><strong>Erivajadused:</strong> <xsl:value-of select="@erivajadused"/></p>
                </xsl:if>
                <p><strong>Kogusumma:</strong> €<xsl:value-of select="arveldus/lõppsumma"/></p>
            </div>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="teenindajad">
        <xsl:for-each select="teenindaja">
            <div class="table-info">
                <strong><xsl:value-of select="@nimi"/></strong><br/>
                <xsl:value-of select="@amet"/> (staaž: <xsl:value-of select="@tööstaaž"/> aastat)<br/>
                Keeled: <xsl:value-of select="@keel"/><br/>
                Vastutusala: <xsl:value-of select="vastutusala"/><br/>
                Tel: <xsl:value-of select="kontakt/@telefon"/>
            </div>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
