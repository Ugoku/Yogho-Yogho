<?php
include('./Classes/Yogho.php');
include('./Classes/Offsets.php');
include('./Classes/Palette.php');

$width = 7;
$height = 9;
$glyphs = 40;
$level = '1'; // Either 'start' or '1' (or 2..6, but that has the same result)

$paletteModel = new \Yogho\Palette($level);
$palette = $paletteModel->palette;


$handle = fopen('YOGHODAT.DAT', 'rb');
$offset = ($level === 'start' ? 692593 : 695365);
fseek($handle, $offset);

header("Content-type: image/png");
$image = imagecreatetruecolor($width * $glyphs, $height);
imagefilledrectangle($image, 0, 0, $width, $height, 0xffffff);

for ($i = 0; $i < $glyphs; $i++) {
    $baseX = $i * $width;
    $xs = [0, 4, 1, 5, 2, 6, 3];
    foreach ($xs as $x) {
        for ($y = 0; $y < $height; $y++) {
            $n = ord(fread($handle, 1));
            if ($n > 0) {
                $thiscol = imagecolorallocate($image, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
                imagesetpixel($image, $baseX + $x, $y, $thiscol);
            } else {
                imagesetpixel($image, $baseX + $x, $y, 0xffffff);
            }
        }
    }

}
fclose($handle);
imagepng($image, 'font_' . $level . '.png');
