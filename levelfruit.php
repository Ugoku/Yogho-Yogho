<?php
include('./Classes/Yogho.php');
include('./Classes/Offsets.php');
include('./Classes/Palette.php');

// Palette
$paletteModel = new \Yogho\Palette('1');
$palette = $paletteModel->palette;

$handle = fopen('YOGHODAT.DAT', 'rb');
fseek($handle, 13164);

/**
 * @return int[]
 */
function planarColumnOrder(int $width): array
{
    $order = [];
    for ($plane = 0; $plane < 4; $plane++) {
        for ($x = $plane; $x < $width; $x += 4) {
            $order[] = $x;
        }
    }
    return $order;
}

$levels = [
    1 => [
        'w' => 19,
        'h' => 17,
    ],
    2 => [
        'w' => 19,
        'h' => 17,
    ],
    3 => [
        'w' => 19,
        'h' => 17,
    ],
    4 => [
        'w' => 18,
        'h' => 16,
    ],
    5 => [
        'w' => 19,
        'h' => 16,
    ],
    6 => [
        'w' => 16,
        'h' => 17,
    ],
];

$width = 0;
$height = 0;
foreach ($levels as $level) {
    $width += $level['w'];
    $height = max($height, $level['h']);
}
$image = imagecreatetruecolor($width, $height);
imagecolortransparent($image, imagecolorallocate($image, $palette[0][0], $palette[0][1], $palette[0][2]));

$baseX = 0;
foreach ($levels as $level) {
    foreach (planarColumnOrder($level['w']) as $x) {
        for ($y = 0; $y < $level['h']; $y++) {
            $n = ord(fread($handle, 1));
            $col = imagecolorallocate($image, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
            imagesetpixel($image, $baseX + $x, $y, $col);
        }
    }

    $baseX += $level['w'];
}


fclose($handle);

if (php_sapi_name() === 'cli') {
    header('Content-type: image/png');
    imagepng($image, 'export/levelfruit.png');
} else {
    imagepng($image);
}
