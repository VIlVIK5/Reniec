<?php
echo "=== CONSULTA DE RUC (API V2) ===\n";
echo "Introduce el número de RUC a consultar: ";
$ruc = trim(fgets(STDIN));

if (strlen($ruc) !== 11 || !is_numeric($ruc)) {
    echo "Error: Debes ingresar un número de RUC válido de 11 dígitos.\n";
    exit;
}

// Tu token integrado
$token = "sk_20157.d0VcNU0w70GtJLz7Q3zfttcv5dzvF7FW"; 
$url = "https://api.apis.net.pe/v2/sunat/ruc?numero=" . $ruc;

echo "Consultando servidor...\n";

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
    echo "\n¡Consulta de RUC exitosa!\n";
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