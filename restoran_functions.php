<?php

class RestoranManager {
    private $xmlFilePath;
    private $jsonFilePath;
    
    public function __construct($xmlFile = 'restoran.xml', $jsonFile = 'restoran_data.json') {
        $this->xmlFilePath = $xmlFile;
        $this->jsonFilePath = $jsonFile;
    }
    
    public function getRestoranInfo() {
        if (!file_exists($this->xmlFilePath)) {
            throw new Exception("XML fail ei leitud: " . $this->xmlFilePath);
        }
        
        $xml = simplexml_load_file($this->xmlFilePath);
        if ($xml === false) {
            throw new Exception("XML faili lugemine ebaõnnestus");
        }
        
        $info = [
            'nimi' => (string)$xml->info['nimi'],
            'aadress' => (string)$xml->info['aadress'],
            'telefon' => (string)$xml->info['telefon'],
            'kirjeldus' => (string)$xml->info->kirjeldus,
            'avamisajad' => [
                'esmaspäev' => (string)$xml->info->avamisajad->esmaspäev,
                'teisipäev' => (string)$xml->info->avamisajad->teisipäev,
                'kolmapäev' => (string)$xml->info->avamisajad->kolmapäev,
                'neljapäev' => (string)$xml->info->avamisajad->neljapäev,
                'reede' => (string)$xml->info->avamisajad->reede,
                'laupäev' => (string)$xml->info->avamisajad->laupäev,
                'pühapäev' => (string)$xml->info->avamisajad->pühapäev
            ]
        ];
        
        return $info;
    }
    
    public function getMenuData($kategooria = null, $maxHind = null, $vegan = false) {
        if (!file_exists($this->xmlFilePath)) {
            throw new Exception("XML fail ei leitud: " . $this->xmlFilePath);
        }
        
        $xml = simplexml_load_file($this->xmlFilePath);
        $menuData = [
            'eelroogad' => [],
            'põhiroogad' => [],
            'magustoidud' => [],
            'joogid' => []
        ];
        
        if ($xml->menüü->eelroogad) {
            foreach ($xml->menüü->eelroogad->toit as $toit) {
                $item = $this->processMenuItem($toit, $maxHind, $vegan);
                if ($item) $menuData['eelroogad'][] = $item;
            }
        }
        
        if ($xml->menüü->toidud) {
            foreach ($xml->menüü->toidud->toit as $toit) {
                $item = $this->processMenuItem($toit, $maxHind, $vegan);
                if ($item) $menuData['põhiroogad'][] = $item;
            }
        }
        
        if ($xml->menüü->magustoidud) {
            foreach ($xml->menüü->magustoidud->toit as $toit) {
                $item = $this->processMenuItem($toit, $maxHind, $vegan);
                if ($item) $menuData['magustoidud'][] = $item;
            }
        }
        
        if ($xml->menüü->joogid) {
            foreach ($xml->menüü->joogid->jook as $jook) {
                $item = $this->processDrinkItem($jook, $maxHind);
                if ($item) $menuData['joogid'][] = $item;
            }
        }
        
        if ($kategooria && isset($menuData[$kategooria])) {
            return [$kategooria => $menuData[$kategooria]];
        }
        
        return $menuData;
    }

    public function getOrderStatistics() {
        if (!file_exists($this->xmlFilePath)) {
            throw new Exception("XML fail ei leitud: " . $this->xmlFilePath);
        }
        
        $xml = simplexml_load_file($this->xmlFilePath);
        $stats = [
            'aktiivsed_tellimused' => 0,
            'lõpetatud_tellimused' => 0,
            'kokku_tellimusi' => 0,
            'päeva_käive' => 0,
            'keskmine_tellimuse_summa' => 0,
            'populaarsemad_toidud' => [],
            'laudade_hõivatus' => [
                'vabad' => 0,
                'hõivatud' => 0,
                'broneeritud' => 0
            ]
        ];
        
        if ($xml->töökorraldus->tellimused) {
            $kogusummad = [];
            $toiduloendur = [];
            
            foreach ($xml->töökorraldus->tellimused->tellimus as $tellimus) {
                $stats['kokku_tellimusi']++;
                $staatus = (string)$tellimus->tellimusestaatus['praegune'];
                
                if ($staatus === 'lõpetatud') {
                    $stats['lõpetatud_tellimused']++;
                    $summa = (float)$tellimus->arveldus->lõppsumma;
                    $stats['päeva_käive'] += $summa;
                    $kogusummad[] = $summa;
                } else {
                    $stats['aktiivsed_tellimused']++;
                }
                
                foreach ($tellimus->tellitud_tooted->toode as $toode) {
                    $toodeNimi = (string)$toode->nimi;
                    $kogus = (int)$toode['kogus'];
                    
                    if (!isset($toiduloendur[$toodeNimi])) {
                        $toiduloendur[$toodeNimi] = 0;
                    }
                    $toiduloendur[$toodeNimi] += $kogus;
                }
            }
            
            if (count($kogusummad) > 0) {
                $stats['keskmine_tellimuse_summa'] = round(array_sum($kogusummad) / count($kogusummad), 2);
            }
            
            arsort($toiduloendur);
            $stats['populaarsemad_toidud'] = array_slice($toiduloendur, 0, 5, true);
        }
        
        if ($xml->töökorraldus->laudad) {
            foreach ($xml->töökorraldus->laudad->laud as $laud) {
                $staatus = (string)$laud['staatus'];
                $stats['laudade_hõivatus'][$staatus]++;
            }
        }
        
        return $stats;
    }
    
