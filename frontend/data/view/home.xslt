<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8" doctype-public="html"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <meta charset="UTF-8"/>
                <title>Restoran - Pealeht</title>
                <link rel="stylesheet" href="css/styles.css"/>
            </head>
            <body>
                <nav class="main-nav">
                    <div class="nav-container">
                        <a href="?format=html" class="nav-brand">Restoran</a>
                        <div class="nav-links">
                            <a href="?format=html">Andmed</a>
                            <a href="?format=json" target="_blank">JSON</a>
                        </div>
                    </div>
                </nav>
                
                <div class="container">
                    <h1>Tere tulemast restorani süsteemi</h1>
                    <p>Valige üks valikutest menüüst.</p>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
