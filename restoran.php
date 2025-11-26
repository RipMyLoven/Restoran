<?php
$xmlFile = 'data/restoran.xml';
$jsonFile = 'data/restoran.json';

function showView($viewName) {
    $xml = simplexml_load_file('data/restoran.xml');
    return transformXslt($xml, "data/view/{$viewName}.xslt");
}

function transformXslt($xml, $xslFile) {
    $xslContent = file_get_contents($xslFile);
    
    $html = preg_replace('/<\?xml.*?\?>/s', '', $xslContent);
    $html = preg_replace('/<xsl:stylesheet.*?>/s', '', $html);
    $html = preg_replace('/<\/xsl:stylesheet>/s', '', $html);
    $html = preg_replace('/<xsl:template.*?>/s', '', $html);
    $html = preg_replace('/<\/xsl:template>/s', '', $html);
    $html = preg_replace('/<xsl:output.*?\/>/s', '', $html);
    
    $html = preg_replace_callback('/<xsl:for-each select="([^"]+)">(.*?)<\/xsl:for-each>/s', 
        function($matches) use ($xml) {
            $select = $matches[1];
            $template = $matches[2];
            $result = '';
            
            if (strpos($select, 'restoran/menyy/tooted/toit') !== false) {
                foreach ($xml->menyy->tooted->toit as $toit) {
                    $row = $template;
                    $row = preg_replace('/<xsl:value-of select="nimetus"\s*\/?>/', htmlspecialchars((string)$toit->nimetus), $row);
                    $row = preg_replace('/<xsl:value-of select="@liik"\s*\/?>/', htmlspecialchars((string)$toit['liik']), $row);
                    $row = preg_replace('/<xsl:value-of select="@hind"\s*\/?>/', htmlspecialchars((string)$toit['hind']), $row);
                    $row = preg_replace('/<xsl:value-of select="koostis"\s*\/?>/', htmlspecialchars((string)$toit->koostis), $row);
                    $row = preg_replace('/<xsl:value-of select="kalorsus"\s*\/?>/', htmlspecialchars((string)$toit->kalorsus), $row);
                    $row = preg_replace('/\{@hind\}/', (float)$toit['hind'], $row);
                    $row = preg_replace('/\{kalorsus\}/', (int)$toit->kalorsus, $row);
                    $result .= $row;
                }
            } elseif (strpos($select, 'restoran/menyy/tooted/jook') !== false) {
                foreach ($xml->menyy->tooted->jook as $jook) {
                    $row = $template;
                    $row = preg_replace('/<xsl:value-of select="nimetus"\s*\/?>/', htmlspecialchars((string)$jook->nimetus), $row);
                    $row = preg_replace('/<xsl:value-of select="@liik"\s*\/?>/', htmlspecialchars((string)$jook['liik']), $row);
                    $row = preg_replace('/<xsl:value-of select="@maht"\s*\/?>/', htmlspecialchars((string)$jook['maht']), $row);
                    $row = preg_replace('/<xsl:value-of select="tootja"\s*\/?>/', htmlspecialchars((string)$jook->tootja), $row);
                    $row = preg_replace('/<xsl:value-of select="alkoholiProtsent"\s*\/?>/', htmlspecialchars((string)$jook->alkoholiProtsent), $row);
                    $row = preg_replace('/<xsl:value-of select="hind"\s*\/?>/', htmlspecialchars((string)$jook->hind), $row);
                    $row = preg_replace('/\{@maht\}/', (float)$jook['maht'], $row);
                    $row = preg_replace('/\{hind\}/', (float)$jook->hind, $row);
                    $result .= $row;
                }
            } elseif (strpos($select, 'restoran/teenindus/lauad/laud') !== false) {
                foreach ($xml->teenindus->lauad->laud as $laud) {
                    $row = $template;
                    $row = preg_replace('/<xsl:value-of select="@kohtade_arv"\s*\/?>/', htmlspecialchars((string)$laud['kohtade_arv']), $row);
                    $row = preg_replace('/<xsl:value-of select="@staatus"\s*\/?>/', htmlspecialchars((string)$laud['staatus']), $row);
                    $row = preg_replace('/<xsl:value-of select="@asukoht"\s*\/?>/', htmlspecialchars((string)$laud['asukoht']), $row);
                    $row = preg_replace('/<xsl:value-of select="reserveeritud"\s*\/?>/', htmlspecialchars((string)$laud->reserveeritud), $row);
                    $row = preg_replace('/<xsl:value-of select="erinoued"\s*\/?>/', htmlspecialchars((string)$laud->erinoued), $row);
                    $row = preg_replace('/\{@kohtade_arv\}/', (int)$laud['kohtade_arv'], $row);
                    $result .= $row;
                }
            } elseif (strpos($select, 'restoran/teenindus/teenindajad/teenindaja') !== false) {
                foreach ($xml->teenindus->teenindajad->teenindaja as $teenindaja) {
                    $row = $template;
                    $row = preg_replace('/<xsl:value-of select="nimi"\s*\/?>/', htmlspecialchars((string)$teenindaja->nimi), $row);
                    $row = preg_replace('/<xsl:value-of select="telefon"\s*\/?>/', htmlspecialchars((string)$teenindaja->telefon), $row);
                    $row = preg_replace('/<xsl:value-of select="@staatus"\s*\/?>/', htmlspecialchars((string)$teenindaja['staatus']), $row);
                    $row = preg_replace('/<xsl:value-of select="@kogemus"\s*\/?>/', htmlspecialchars((string)$teenindaja['kogemus']), $row);
                    $row = preg_replace('/<xsl:value-of select="tööaeg"\s*\/?>/', htmlspecialchars((string)$teenindaja->tööaeg), $row);
                    $row = preg_replace('/\{@kogemus\}/', (int)$teenindaja['kogemus'], $row);
                    $result .= $row;
                }
            } elseif (strpos($select, 'restoran/tellimused/tellimus') !== false) {
                foreach ($xml->tellimused->tellimus as $tellimus) {
                    $row = $template;
                    $row = preg_replace('/<xsl:value-of select="@laud_id"\s*\/?>/', htmlspecialchars((string)$tellimus['laud_id']), $row);
                    $row = preg_replace('/<xsl:value-of select="@teenindaja_id"\s*\/?>/', htmlspecialchars((string)$tellimus['teenindaja_id']), $row);
                    $row = preg_replace('/<xsl:value-of select="kliendi_info\/nimi"\s*\/?>/', htmlspecialchars((string)$tellimus->kliendi_info->nimi), $row);
                    $row = preg_replace('/<xsl:value-of select="kliendi_info\/telefon"\s*\/?>/', htmlspecialchars((string)$tellimus->kliendi_info->telefon), $row);
                    $row = preg_replace('/<xsl:value-of select="tellimuse_aeg"\s*\/?>/', htmlspecialchars((string)$tellimus->tellimuse_aeg), $row);
                    $row = preg_replace('/<xsl:value-of select="@staatus"\s*\/?>/', htmlspecialchars((string)$tellimus['staatus']), $row);
                    $row = preg_replace('/<xsl:value-of select="@prioriteet"\s*\/?>/', htmlspecialchars((string)$tellimus['prioriteet']), $row);
                    $row = preg_replace('/<xsl:value-of select="kogusumma"\s*\/?>/', htmlspecialchars((string)$tellimus->kogusumma), $row);
                    $row = preg_replace('/<xsl:value-of select="@id"\s*\/?>/', htmlspecialchars((string)$tellimus['id']), $row);
                    $row = preg_replace('/\{@laud_id\}/', (int)$tellimus['laud_id'], $row);
                    $row = preg_replace('/\{kogusumma\}/', (float)$tellimus->kogusumma, $row);
                    $result .= $row;
                }
            }
            
            return $result;
        },
        $html
    );
    
    return $html;
}

