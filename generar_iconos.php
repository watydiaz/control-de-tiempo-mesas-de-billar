<?php
// Script para generar iconos PNG cuadrados desde img/logo.jpg
function crear_icono($origen, $destino, $tamano) {
    $src = imagecreatefromjpeg($origen);
    $w = imagesx($src);
    $h = imagesy($src);
    $min = min($w, $h);
    // Recortar a cuadrado centrado
    $x = ($w - $min) / 2;
    $y = ($h - $min) / 2;
    $cuadrado = imagecreatetruecolor($tamano, $tamano);
    // Fondo transparente
    imagesavealpha($cuadrado, true);
    $trans = imagecolorallocatealpha($cuadrado, 0, 0, 0, 127);
    imagefill($cuadrado, 0, 0, $trans);
    imagecopyresampled($cuadrado, $src, 0, 0, $x, $y, $tamano, $tamano, $min, $min);
    imagepng($cuadrado, $destino);
    imagedestroy($src);
    imagedestroy($cuadrado);
}
crear_icono('img/logo.jpg', 'img/logo-192.png', 192);
crear_icono('img/logo.jpg', 'img/logo-512.png', 512);
echo "Iconos generados: img/logo-192.png y img/logo-512.png";
