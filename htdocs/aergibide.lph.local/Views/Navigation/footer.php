<?php
$user = $_SESSION["user"] ?? "";
if (DEBUG_MODE){
    $version = getBuildInfo()["buildName"] ." - ".$user->getUsername() ?? WEB_APP_NAME.' v0.0.0';
} else {
    $version = "&#169 Los Pollos Hermanos";
}

if (!isset($importsJs)){
    $importsJs = [];
}


?>

<footer class="pie">

    <ul class="fotoI listas">
        <li><img src="/assets/img/lph-logo.png" alt="Logo" ></li>
        <li class="version"><p> <?= $version ?>  </p></li>
    </ul>

    <ul class="listaD listas">
        <li><a class="navLink" href="#">Pagina web</a></li>
        <li><a class="navLink" href="#">Redes sociales</a></li>
        <li><a class="navLink" href="#">Contacto</a></li>
    </ul>

</footer>

</div>
<?php
foreach ($importsJs as $import){
    echo "<script src='$import'></script>";
}
?>
<script src="//cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro-v6@44659d9/css/all.min.css" rel="stylesheet" type="text/css"/>
</body>
</html>