    public function createJsonFromXml($täisInfo = true) {
        try {
            $restoranInfo = $this->getRestoranInfo();
            $menuData = $this->getMenuData();
            $statistics = $this->getOrderStatistics();
            
            $jsonData = [
                'restoran_info' => $restoranInfo,
                'menüü' => $menuData,
                'updated' => date('Y-m-d H:i:s')
            ];
            
            if ($täisInfo) {
                $jsonData['statistika'] = $statistics;
                $jsonData['laudade_info'] = $this->getTableInfo();
                $jsonData['teenindajad'] = $this->getStaffInfo();
            }
            
            $jsonString = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            if (file_put_contents($this->jsonFilePath, $jsonString) !== false) {
                return true;
            } else {
                throw new Exception("JSON faili kirjutamine ebaõnnestus");
            }
        } catch (Exception $e) {
            error_log("JSON loomise viga: " . $e->getMessage());
            return false;
        }
    }
    
    public function addDataToJson($kategooria, $andmed) {
        try {
            $jsonData = [];
            if (file_exists($this->jsonFilePath)) {
                $jsonString = file_get_contents($this->jsonFilePath);
                $jsonData = json_decode($jsonString, true);
                if ($jsonData === null) {
                    throw new Exception("JSON faili lugemine ebaõnnestus");
                }
            }
            
            if (!isset($jsonData['lisaandmed'])) {
                $jsonData['lisaandmed'] = [];
            }
            
            if (!isset($jsonData['lisaandmed'][$kategooria])) {
                $jsonData['lisaandmed'][$kategooria] = [];
            }
            
            $lisatavAndmed = array_merge($andmed, [
                'id' => uniqid(),
                'lisatud' => date('Y-m-d H:i:s')
            ]);
            
            $jsonData['lisaandmed'][$kategooria][] = $lisatavAndmed;
            $jsonData['viimati_uuendatud'] = date('Y-m-d H:i:s');
            
            $jsonString = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            if (file_put_contents($this->jsonFilePath, $jsonString) !== false) {
                return true;
            } else {
                throw new Exception("JSON faili kirjutamine ebaõnnestus");
            }
        } catch (Exception $e) {
            error_log("JSON andmete lisamise viga: " . $e->getMessage());
            return false;
        }
    }

    public function getJsonData($kategooria = null) {
        if (!file_exists($this->jsonFilePath)) {
            return ['error' => 'JSON fail ei leidu'];
        }
        
        $jsonString = file_get_contents($this->jsonFilePath);
        $jsonData = json_decode($jsonString, true);
        
        if ($jsonData === null) {
            return ['error' => 'JSON faili lugemine ebaõnnestus'];
        }
        
        if ($kategooria) {
            return isset($jsonData[$kategooria]) ? $jsonData[$kategooria] : ['error' => 'Kategooriat ei leitud'];
        }
        
        return $jsonData;
    }
    
    
    private function processMenuItem($toit, $maxHind, $vegan) {
        $hind = (float)$toit['hind'];
        $isVegan = isset($toit->spetsiaalne_dieet) && (string)$toit->spetsiaalne_dieet['vegan'] === 'jah';
        
        if ($maxHind && $hind > $maxHind) return null;
        if ($vegan && !$isVegan) return null;
        
        $item = [
            'id' => (string)$toit['id'],
            'nimi' => (string)$toit['nimi'],
            'hind' => $hind,
            'kirjeldus' => (string)$toit->kirjeldus,
            'valmimisaeg' => (int)$toit['valmimisaeg'],
            'allergeenid' => (string)$toit['allergeenid']
        ];
        
        if (isset($toit->toiteväärtus)) {
            $item['toiteväärtus'] = [
                'kalorsus' => (int)$toit->toiteväärtus['kalorsus'],
                'valgud' => (int)$toit->toiteväärtus['valgud'],
                'rasv' => (int)$toit->toiteväärtus['rasv'],
                'süsivesikud' => (int)$toit->toiteväärtus['süsivesikud']
            ];
        }
        
        if (isset($toit->spetsiaalne_dieet)) {
            $item['dieet'] = [
                'vegan' => (string)$toit->spetsiaalne_dieet['vegan'] === 'jah',
                'vegetarian' => (string)$toit->spetsiaalne_dieet['vegetarian'] === 'jah',
                'gluteenivaba' => (string)$toit->spetsiaalne_dieet['gluteenivaba'] === 'jah'
            ];
        }
        
        return $item;
    }
    