function searchOrdersByStatus($xml, $status) {
    $results = [];
    foreach ($xml->tellimused->tellimus as $tellimus) {
        if (empty($status) || strtolower((string)$tellimus['staatus']) == strtolower($status)) {
            $results[] = $tellimus;
        }
    }
    return $results;
}

function searchFoodByName($xml, $searchName) {
    $results = [];
    foreach ($xml->menyy->tooted->toit as $toit) {
        if (empty($searchName) || stripos((string)$toit->nimetus, $searchName) !== false) {
            $results[] = $toit;
        }
    }
    return $results;
}

function searchTablesByStatus($xml, $status) {
    $results = [];
    foreach ($xml->teenindus->lauad->laud as $laud) {
        if (empty($status) || strtolower((string)$laud['staatus']) == strtolower($status)) {
            $results[] = $laud;
        }
    }
    return $results;
}

function xmlToHtml($xmlFile) {
    return showView('main');
}

function loadJsonData($jsonFile) {
    if (!file_exists($jsonFile)) {
        return false;
    }
    $jsonContent = file_get_contents($jsonFile);
    return json_decode($jsonContent, true);
}

function saveJsonData($jsonFile, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($jsonFile, $json) !== false;
}

function addFoodToJson($jsonFile, $foodData) {
    $data = loadJsonData($jsonFile);
    if (!$data) {
        return false;
    }
    
    $maxId = 100;
    foreach ($data['menyy']['tooted']['toit'] as $toit) {
        $currentId = (int)$toit['@attributes']['id'];
        if ($currentId > $maxId) {
            $maxId = $currentId;
        }
    }
    $newId = $maxId + 1;
    
    $newFood = [
        '@attributes' => [
            'id' => (string)$newId,
            'liik' => $foodData['liik'],
            'hind' => $foodData['hind']
        ],
        'nimetus' => $foodData['nimetus'],
        'koostis' => $foodData['koostis'],
        'kalorsus' => $foodData['kalorsus']
    ];
    
    $data['menyy']['tooted']['toit'][] = $newFood;
    
    return saveJsonData($jsonFile, $data);
}

