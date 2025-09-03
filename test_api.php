<?php

/**
 * Test básico de las APIs del Módulo 3 - POS
 * SmartKet v4 ERP
 */

// Test de endpoint público
echo "=== TESTING SmartKet v4 APIs ===\n\n";

function testEndpoint($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    echo "Testing: $url\n";
    echo "HTTP Code: $httpCode\n";
    
    if ($error) {
        echo "Error: $error\n";
    } else {
        $decoded = json_decode($response, true);
        if ($decoded) {
            echo "Response: " . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "Response: $response\n";
        }
    }
    echo "---\n\n";
}

// Test endpoints públicos
testEndpoint('http://localhost:8000/api/public/health');
testEndpoint('http://localhost:8000/api/public/info');

// Test endpoints que requieren autenticación (deberían devolver 401)
testEndpoint('http://localhost:8000/api/categorias');
testEndpoint('http://localhost:8000/api/productos');
testEndpoint('http://localhost:8000/api/clientes');

echo "=== TEST COMPLETED ===\n";
