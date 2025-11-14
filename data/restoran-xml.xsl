<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="UTF-8" indent="yes"/>
    
    <!-- Template for root element transformation -->
    <xsl:template match="/">
        <restorani_kokkuvõte>
            <metaandmed>
                <genereeritud_kuupäev><xsl:value-of select="current-dateTime()"/></genereeritud_kuupäev>
                <allikas>restoran.xml</allikas>
                <kirjeldus>Automaatselt genereeritud kokkuvõte restoraniandmetest</kirjeldus>
            </metaandmed>
            <xsl:apply-templates select="restoran"/>
        </restorani_kokkuvõte>
    </xsl:template>
    
    <!-- Restaurant basic info template -->
    <xsl:template match="restoran">
        <restoraniinfo>
            <nimi><xsl:value-of select="info/@nimi"/></nimi>
            <aadress><xsl:value-of select="info/@aadress"/></aadress>
            <telefon><xsl:value-of select="info/@telefon"/></telefon>
            <kirjeldus><xsl:value-of select="info/kirjeldus"/></kirjeldus>
            <xsl:apply-templates select="info/avamisajad"/>
        </restoraniinfo>
        
        <menüü_kokkuvõte>
            <xsl:apply-templates select="menüü" mode="summary"/>
        </menüü_kokkuvõte>
        
        <töökorraldus_kokkuvõte>
            <xsl:apply-templates select="töökorraldus" mode="summary"/>
        </töökorraldus_kokkuvõte>
        
        <täpsem_info>
            <xsl:apply-templates select="menüü" mode="detailed"/>
            <xsl:apply-templates select="töökorraldus" mode="detailed"/>
        </täpsem_info>
    </xsl:template>
    
    <!-- Opening hours transformation -->
    <xsl:template match="avamisajad">
        <lahtiolekuajad>
            <tööpäevad>
                <esmaspäev><xsl:value-of select="esmaspäev"/></esmaspäev>
                <teisipäev><xsl:value-of select="teisipäev"/></teisipäev>
                <kolmapäev><xsl:value-of select="kolmapäev"/></kolmapäev>
                <neljapäev><xsl:value-of select="neljapäev"/></neljapäev>
                <reede><xsl:value-of select="reede"/></reede>
            </tööpäevad>
            <nädalavahetus>
                <laupäev><xsl:value-of select="laupäev"/></laupäev>
                <pühapäev><xsl:value-of select="pühapäev"/></pühapäev>
            </nädalavahetus>
        </lahtiolekuajad>
    </xsl:template>
    
    <!-- Menu summary template -->
    <xsl:template match="menüü" mode="summary">
        <statistika>
            <eelroogad_arv><xsl:value-of select="count(eelroogad/toit)"/></eelroogad_arv>
            <põhiroogad_arv><xsl:value-of select="count(toidud/toit)"/></põhiroogad_arv>
            <magustoidud_arv><xsl:value-of select="count(magustoidud/toit)"/></magustoidud_arv>
            <joogid_arv><xsl:value-of select="count(joogid/jook)"/></joogid_arv>
            <kokku_toiduained><xsl:value-of select="count(eelroogad/toit) + count(toidud/toit) + count(magustoidud/toit) + count(joogid/jook)"/></kokku_toiduained>
        </statistika>
        
        <hinnavahemikud>
            <eelroogad>
                <min_hind><xsl:value-of select="format-number(number(eelroogad/toit[1]/@hind), '0.00')"/></min_hind>
                <max_hind><xsl:value-of select="format-number(number(eelroogad/toit[1]/@hind), '0.00')"/></max_hind>
            </eelroogad>
            <põhiroogad>
                <min_hind><xsl:value-of select="format-number(min(toidud/toit/@hind), '0.00')"/></min_hind>
                <max_hind><xsl:value-of select="format-number(max(toidud/toit/@hind), '0.00')"/></max_hind>
            </põhiroogad>
        </hinnavahemikud>
        
        <eridieedi_valikud>
            <vegan_valikud><xsl:value-of select="count(.//toit[spetsiaalne_dieet/@vegan='jah'])"/></vegan_valikud>
            <vegetaar_valikud><xsl:value-of select="count(.//toit[spetsiaalne_dieet/@vegetarian='jah'])"/></vegetaar_valikud>
            <gluteenivabad_valikud><xsl:value-of select="count(.//toit[spetsiaalne_dieet/@gluteenivaba='jah'])"/></gluteenivabad_valikud>
        </eridieedi_valikud>
    </xsl:template>
    
    <!-- Operations summary template -->
    <xsl:template match="töökorraldus" mode="summary">
        <laudad_kokkuvõte>
            <kokku_laudu><xsl:value-of select="count(laudad/laud)"/></kokku_laudu>
            <vabad_lauad><xsl:value-of select="count(laudad/laud[@staatus='vaba'])"/></vabad_lauad>
            <broneeritud_lauad><xsl:value-of select="count(laudad/laud[@staatus='broneeritud'])"/></broneeritud_lauad>
            <hõivatud_lauad><xsl:value-of select="count(laudad/laud[@staatus='hõivatud'])"/></hõivatud_lauad>
            <kokku_kohti><xsl:value-of select="sum(laudad/laud/@kohtade_arv)"/></kokku_kohti>
        </laudad_kokkuvõte>
        
        <tellimused_kokkuvõte>
            <aktiivsed_tellimused><xsl:value-of select="count(tellimused/tellimus[tellimusestaatus/@praegune != 'lõpetatud'])"/></aktiivsed_tellimused>
            <lõpetatud_tellimused><xsl:value-of select="count(tellimused/tellimus[tellimusestaatus/@praegune = 'lõpetatud'])"/></lõpetatud_tellimused>
            <päeva_käive>
                <xsl:value-of select="format-number(sum(tellimused/tellimus[tellimusestaatus/@praegune = 'lõpetatud']/arveldus/lõppsumma), '0.00')"/>
            </päeva_käive>
        </tellimused_kokkuvõte>
        
        <personal_kokkuvõte>
            <teenindajate_arv><xsl:value-of select="count(teenindajad/teenindaja)"/></teenindajate_arv>
            <keelte_osakonnad>
                <eesti_keel><xsl:value-of select="count(teenindajad/teenindaja[contains(@keel, 'eesti')])"/></eesti_keel>
                <inglise_keel><xsl:value-of select="count(teenindajad/teenindaja[contains(@keel, 'inglise')])"/></inglise_keel>
                <vene_keel><xsl:value-of select="count(teenindajad/teenindaja[contains(@keel, 'vene')])"/></vene_keel>
            </keelte_osakonnad>
        </personal_kokkuvõte>
    </xsl:template>
    
    <!-- Detailed menu template -->
    <xsl:template match="menüü" mode="detailed">
        <täpsem_menüü>
            <kategooriad>
                <eelroogad kategooria="{eelroogad/@kategooria}">
                    <xsl:for-each select="eelroogad/toit">
                        <toode id="{@id}">
                            <nimi><xsl:value-of select="@nimi"/></nimi>
                            <hind><xsl:value-of select="@hind"/></hind>
                            <allergeenid><xsl:value-of select="@allergeenid"/></allergeenid>
                            <valmimisaeg_min><xsl:value-of select="@valmimisaeg"/></valmimisaeg_min>
                            <toiteväärtus kalorsus="{toiteväärtus/@kalorsus}" valgud="{toiteväärtus/@valgud}" rasv="{toiteväärtus/@rasv}" süsivesikud="{toiteväärtus/@süsivesikud}"/>
                        </toode>
                    </xsl:for-each>
                </eelroogad>
                
                <põhiroogad kategooria="{toidud/@kategooria}">
                    <xsl:for-each select="toidud/toit">
                        <toode id="{@id}">
                            <nimi><xsl:value-of select="@nimi"/></nimi>
                            <hind><xsl:value-of select="@hind"/></hind>
                            <allergeenid><xsl:value-of select="@allergeenid"/></allergeenid>
                            <valmimisaeg_min><xsl:value-of select="@valmimisaeg"/></valmimisaeg_min>
                            <toiteväärtus kalorsus="{toiteväärtus/@kalorsus}" valgud="{toiteväärtus/@valgud}" rasv="{toiteväärtus/@rasv}" süsivesikud="{toiteväärtus/@süsivesikud}"/>
                            <dieet vegan="{spetsiaalne_dieet/@vegan}" vegetarian="{spetsiaalne_dieet/@vegetarian}" gluteenivaba="{spetsiaalne_dieet/@gluteenivaba}"/>
                        </toode>
                    </xsl:for-each>
                </põhiroogad>
                
                <magustoidud kategooria="{magustoidud/@kategooria}">
                    <xsl:for-each select="magustoidud/toit">
                        <toode id="{@id}">
                            <nimi><xsl:value-of select="@nimi"/></nimi>
                            <hind><xsl:value-of select="@hind"/></hind>
                            <allergeenid><xsl:value-of select="@allergeenid"/></allergeenid>
                            <valmimisaeg_min><xsl:value-of select="@valmimisaeg"/></valmimisaeg_min>
                            <toiteväärtus kalorsus="{toiteväärtus/@kalorsus}" valgud="{toiteväärtus/@valgud}" rasv="{toiteväärtus/@rasv}" süsivesikud="{toiteväärtus/@süsivesikud}"/>
                            <dieet vegan="{spetsiaalne_dieet/@vegan}" vegetarian="{spetsiaalne_dieet/@vegetarian}" gluteenivaba="{spetsiaalne_dieet/@gluteenivaba}"/>
                        </toode>
                    </xsl:for-each>
                </magustoidud>
                
                <joogid kategooria="{joogid/@kategooria}">
                    <xsl:for-each select="joogid/jook">
                        <jook id="{@id}">
                            <nimi><xsl:value-of select="@nimi"/></nimi>
                            <hind><xsl:value-of select="@hind"/></hind>
                            <maht><xsl:value-of select="@maht"/></maht>
                            <temperatuur><xsl:value-of select="@temperatuur"/></temperatuur>
                            <xsl:if test="@alkohol">
                                <alkoholisisaldus><xsl:value-of select="@alkohol"/></alkoholisisaldus>
                            </xsl:if>
                        </jook>
                    </xsl:for-each>
                </joogid>
            </kategooriad>
        </täpsem_menüü>
    </xsl:template>
    
    <!-- Detailed operations template -->
    <xsl:template match="töökorraldus" mode="detailed">
        <täpsem_töökorraldus>
            <laudade_detailid>
                <xsl:for-each select="laudad/laud">
                    <laud number="{@number}" id="{@id}">
                        <kohtade_arv><xsl:value-of select="@kohtade_arv"/></kohtade_arv>
                        <ala><xsl:value-of select="@ala"/></ala>
                        <staatus><xsl:value-of select="@staatus"/></staatus>
                        <asukoht><xsl:value-of select="asukoht"/></asukoht>
                        <omadused romantiline="{spetsiaalsed_omadused/@romantiline}" ligipääsetav="{spetsiaalsed_omadused/@ligipääsetav}"/>
                        <xsl:if test="broneering">
                            <broneering klient="{broneering/@klient}" aeg="{broneering/@aeg}"/>
                        </xsl:if>
                    </laud>
                </xsl:for-each>
            </laudade_detailid>
            
            <teenindajate_detailid>
                <xsl:for-each select="teenindajad/teenindaja">
                    <teenindaja id="{@id}">
                        <nimi><xsl:value-of select="@nimi"/></nimi>
                        <amet><xsl:value-of select="@amet"/></amet>
                        <tööstaaž><xsl:value-of select="@tööstaaž"/></tööstaaž>
                        <keeled><xsl:value-of select="@keel"/></keeled>
                        <vastutusala><xsl:value-of select="vastutusala"/></vastutusala>
                        <kontakt telefon="{kontakt/@telefon}" email="{kontakt/@email}"/>
                        <sertifikaadid>
                            <xsl:for-each select="sertifikaadid/sertifikaat">
                                <sertifikaat><xsl:value-of select="."/></sertifikaat>
                            </xsl:for-each>
                        </sertifikaadid>
                    </teenindaja>
                </xsl:for-each>
            </teenindajate_detailid>
            
            <aktiivsed_tellimused>
                <xsl:for-each select="tellimused/tellimus[tellimusestaatus/@praegune != 'lõpetatud']">
                    <tellimus id="{@id}">
                        <laud><xsl:value-of select="@laua_nr"/></laud>
                        <teenindaja><xsl:value-of select="@teenindaja_id"/></teenindaja>
                        <kellaaeg><xsl:value-of select="@kellaaeg"/></kellaaeg>
                        <klientide_arv><xsl:value-of select="@klientide_arv"/></klientide_arv>
                        <staatus><xsl:value-of select="tellimusestaatus/@praegune"/></staatus>
                        <summa><xsl:value-of select="arveldus/lõppsumma"/></summa>
                        <tooted_kokku><xsl:value-of select="count(tellitud_tooted/toode)"/></tooted_kokku>
                    </tellimus>
                </xsl:for-each>
            </aktiivsed_tellimused>
        </täpsem_töökorraldus>
    </xsl:template>
</xsl:stylesheet>
