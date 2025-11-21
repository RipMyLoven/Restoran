<?php
$xmlFile = 'data/restoran.xml';
$jsonFile = 'data/restoran.json';

function generateNavigation($currentPage = '') {
    return '
    
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
    

    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Restoran Data</title>
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body class="data-view">
        ' . generateNavigation('html') . '
        
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h1>Restorani Andmed</h1>';

    
    $html .= '<div class="menu-section">
        <h2>Toidud</h2>
        
        <div class="table-controls">
            <input type="text" class="search-input" id="food-search" placeholder="Otsi toitu..." onkeyup="filterTable(\'food-table\', this.value)">
        </div>
        
        <table id="food-table">
            <thead>
                <tr>
                    <th onclick="sortTable(\'food-table\', \'0\')">Nimetus <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'food-table\', \'1\')">Liik <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'food-table\', \'2\')">Hind <span class="sort-indicator"></span></th>
                    <th>Koostis</th>
                    <th onclick="sortTable(\'food-table\', \'4\')">Kalorid <span class="sort-indicator"></span></th>
                </tr>
            </thead>
            <tbody>';

    foreach ($xml->menyy->tooted->toit as $toit) {
        $html .= '<tr>
            <td>' . htmlspecialchars((string)$toit->nimetus) . '</td>
            <td>' . htmlspecialchars((string)$toit['liik']) . '</td>
            <td data-sort="' . (float)$toit['hind'] . '">€' . htmlspecialchars((string)$toit['hind']) . '</td>
            <td>' . htmlspecialchars((string)$toit->koostis) . '</td>
            <td data-sort="' . (int)$toit->kalorsus . '">' . htmlspecialchars((string)$toit->kalorsus) . '</td>
        </tr>';
    }
    $html .= '</tbody></table></div>';
    
    $html .= '<div class="menu-section">
        <h2>Joogid</h2>
        
        <div class="table-controls">
            <input type="text" class="search-input" id="drinks-search" placeholder="Otsi jooki..." onkeyup="filterTable(\'drinks-table\', this.value)">
        </div>
        
        <table id="drinks-table">
            <thead>
                <tr>
                    <th onclick="sortTable(\'drinks-table\', \'0\')">Nimetus <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'drinks-table\', \'1\')">Liik <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'drinks-table\', \'2\')">Maht <span class="sort-indicator"></span></th>
                    <th>Tootja</th>
                    <th>Alkohol %</th>
                    <th onclick="sortTable(\'drinks-table\', \'5\')">Hind <span class="sort-indicator"></span></th>
                </tr>
            </thead>
            <tbody>';

    foreach ($xml->menyy->tooted->jook as $jook) {
        $html .= '<tr>
            <td>' . htmlspecialchars((string)$jook->nimetus) . '</td>
            <td>' . htmlspecialchars((string)$jook['liik']) . '</td>
            <td data-sort="' . (float)$jook['maht'] . '">' . htmlspecialchars((string)$jook['maht']) . 'L</td>
            <td>' . htmlspecialchars((string)$jook->tootja) . '</td>
            <td>' . htmlspecialchars((string)$jook->alkoholiProtsent) . '</td>
            <td data-sort="' . (float)$jook->hind . '">€' . htmlspecialchars((string)$jook->hind) . '</td>
        </tr>';
    }
    $html .= '</tbody></table></div>';
    
    $html .= '<div class="menu-section">
        <h2>Teenindus</h2>
        
        <h4>Lauad</h4>
        <table id="tables-table">
            <thead>
                <tr>
                    <th onclick="sortTable(\'tables-table\', \'0\')">Kohtade arv <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'tables-table\', \'1\')">Staatus <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'tables-table\', \'2\')">Asukoht <span class="sort-indicator"></span></th>
                    <th>Reserveeritud</th>
                    <th>Erinoued</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($xml->teenindus->lauad->laud as $laud) {
        $html .= '<tr>
            <td data-sort="' . (int)$laud['kohtade_arv'] . '">' . htmlspecialchars((string)$laud['kohtade_arv']) . '</td>
            <td>' . htmlspecialchars((string)$laud['staatus']) . '</td>
            <td>' . htmlspecialchars((string)$laud['asukoht']) . '</td>
            <td>' . htmlspecialchars((string)$laud->reserveeritud) . '</td>
            <td>' . htmlspecialchars((string)$laud->erinoued) . '</td>
        </tr>';
    }
    $html .= '</tbody></table>
    
        <h4>Teenindajad</h4>
        

        
        <table id="staff-table">
            <thead>
                <tr>
                    <th onclick="sortTable(\'staff-table\', \'0\')">Nimi <span class="sort-indicator"></span></th>
                    <th>Telefon</th>
                    <th onclick="sortTable(\'staff-table\', \'2\')">Staatus <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'staff-table\', \'3\')">Kogemus <span class="sort-indicator"></span></th>
                    <th>Tööaeg</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($xml->teenindus->teenindajad->teenindaja as $teenindaja) {
        $html .= '<tr>
            <td>' . htmlspecialchars((string)$teenindaja->nimi) . '</td>
            <td>' . htmlspecialchars((string)$teenindaja->telefon) . '</td>
            <td>' . htmlspecialchars((string)$teenindaja['staatus']) . '</td>
            <td data-sort="' . (int)$teenindaja['kogemus'] . '">' . htmlspecialchars((string)$teenindaja['kogemus']) . ' aastad</td>
            <td>' . htmlspecialchars((string)$teenindaja->tööaeg) . '</td>
        </tr>';
    }
    $html .= '</tbody></table></div>';
    
    $html .= '
    <div class="menu-section">
        <h2>Tellimused</h2>
        

        
        <table id="orders-table">
            <thead>
                <tr>
                    <th onclick="sortTable(\'orders-table\', \'0\')">Laud <span class="sort-indicator"></span></th>
                    <th>Teenindaja</th>
                    <th onclick="sortTable(\'orders-table\', \'2\')">Klient <span class="sort-indicator"></span></th>
                    <th>Telefon</th>
                    <th onclick="sortTable(\'orders-table\', \'4\')">Aeg <span class="sort-indicator"></span></th>
                    <th onclick="sortTable(\'orders-table\', \'5\')">Staatus <span class="sort-indicator"></span></th>
                    <th>Prioriteet</th>
                    <th onclick="sortTable(\'orders-table\', \'7\')">Summa <span class="sort-indicator"></span></th>
                </tr>
            </thead>
            <tbody>';

    foreach ($xml->tellimused->tellimus as $tellimus) {
        $html .= '<tr>
            <td data-sort="' . (int)$tellimus['laud_id'] . '">' . htmlspecialchars((string)$tellimus['laud_id']) . '</td>
            <td>' . htmlspecialchars((string)$tellimus['teenindaja_id']) . '</td>
            <td>' . htmlspecialchars((string)$tellimus->kliendi_info->nimi) . '</td>
            <td>' . htmlspecialchars((string)$tellimus->kliendi_info->telefon) . '</td>
            <td>' . htmlspecialchars((string)$tellimus->tellimuse_aeg) . '</td>
            <td><strong>' . htmlspecialchars((string)$tellimus['staatus']) . '</strong></td>
            <td>' . htmlspecialchars((string)$tellimus['prioriteet']) . '</td>
            <td data-sort="' . (float)$tellimus->kogusumma . '">€' . htmlspecialchars((string)$tellimus->kogusumma) . '</td>
        </tr>';
    }
    $html .= '</tbody></table></div>';
    
    $html .= '
        </div>
    <script src="/script.js"></script>
    <script>
        function filterTable(tableId, searchValue) {
            console.log("filterTable called:", tableId, searchValue);
            const table = document.getElementById(tableId);
            const tbody = table.querySelector("tbody");
            const rows = tbody.querySelectorAll("tr");
            
            searchValue = searchValue.toLowerCase();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
        
        const tableSortState = {};
        
        function sortTable(tableId, columnIndex) {
            console.log("sortTable called:", tableId, columnIndex);
            sortTableByHeader(tableId, parseInt(columnIndex));
        }
        
        function sortTableByHeader(tableId, columnIndex) {
            console.log("sortTableByHeader called:", tableId, columnIndex);
            const table = document.getElementById(tableId);
            const tbody = table.querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));
            
            if (!tableSortState[tableId]) {
                tableSortState[tableId] = {};
            }
            
            const currentSort = tableSortState[tableId][columnIndex];
            const isDesc = currentSort === "asc";
            tableSortState[tableId][columnIndex] = isDesc ? "desc" : "asc";
            
            table.querySelectorAll(".sort-indicator").forEach(indicator => {
                indicator.textContent = "";
            });
            
            const currentHeader = table.querySelector(`thead tr th:nth-child(${columnIndex + 1}) .sort-indicator`);
            if (currentHeader) {
                currentHeader.textContent = isDesc ? " ↓" : " ↑";
            }
            
            rows.sort((a, b) => {
                let aValue = a.cells[columnIndex].textContent.trim();
                let bValue = b.cells[columnIndex].textContent.trim();
                
                const aSort = a.cells[columnIndex].getAttribute("data-sort");
                const bSort = b.cells[columnIndex].getAttribute("data-sort");
                
                if (aSort && bSort) {
                    aValue = parseFloat(aSort);
                    bValue = parseFloat(bSort);
                } else {
                    const aClean = aValue.replace(/[€L,\\s]/g, "");
                    const bClean = bValue.replace(/[€L,\\s]/g, "");
                    
                    const aNum = parseFloat(aClean);
                    const bNum = parseFloat(bClean);
                    
                    if (!isNaN(aNum) && !isNaN(bNum)) {
                        aValue = aNum;
                        bValue = bNum;
                    }
                }
                
                let result;
                if (typeof aValue === "number" && typeof bValue === "number") {
                    result = aValue - bValue;
                } else {
                    result = aValue.toString().localeCompare(bValue.toString(), "et");
                }
                
                return isDesc ? -result : result;
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            console.log("DOM loaded, setting up tables...");
            document.querySelectorAll("th[onclick]").forEach(th => {
                console.log("Found sortable header:", th);
                th.style.cursor = "pointer";
                th.title = "Kliki sortimiseks";
            });
        });
    </script>
    </body></html>';
    return $html;
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

function updateOrderStatus($jsonFile, $orderId, $newStatus) {
    $data = loadJsonData($jsonFile);
    if (!$data) {
        return false;
    }
    
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
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Andmete haldamine</title>
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body class="manage-view">
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
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    ' . generateNavigation('home') . '
</html>';
?>
