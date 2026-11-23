<?php

require_once __DIR__ . '/app.php';

try {
    $db = new mysqli(
        APP_CONFIG['db_host'],
        APP_CONFIG['db_user'],
        APP_CONFIG['db_password'],
        APP_CONFIG['db_name'],
        APP_CONFIG['db_port']
    );
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('CrossFit: connexion MySQL impossible.');
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    exit(json_encode(['success' => false, 'message' => 'Base de donnees indisponible.']));
}
