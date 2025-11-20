<?php
class RestoraniHaldus {
    private $xmlFile = 'data/restoran.xml';
    private $jsonFile = 'restoran.json';
    private $dom;
    
    public function __construct() {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
        if (file_exists($this->xmlFile)) {
            $this->dom->load($this->xmlFile);
        }
    }
    
    
    public function xmlToHtml() {
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
            </style>
        </head>
        <body>
            <h1>Restorani Andmed</h1>';
        
        $xpath = new DOMXPath($this->dom);
        
        $html .= '<h2>Menüü</h2><table><tr><th>ID</th><th>Nimetus</th><th>Hind</th><th>Liik</th></tr>';
        $toidud = $xpath->query('//toit');
        foreach ($toidud as $toit) {
            $html .= '<tr>
                <td>' . $toit->getAttribute('id') . '</td>
                <td>' . $toit->getElementsByTagName('nimetus')->item(0)->nodeValue . '</td>
                <td>€' . $toit->getAttribute('hind') . '</td>
                <td>' . $toit->getAttribute('liik') . '</td>
            </tr>';
        }
        $html .= '</table>';
        
        $html .= '<h2>Joogid</h2><table><tr><th>ID</th><th>Nimetus</th><th>Hind</th><th>Liik</th></tr>';
        $joogid = $xpath->query('//jook');
        foreach ($joogid as $jook) {
            $html .= '<tr>
                <td>' . $jook->getAttribute('id') . '</td>
                <td>' . $jook->getElementsByTagName('nimetus')->item(0)->nodeValue . '</td>
                <td>€' . $jook->getElementsByTagName('hind')->item(0)->nodeValue . '</td>
                <td>' . $jook->getAttribute('liik') . '</td>
            </tr>';
        }
        $html .= '</table>';
        
        $html .= '<h2>Tellimused</h2><table><tr><th>ID</th><th>Klient</th><th>Staatus</th><th>Summa</th></tr>';
        $tellimused = $xpath->query('//tellimus');
        foreach ($tellimused as $tellimus) {
            $html .= '<tr>
                <td>' . $tellimus->getAttribute('id') . '</td>
                <td>' . $tellimus->getElementsByTagName('nimi')->item(0)->nodeValue . '</td>
                <td>' . $tellimus->getAttribute('staatus') . '</td>
                <td>€' . $tellimus->getElementsByTagName('kogusumma')->item(0)->nodeValue . '</td>
            </tr>';
        }
        $html .= '</table></body></html>';
        
        return $html;
    }
    
    public function xmlToJson() {
        $xml_string = file_get_contents($this->xmlFile);
        $xml = simplexml_load_string($xml_string);
        $json = json_encode($xml, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        file_put_contents($this->jsonFile, $json);
        return $json;
    }
}

$restoran = new RestoraniHaldus();

echo "<h1>Restorani XML Konverterid</h1>";

if (isset($_GET['format']) && $_GET['format'] == 'html') {
    echo $restoran->xmlToHtml();
    exit;
}

if (isset($_GET['format']) && $_GET['format'] == 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo $restoran->xmlToJson();
    exit;
}

echo '<h2>Vali formaat:</h2>';
echo '<p><a href="?format=html">Näita HTML</a></p>';
echo '<p><a href="?format=json">Näita JSON</a></p>';
?>
