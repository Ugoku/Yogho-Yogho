<?php
include('Classes\Yogho.php');
include('Classes\Offsets.php');
include('Classes\Palette.php');

$w = 7;
$h = 9;
$nr = 40;

$level = 'start';
// Palette
$paletteModel = new \Yogho\Palette($level);
$palette = $paletteModel->palette;


$handle = fopen('YOGHODAT.DAT', 'rb');
$offset = ($level === 'start' ? 692593 : 695365);
fseek($handle, $offset);

header("Content-type: image/png");
$plaatje = imagecreatetruecolor($w * $nr, $h);
imagefilledrectangle($plaatje, 0, 0, $w, $h, 0xffffff);

for ($i = 0; $i < $nr; $i++) {
    $baseX = $i * $w;
    $xs = [0, 4, 1, 5, 2, 6, 3];
    foreach ($xs as $x) {
        for ($y = 0; $y < $h; $y++) {
            $n = ord(fread($handle, 1));
            if ($n > 0) {
                $thiscol = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
                imagesetpixel($plaatje, $baseX + $x, $y, $thiscol);
            } else {
                imagesetpixel($plaatje, $baseX + $x, $y, 0xffffff);
            }
        }
    }

}
fclose($handle);
imagepng($plaatje);