    private function processDrinkItem($jook, $maxHind) {
        $hind = (float)$jook['hind'];
        
        if ($maxHind && $hind > $maxHind) return null;
        
        return [
            'id' => (string)$jook['id'],
            'nimi' => (string)$jook['nimi'],
            'hind' => $hind,
            'kirjeldus' => (string)$jook->kirjeldus,
            'maht' => (string)$jook['maht'],
            'temperatuur' => (string)$jook['temperatuur'],
            'alkohol' => isset($jook['alkohol']) ? (string)$jook['alkohol'] : null
        ];
    }
    
    private function getTableInfo() {
        $xml = simplexml_load_file($this->xmlFilePath);
        $tables = [];
        
        if ($xml->töökorraldus->laudad) {
            foreach ($xml->töökorraldus->laudad->laud as $laud) {
                $tables[] = [
                    'id' => (string)$laud['id'],
                    'number' => (int)$laud['number'],
                    'kohtade_arv' => (int)$laud['kohtade_arv'],
                    'ala' => (string)$laud['ala'],
                    'staatus' => (string)$laud['staatus'],
                    'asukoht' => (string)$laud->asukoht
                ];
            }
        }
        
        return $tables;
    }
    
    private function getStaffInfo() {
        $xml = simplexml_load_file($this->xmlFilePath);
        $staff = [];
        
        if ($xml->töökorraldus->teenindajad) {
            foreach ($xml->töökorraldus->teenindajad->teenindaja as $teenindaja) {
                $staff[] = [
                    'id' => (string)$teenindaja['id'],
                    'nimi' => (string)$teenindaja['nimi'],
                    'amet' => (string)$teenindaja['amet'],
                    'tööstaaž' => (int)$teenindaja['tööstaaž'],
                    'keeled' => (string)$teenindaja['keel'],
                    'vastutusala' => (string)$teenindaja->vastutusala,
                    'telefon' => (string)$teenindaja->kontakt['telefon']
                ];
            }
        }
        
        return $staff;
    }
}

try {
    $restoran = new RestoranManager();
    
    echo "<h2>Restoraniinfo:</h2>\n";
    $info = $restoran->getRestoranInfo();
    print_r($info);
    
    echo "<h2>Vegan menüü (max 20€):</h2>\n";
    $veganMenu = $restoran->getMenuData(null, 20.00, true);
    print_r($veganMenu);
    
    echo "<h2>Tellimuste statistika:</h2>\n";
    $stats = $restoran->getOrderStatistics();
    print_r($stats);
    
    echo "<h2>JSON faili loomine...</h2>\n";
    if ($restoran->createJsonFromXml(true)) {
        echo "JSON fail edukalt loodud!\n";
    } else {
        echo "JSON faili loomine ebaõnnestus!\n";
    }
    
    echo "<h2>Andmete lisamine JSON faili...</h2>\n";
    $uusTellimus = [
        'laud' => 5,
        'klient' => 'Test Klient',
        'tooted' => ['Grillitud lõhe', 'Kohv'],
        'summa' => 28.40
    ];
    
    if ($restoran->addDataToJson('tellimused', $uusTellimus)) {
        echo "Andmed edukalt lisatud!\n";
    } else {
        echo "Andmete lisamine ebaõnnestus!\n";
    }
    
    $uusHinnang = [
        'klient' => 'Mari Mets',
        'hinne' => 5,
        'kommentaar' => 'Suurepärane teenindus ja maitsev toit!'
    ];
    
    if ($restoran->addDataToJson('hinnangud', $uusHinnang)) {
        echo "Hinnang edukalt lisatud!\n";
    } else {
        echo "Hinnangu lisamine ebaõnnestus!\n";
    }
    
} catch (Exception $e) {
    echo "Viga: " . $e->getMessage() . "\n";
}
?>
