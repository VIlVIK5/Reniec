<?php
echo "=== CONSULTA AUTOMÁTICA DE RUC Y DNI ===\n";
echo "Introduce el número (DNI de 8 dígitos o RUC de 11 dígitos): ";
$numero = trim(fgets(STDIN));

$longitud = strlen($numero);
if ($longitud !== 11 && $longitud !== 8) {
    echo "Error: Debes ingresar un DNI de 8 dígitos o un RUC de 11 dígitos.\n";
    exit;
}

// Selecciona la URL dependiendo de si es RUC o DNI
if ($longitud === 11) {
    $url = "https://api.apis.net.pe/v2/sunat/ruc?numero=" . $numero;
    echo "Consultando RUC en SUNAT...\n";
} else {
    $url = "https://api.apis.net.pe/v2/reniec/dni?numero=" . $numero;
    echo "Consultando DNI en RENIEC...\n";
}

// Tu token integrado
$token = "sk_20157.d0VcNU0w70GtJLz7Q3zfttcv5dzvF7FW"; 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/json",
    "Authorization: Bearer " . $token
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errorMsg = curl_error($ch);
curl_close($ch);

if ($httpCode === 200) {
    echo "\n¡Consulta exitosa!\n";
    $data = json_decode($response, true);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "\nError en la consulta (Código HTTP: $httpCode).\n";
    if (!empty($errorMsg)) {
        echo "Error técnico cURL: " . $errorMsg . "\n";
    }
    echo "Respuesta del servidor: " . $response . "\n";
}
?>