<?php
echo "=== CONSULTA DIRECTA DE DNI E RUC (PHP) ===\n";
echo "Introduce o número (8 díxitos para DNI ou 11 para RUC): ";
$numero = trim(fgets(STDIN));

$longitud = strlen($numero);
if ($longitud !== 11 && $longitud !== 8) {
    echo "Erro: O número debe ser de 8 díxitos (DNI) ou 11 díxitos (RUC).\n";
    exit;
}

// Seleccionamos a ruta oficial dispoñible para consultas en liña
if ($longitud === 11) {
    $url = "https://api.apis.net.pe/v1/ruc?numero=" . $numero;
    echo "Consultando RUC no servidor...\n";
} else {
    $url = "https://api.apis.net.pe/v1/dni?numero=" . $numero;
    echo "Consultando DNI no servidor...\n";
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// Cabeceiras para simular un navegador web lexímo e evitar bloqueos perimetrais
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/json",
    "Referer: https://apis.net.pe/"
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errorMsg = curl_error($ch);
curl_close($ch);

if ($httpCode === 200 && !empty($response)) {
    echo "\n[✔] ¡Consulta realizada con éxito!\n";
    $data = json_decode($response, true);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "\n[✖] Non se puido procesar a solicitude (Código HTTP: $httpCode).\n";
    if (!empty($errorMsg)) {
        echo "Detalle técnico cURL: $errorMsg\n";
    }
    echo "Resposta do servidor: " . $response . "\n";
    echo "Consello: Se o servidor rexeita a conexión, asegúrate de ter conexión a internet activa ou proba nun intre.\n";
}
?>