<?php
include('Classes\Yogho.php');
include('Classes\Offsets.php');
include('Classes\Palette.php');

// Palette
$paletteModel = new \Yogho\Palette('1');
$palette = $paletteModel->palette;

$handle = fopen('YOGHODAT.DAT', 'rb');
fseek($handle, 13164);

$orderLevels1Through3 = [
    0, 4, 8, 12, 16,
    1, 5, 9, 13, 17,
    2, 6, 10, 14, 18,
    3, 7, 11, 15
];
$orderLevel4 = [
    0, 4, 8, 12, 16,
    1, 5, 9, 13, 17,
    2, 6, 10, 14,
    3, 7, 11, 15
];
$orderLevel5 = [
    0, 4, 8, 12, 16,
    1, 5, 9, 13, 17,
    2, 6, 10, 14, 18,
    3, 7, 11, 15
];
$orderLevel6 = [
    0, 4, 8, 12,
    1, 5, 9, 13,
    2, 6, 10, 14,
    3, 7, 11,
];

$levels = [
    1 => [
        'w' => 19,
        'h' => 17,
        'order' => $orderLevels1Through3,
    ],
    2 => [
        'w' => 19,
        'h' => 17,
        'order' => $orderLevels1Through3,
    ],
    3 => [
        'w' => 19,
        'h' => 17,
        'order' => $orderLevels1Through3,
    ],
    4 => [
        'w' => 18,
        'h' => 16,
        'order' => $orderLevel4,
    ],
    5 => [
        'w' => 19,
        'h' => 16,
        'order' => $orderLevel5,
    ],
    6 => [
        'w' => 16,
        'h' => 17,
        'order' => $orderLevel6,
    ],
];

$width = 0;
$height = 0;
foreach ($levels as $level) {
    $width += $level['w'];
    $height = max($height, $level['h']);
}
$plaatje = imagecreatetruecolor($width, $height);
imagecolortransparent($plaatje, imagecolorallocate($plaatje, $palette[0][0], $palette[0][1], $palette[0][2]));

$baseX = 0;
foreach ($levels as $level) {
    foreach ($level['order'] as $x) {
        for ($y = 0; $y < $level['h']; $y++) {
            $n = ord(fread($handle, 1));
            $col = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
            imagesetpixel($plaatje, $baseX + $x, $y, $col);
        }
    }

    $baseX += $level['w'];
}


fclose($handle);

header("Content-type: image/png");
imagepng($plaatje);


/*
$baseX = 0;
foreach ($wh as list($w, $h)) {
    $x = 0;
    for ($i = 0; $i < $w; $i++) {
        for ($y = 0; $y < $h; $y++) {
            $n = ord(fread($handle, 1));
            $col = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
            imagesetpixel($plaatje, $baseX + $x, $y, $col);
        }

        $x += 4;
        if ($x >= $w) {
            $x = $x % $w;
            if ($x === 0) {
                $x = 1;
            }
        }
    }

    $baseX += $w;
}*/