function addOrderToJson($jsonFile, $orderData) {
    $data = loadJsonData($jsonFile);
    if (!$data) {
        return false;
    }
    
    $maxId = 1000;
    foreach ($data['tellimused']['tellimus'] as $tellimus) {
        $currentId = (int)$tellimus['@attributes']['id'];
        if ($currentId > $maxId) {
            $maxId = $currentId;
        }
    }
    $newId = $maxId + 1;
    
    $newOrder = [
        '@attributes' => [
            'id' => (string)$newId,
            'laud_id' => $orderData['laud_id'],
            'teenindaja_id' => $orderData['teenindaja_id'],
            'staatus' => $orderData['staatus'],
            'prioriteet' => $orderData['prioriteet']
        ],
        'tellimuse_aeg' => $orderData['tellimuse_aeg'],
        'kliendi_info' => [
            'nimi' => $orderData['klient_nimi'],
            'telefon' => $orderData['klient_telefon']
        ],
        'kogusumma' => $orderData['kogusumma']
    ];
    
    $data['tellimused']['tellimus'][] = $newOrder;
    
    return saveJsonData($jsonFile, $data);
}

function xmlToJson($xmlFile, $jsonFile) {
    $xml = simplexml_load_file($xmlFile);
    if (!$xml) {
        return false;
    }
    
    function xmlToArray($xmlObject, $out = []) {
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node) || is_array($node)) ? xmlToArray($node) : $node;
        }
        return $out;
    }
    
    $array = xmlToArray($xml);
    $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($jsonFile, $json);
    return $json;
}

if (empty($_GET) && empty($_POST)) {
    header('Location: ?format=html');
    exit;
}

if (isset($_GET['format']) && $_GET['format'] == 'html') {
    echo xmlToHtml($xmlFile);
    exit;
}

if (isset($_GET['format']) && $_GET['format'] == 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo xmlToJson($xmlFile, $jsonFile);
    exit;
}

echo showView('home');
?>
