<?php
$xmlFile = 'data/restoran.xml';
$jsonFile = 'data/restoran.json';

// Функция для генерации единой навигации
function generateNavigation($currentPage = '') {
    return '
    <style>
        .main-nav {
            background: linear-gradient(135deg, #007cba, #005a87);
            padding: 15px 0;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .nav-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            margin-right: 30px;
        }
        .nav-links {
            display: flex;
            gap: 0;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            margin: 2px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
            border: 2px solid transparent;
        }
        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        .nav-links a.active {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
        }
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
            }
            .nav-links {
                justify-content: center;
            }
        }
    </style>
    
    <nav class="main-nav">
        <div class="nav-container">
            <a href="?format=html" class="nav-brand">Restoran</a>
            <div class="nav-links">
                <a href="?format=html"' . ($currentPage == 'html' ? ' class="active"' : '') . '>Andmed</a>
                <a href="?format=json" target="_blank"' . ($currentPage == 'json' ? ' class="active"' : '') . '>JSON</a>
                <a href="?page=manage"' . ($currentPage == 'manage' ? ' class="active"' : '') . '>Haldamine</a>
            </div>
        </div>
    </nav>';
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

function sortFood($xml, $sortBy = '', $sortOrder = 'asc') {
    $foods = [];
    foreach ($xml->menyy->tooted->toit as $toit) {
        $foods[] = $toit;
    }
    
    if ($sortBy == 'hind') {
        usort($foods, function($a, $b) use ($sortOrder) {
            $priceA = (float)$a['hind'];
            $priceB = (float)$b['hind'];
            return $sortOrder == 'desc' ? $priceB <=> $priceA : $priceA <=> $priceB;
        });
    } elseif ($sortBy == 'kalorsus') {
        usort($foods, function($a, $b) use ($sortOrder) {
            $calA = (int)$a->kalorsus;
            $calB = (int)$b->kalorsus;
            return $sortOrder == 'desc' ? $calB <=> $calA : $calA <=> $calB;
        });
    } elseif ($sortBy == 'nimetus') {
        usort($foods, function($a, $b) use ($sortOrder) {
            $nameA = (string)$a->nimetus;
            $nameB = (string)$b->nimetus;
            return $sortOrder == 'desc' ? strcmp($nameB, $nameA) : strcmp($nameA, $nameB);
        });
    }
    
    return $foods;
}

