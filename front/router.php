<?php
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$file = __DIR__ . $path;

if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $path) && file_exists($file)) {
    $mime_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml'
    ];
    
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mime = isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
    
    header('Content-Type: ' . $mime);
    readfile($file);
    return true;
}
return false;
?>
