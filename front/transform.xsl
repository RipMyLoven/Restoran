<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <title>Restoran XSL</title>
                <style>
                    body { font-family: Arial; margin: 20px; }
                    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
                    th, td { border: 1px solid #ddd; padding: 8px; }
                    th { background: #f2f2f2; }
                </style>
            </head>
            <body>
                <h1>Restoran (XSL Transform)</h1>
                
                <h2>Tellimused</h2>
                <table>
                    <tr><th>ID</th><th>Klient</th><th>Staatus</th><th>Summa</th></tr>
                    <xsl:for-each select="//tellimus">
                        <tr>
                            <td><xsl:value-of select="@id"/></td>
                            <td><xsl:value-of select="kliendi_info/nimi"/></td>
                            <td><xsl:value-of select="@staatus"/></td>
                            <td>€<xsl:value-of select="kogusumma"/></td>
                        </tr>
                    </xsl:for-each>
                </table>
                
                <h2>Menüü</h2>
                <table>
                    <tr><th>Nimetus</th><th>Hind</th><th>Liik</th></tr>
                    <xsl:for-each select="//toit">
                        <tr>
                            <td><xsl:value-of select="nimetus"/></td>
                            <td>€<xsl:value-of select="@hind"/></td>
                            <td><xsl:value-of select="@liik"/></td>
                        </tr>
                    </xsl:for-each>
                </table>
                
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