function xmlToHtml($xmlFile) {
    $xml = simplexml_load_file($xmlFile);
    
    $orderStatus = isset($_GET['order_status']) ? $_GET['order_status'] : '';
    $foodSearch = isset($_GET['food_search']) ? $_GET['food_search'] : '';
    $tableStatus = isset($_GET['table_status']) ? $_GET['table_status'] : '';
    $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
    $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Restoran Data</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            h2 { color: #333; }
            .menu-section { margin: 30px 0; }
            .search-form { 
                background: #f9f9f9; 
                padding: 15px; 
                border: 1px solid #ddd; 
                border-radius: 5px; 
                margin: 20px 0; 
            }
            .form-group { 
                display: inline-block; 
                margin-right: 15px; 
                margin-bottom: 10px; 
            }
            label { font-weight: bold; margin-right: 5px; }
            input, select { 
                padding: 5px; 
                border: 1px solid #ccc; 
                border-radius: 3px; 
            }
            button { 
                background: #007cba; 
                color: white; 
                padding: 8px 15px; 
                border: none; 
                border-radius: 3px; 
                cursor: pointer; 
            }
            button:hover { background: #005a87; }
            .reset-btn { background: #6c757d; }
            .reset-btn:hover { background: #545b62; }
        </style>
    </head>
    <body>
        ' . generateNavigation('html') . '
        
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h1>Restorani Andmed</h1>
        
        <div class="search-form">
            <h3>Otsing ja sortimine</h3>
            <form method="GET">
                <input type="hidden" name="format" value="html">
                
                <div class="form-group">
                    <label for="food_search">Toidu nimi:</label>
                    <input type="text" id="food_search" name="food_search" 
                           value="' . htmlspecialchars($foodSearch) . '" 
                           placeholder="Sisesta toidu nimi...">
                </div>
                
                <div class="form-group">
                    <label for="order_status">Tellimuse staatus:</label>
                    <select id="order_status" name="order_status">
                        <option value="">Kõik</option>
                        <option value="ootel"' . ($orderStatus == 'ootel' ? ' selected' : '') . '>Ootel</option>
                        <option value="kinnitatud"' . ($orderStatus == 'kinnitatud' ? ' selected' : '') . '>Kinnitatud</option>
                        <option value="valmis"' . ($orderStatus == 'valmis' ? ' selected' : '') . '>Valmis</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="table_status">Laua staatus:</label>
                    <select id="table_status" name="table_status">
                        <option value="">Kõik</option>
                        <option value="vaba"' . ($tableStatus == 'vaba' ? ' selected' : '') . '>Vaba</option>
                        <option value="reserveeritud"' . ($tableStatus == 'reserveeritud' ? ' selected' : '') . '>Reserveeritud</option>
                        <option value="hõivatud"' . ($tableStatus == 'hõivatud' ? ' selected' : '') . '>Hõivatud</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="sort_by">Sortimise alus:</label>
                    <select id="sort_by" name="sort_by">
                        <option value="">Tavaline</option>
                        <option value="hind"' . ($sortBy == 'hind' ? ' selected' : '') . '>Hind</option>
                        <option value="kalorsus"' . ($sortBy == 'kalorsus' ? ' selected' : '') . '>Kalorid</option>
                        <option value="nimetus"' . ($sortBy == 'nimetus' ? ' selected' : '') . '>Nimi</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="sort_order">Järjekord:</label>
                    <select id="sort_order" name="sort_order">
                        <option value="asc"' . ($sortOrder == 'asc' ? ' selected' : '') . '>Kasvav</option>
                        <option value="desc"' . ($sortOrder == 'desc' ? ' selected' : '') . '>Kahanev</option>
                    </select>
                </div>
                
                <button type="submit">Otsi</button>
                <button type="button" class="reset-btn" onclick="window.location.href=\'?format=html\'">Lähtesta</button>
            </form>
        </div>
        
        <script>
            document.querySelectorAll("select").forEach(function(select) {
                select.addEventListener("change", function() {
                    if (this.value !== "") {
                        this.form.submit();
                    }
                });
            });
            
            document.querySelectorAll("table tr").forEach(function(row) {
                row.addEventListener("mouseenter", function() {
                    this.style.backgroundColor = "#f5f5f5";
                });
                row.addEventListener("mouseleave", function() {
                    this.style.backgroundColor = "";
                });
            });
        </script>';
    
    $filteredFoods = searchFoodByName($xml, $foodSearch);
    if (!empty($sortBy)) {
        $tempXml = new SimpleXMLElement('<root><menyy><tooted></tooted></menyy></root>');
        foreach ($filteredFoods as $food) {
            $tempFood = $tempXml->menyy->tooted->addChild('toit');
            foreach ($food->attributes() as $key => $value) {
                $tempFood->addAttribute($key, $value);
            }
            foreach ($food->children() as $child) {
                $tempFood->addChild($child->getName(), (string)$child);
            }
        }
        $sortedFoods = sortFood($tempXml, $sortBy, $sortOrder);
    } else {
        $sortedFoods = $filteredFoods;
    }
    
    $html .= '<div class="menu-section">
        <h2>Toidud (' . count($sortedFoods) . ' tulemust)</h2>
        <table>
            <tr>
            <th>Nimetus</th>
            <th>Liik</th>
            <th>Hind</th>
            <th>Koostis</th>
            <th>Kalorid</th></tr>';

    foreach ($sortedFoods as $toit) {
        $html .= '<tr>
            <td>' . (string)$toit->nimetus . '</td>
            <td>' . (string)$toit['liik'] . '</td>
            <td>€' . (string)$toit['hind'] . '</td>
            <td>' . (string)$toit->koostis . '</td>
            <td>' . (string)$toit->kalorsus . '</td>
        </tr>';
    }
    $html .= '</table></div>';
    
    $html .= '<div class="menu-section">
        <h2>Joogid</h2>
        <table>
            <tr>
            <th>Nimetus</th>
            <th>Liik</th>
            <th>Maht</th>
            <th>Tootja</th>
            <th>Alkohol %</th>
            <th>Hind</th>
            </tr>';

    foreach ($xml->menyy->tooted->jook as $jook) {
        $html .= '<tr>
            <td>' . (string)$jook->nimetus . '</td>
            <td>' . (string)$jook['liik'] . '</td>
            <td>' . (string)$jook['maht'] . 'L</td>
            <td>' . (string)$jook->tootja . '</td>
            <td>' . (string)$jook->alkoholiProtsent . '</td>
            <td>€' . (string)$jook->hind . '</td>
        </tr>';
    }
    $html .= '</table></div>';
    
    $html .= '<div class="menu-section">
        <h2>Teenindus</h2>';

    $filteredTables = searchTablesByStatus($xml, $tableStatus);
    
    $html .= '<h4>Lauad (' . count($filteredTables) . ' tulemust)</h4>
        <table>
            <tr>
            <th>Kohtade arv</th>
            <th>Staatus</th>
            <th>Asukoht</th>
            <th>Reserveeritud</th>
            <th>Erinoued</th></tr>';

    foreach ($filteredTables as $laud) {
        $html .= '<tr>
            <td>' . (string)$laud['kohtade_arv'] . '</td>
            <td>' . (string)$laud['staatus'] . '</td>
            <td>' . (string)$laud['asukoht'] . '</td>
            <td>' . (string)$laud->reserveeritud . '</td>
            <td>' . (string)$laud->erinoued . '</td>
        </tr>';
    }
    $html .= '</table>';
    
    $html .= '<h4>Teenindajad</h4>
        <table>
            <tr>
            <th>Nimi</th>
            <th>Telefon</th>
            <th>Staatus</th>
            <th>Kogemus</th>
            <th>Tööaeg</th></tr>';

    foreach ($xml->teenindus->teenindajad->teenindaja as $teenindaja) {
        $html .= '<tr>
            <td>' . (string)$teenindaja->nimi . '</td>
            <td>' . (string)$teenindaja->telefon . '</td>
            <td>' . (string)$teenindaja['staatus'] . '</td>
            <td>' . (string)$teenindaja['kogemus'] . ' aastad</td>
            <td>' . (string)$teenindaja->tööaeg . '</td>
        </tr>';
    }
    $html .= '</table></div>';
    
    $filteredOrders = searchOrdersByStatus($xml, $orderStatus);
    
    $html .= '<div class="menu-section">
        <h2>Tellimused (' . count($filteredOrders) . ' tulemust)</h2>
        <table>
            <tr>
            <th>Laud</th>
            <th>Teenindaja</th>
            <th>Klient</th>
            <th>Telefon</th>
            <th>Aeg</th>
            <th>Staatus</th>
            <th>Prioriteet</th>
            <th>Summa</th>
            </tr>';

    foreach ($filteredOrders as $tellimus) {
        $html .= '<tr>
            <td>' . (string)$tellimus['laud_id'] . '</td>
            <td>' . (string)$tellimus['teenindaja_id'] . '</td>
            <td>' . (string)$tellimus->kliendi_info->nimi . '</td>
            <td>' . (string)$tellimus->kliendi_info->telefon . '</td>
            <td>' . (string)$tellimus->tellimuse_aeg . '</td>
            <td><strong>' . (string)$tellimus['staatus'] . '</strong></td>
            <td>' . (string)$tellimus['prioriteet'] . '</td>
            <td>€' . (string)$tellimus->kogusumma . '</td>
        </tr>';
    }
    $html .= '</table></div>';
    
    $html .= '    </div>
    </body></html>';
    return $html;
}

// Функция для загрузки JSON данных
function loadJsonData($jsonFile) {
    if (!file_exists($jsonFile)) {
        return false;
    }
    $jsonContent = file_get_contents($jsonFile);
    return json_decode($jsonContent, true);
}

// Функция для сохранения JSON данных
function saveJsonData($jsonFile, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($jsonFile, $json) !== false;
}

// Функция для добавления новой еды в JSON
function addFoodToJson($jsonFile, $foodData) {
    $data = loadJsonData($jsonFile);
    if (!$data) {
        return false;
    }
    
    // Генерируем новый ID
    $maxId = 100;
    foreach ($data['menyy']['tooted']['toit'] as $toit) {
        $currentId = (int)$toit['@attributes']['id'];
        if ($currentId > $maxId) {
            $maxId = $currentId;
        }
    }
    $newId = $maxId + 1;
    
    // Создаем новый элемент еды
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
    
    // Добавляем в массив
    $data['menyy']['tooted']['toit'][] = $newFood;
    
    return saveJsonData($jsonFile, $data);
}

// Функция для добавления нового заказа в JSON
function addOrderToJson($jsonFile, $orderData) {
    $data = loadJsonData($jsonFile);
    if (!$data) {
        return false;
    }
    
    // Генерируем новый ID заказа
    $maxId = 1000;
    foreach ($data['tellimused']['tellimus'] as $tellimus) {
        $currentId = (int)$tellimus['@attributes']['id'];
        if ($currentId > $maxId) {
            $maxId = $currentId;
        }
    }
    $newId = $maxId + 1;
    
    // Создаем новый заказ
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
    
    // Добавляем в массив
    $data['tellimused']['tellimus'][] = $newOrder;
    
    return saveJsonData($jsonFile, $data);
}

// Функция для обновления статуса заказа в JSON
function updateOrderStatus($jsonFile, $orderId, $newStatus) {
    $data = loadJsonData($jsonFile);
    if (!$data) {
        return false;
    }
    
    // Ищем заказ по ID
    for ($i = 0; $i < count($data['tellimused']['tellimus']); $i++) {
        if ($data['tellimused']['tellimus'][$i]['@attributes']['id'] == $orderId) {
            $data['tellimused']['tellimus'][$i]['@attributes']['staatus'] = $newStatus;
            return saveJsonData($jsonFile, $data);
        }
    }
    
    return false;
}

function xmlToJson($xmlFile, $jsonFile) {
    $xml = simplexml_load_file($xmlFile);
    if (!$xml) {
        return false;
    }
    
    // Улучшенная конвертация XML в JSON с правильной структурой
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

// Автоматическое перенаправление на HTML страницу если нет параметров
if (empty($_GET) && empty($_POST)) {
    header('Location: ?format=html');
    exit;
}

// Обработка POST запросов для добавления данных
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_food':
                $foodData = [
                    'nimetus' => $_POST['nimetus'],
                    'liik' => $_POST['liik'],
                    'hind' => $_POST['hind'],
                    'koostis' => $_POST['koostis'],
                    'kalorsus' => $_POST['kalorsus']
                ];
                
                if (addFoodToJson($jsonFile, $foodData)) {
                    // Обновляем XML файл из JSON (синхронизация)
                    xmlToJson($xmlFile, $jsonFile);
                    $message = 'Toit edukalt lisatud!';
                    $messageType = 'success';
                } else {
                    $message = 'Viga toidu lisamisel!';
                    $messageType = 'error';
                }
                break;
                
            case 'add_order':
                $orderData = [
                    'laud_id' => $_POST['laud_id'],
                    'teenindaja_id' => $_POST['teenindaja_id'],
                    'staatus' => $_POST['staatus'],
                    'prioriteet' => $_POST['prioriteet'],
                    'tellimuse_aeg' => $_POST['tellimuse_aeg'],
                    'klient_nimi' => $_POST['klient_nimi'],
                    'klient_telefon' => $_POST['klient_telefon'],
                    'kogusumma' => $_POST['kogusumma']
                ];
                
                if (addOrderToJson($jsonFile, $orderData)) {
                    $message = 'Tellimus edukalt lisatud!';
                    $messageType = 'success';
                } else {
                    $message = 'Viga tellimuse lisamisel!';
                    $messageType = 'error';
                }
                break;
                
            case 'update_status':
                $orderId = $_POST['order_id'];
                $newStatus = $_POST['new_status'];
                
                if (updateOrderStatus($jsonFile, $orderId, $newStatus)) {
                    $message = 'Tellimuse staatus edukalt uuendatud!';
                    $messageType = 'success';
                } else {
                    $message = 'Viga staatuse uuendamisel!';
                    $messageType = 'error';
                }
                break;
        }
    }
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

if (isset($_GET['page']) && $_GET['page'] == 'manage') {
    // Страница управления данными
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Andmete haldamine</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; }
            .card { 
                background: white; 
                padding: 20px; 
                margin: 20px 0; 
                border-radius: 8px; 
                box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-weight: bold; }
            input, select, textarea { 
                width: 100%; 
                padding: 8px; 
                border: 1px solid #ddd; 
                border-radius: 4px; 
                box-sizing: border-box; 
            }
            button { 
                background: #007cba; 
                color: white; 
                padding: 10px 20px; 
                border: none; 
                border-radius: 4px; 
                cursor: pointer; 
                margin-right: 10px;
            }
            button:hover { background: #005a87; }
            .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; }
            .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; }
            .nav { margin-bottom: 20px; }
            .nav a { 
                display: inline-block; 
                padding: 10px 15px; 
                margin-right: 10px; 
                background: #6c757d; 
                color: white; 
                text-decoration: none; 
                border-radius: 4px; 
            }
            .nav a:hover { background: #545b62; }
            .two-column { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        </style>
    </head>
    <body>
        ' . generateNavigation('manage') . '
        
        <div class="container">
            <h1>Restorani andmete haldamine</h1>';
    
    if ($message) {
        echo '<div class="' . $messageType . '">' . htmlspecialchars($message) . '</div>';
    }
    
    echo '<div class="two-column">
                <div class="card">
                    <h2>Lisa uus toit</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_food">
                        
                        <div class="form-group">
                            <label for="nimetus">Toidu nimi:</label>
                            <input type="text" id="nimetus" name="nimetus" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="liik">Liik:</label>
                            <select id="liik" name="liik" required>
                                <option value="">Vali liik</option>
                                <option value="eelroog">Eelroog</option>
                                <option value="peablokk">Peablokk</option>
                                <option value="magustoit">Magustoit</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="hind">Hind (€):</label>
                            <input type="number" id="hind" name="hind" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="koostis">Koostis:</label>
                            <textarea id="koostis" name="koostis" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="kalorsus">Kalorid:</label>
                            <input type="number" id="kalorsus" name="kalorsus" min="0" required>
                        </div>
                        
                        <button type="submit">Lisa toit</button>
                    </form>
                </div>
                
                <div class="card">
                    <h2>Lisa uus tellimus</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_order">
                        
                        <div class="form-group">
                            <label for="laud_id">Laua ID:</label>
                            <input type="number" id="laud_id" name="laud_id" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="teenindaja_id">Teenindaja ID:</label>
                            <input type="number" id="teenindaja_id" name="teenindaja_id" min="500" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="klient_nimi">Kliendi nimi:</label>
                            <input type="text" id="klient_nimi" name="klient_nimi" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="klient_telefon">Kliendi telefon:</label>
                            <input type="tel" id="klient_telefon" name="klient_telefon" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="staatus">Staatus:</label>
                            <select id="staatus" name="staatus" required>
                                <option value="">Vali staatus</option>
                                <option value="ootel">Ootel</option>
                                <option value="kinnitatud">Kinnitatud</option>
                                <option value="valmis">Valmis</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="prioriteet">Prioriteet:</label>
                            <select id="prioriteet" name="prioriteet" required>
                                <option value="">Vali prioriteet</option>
                                <option value="tavaline">Tavaline</option>
                                <option value="kire">Kiire</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tellimuse_aeg">Tellimuse aeg (HH:MM:SS):</label>
                            <input type="time" id="tellimuse_aeg" name="tellimuse_aeg" step="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kogusumma">Kogusumma (€):</label>
                            <input type="number" id="kogusumma" name="kogusumma" step="0.01" min="0" required>
                        </div>
                        
                        <button type="submit">Lisa tellimus</button>
                    </form>
                </div>
            </div>';
    
    // Форма для обновления статуса заказа
    $jsonData = loadJsonData($jsonFile);
    if ($jsonData && isset($jsonData['tellimused']['tellimus'])) {
        echo '<div class="card">
                <h2>Uuenda tellimuse staatust</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_status">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="order_id">Tellimuse ID:</label>
                            <select id="order_id" name="order_id" required>
                                <option value="">Vali tellimus</option>';
        
        foreach ($jsonData['tellimused']['tellimus'] as $tellimus) {
            $orderId = $tellimus['@attributes']['id'];
            $currentStatus = $tellimus['@attributes']['staatus'];
            $clientName = isset($tellimus['kliendi_info']['nimi']) ? $tellimus['kliendi_info']['nimi'] : 'Teadmata';
            echo '<option value="' . $orderId . '">ID: ' . $orderId . ' - ' . $clientName . ' (' . $currentStatus . ')</option>';
        }
        
        echo '          </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_status">Uus staatus:</label>
                            <select id="new_status" name="new_status" required>
                                <option value="">Vali staatus</option>
                                <option value="ootel">Ootel</option>
                                <option value="kinnitatud">Kinnitatud</option>
                                <option value="valmis">Valmis</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit">Uuenda staatus</button>
                        </div>
                    </div>
                </form>
            </div>';
    }
    
    echo '</div>
        </body>
    </html>';
    exit;
}

echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Restoran - Pealeht</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .hero { 
            background: linear-gradient(135deg, #007cba, #005a87); 
            color: white; 
            padding: 60px 0; 
            text-align: center; 
            margin-bottom: 40px;
        }
        .hero h1 { font-size: 3em; margin: 0 0 20px 0; }
        .hero p { font-size: 1.2em; margin: 0; opacity: 0.9; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); }
        .card-icon { font-size: 3em; margin-bottom: 20px; }
        .card h3 { color: #007cba; margin-bottom: 15px; }
        .card p { color: #666; line-height: 1.6; margin-bottom: 25px; }
        .btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #007cba; 
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            transition: background 0.3s ease;
        }
        .btn:hover { background: #005a87; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    ' . generateNavigation('home') . '
</html>';
?>
