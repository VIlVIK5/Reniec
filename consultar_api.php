<?php
echo "=== CONSULTA DE DNI (API V2) ===\n";
echo "Introduce el DNI a consultar: ";
$dni = trim(fgets(STDIN));

if (strlen($dni) !== 8 || !is_numeric($dni)) {
    echo "Error: Debes ingresar un número de DNI válido de 8 dígitos.\n";
    exit;
}

// Asegúrate de colocar tu Token real obtenido en https://apis.net.pe/
$token = "TU_TOKEN_AQUI"; 
// Endpoint actualizado a la versión 2
$url = "https://api.apis.net.pe/v2/reniec/dni?numero=" . $dni;

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
    echo "\n¡Consulta exitosa!\n";
    $data = json_decode($response, true);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "\nError en la consulta (Código HTTP: $httpCode).\n";
    if (!empty($errorMsg)) {
        echo "Error técnico cURL: " . $errorMsg . "\n";
    }
    echo "Respuesta del servidor: " . $response . "\n";
    echo "Verifica que tu Token sea correcto y esté activo.\n";
}
?>