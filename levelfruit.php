<?php
include('Classes\Yogho.php');
include('Classes\Offsets.php');
include('Classes\Palette.php');

// Palette
$paletteModel = new \Yogho\Palette('1');
$palette = $paletteModel->palette;

$handle = fopen('YOGHODAT.DAT', 'rb');
fseek($handle, 13164);

$wh = [
    1 => [19, 17],
    2 => [19, 17],
    3 => [19, 17],
    4 => [18, 16],
    5 => [19, 16],
    6 => [16, 17],
];

$w1 = 19;
$h1 = 17;

$w2 = 18;
$h2 = 16;

$w3 = 15;
$h3 = 17;

$levels = 6;

$plaatje = imagecreatetruecolor(($w1 * 3) + ($w2 * 3), max($h1, $h2));
imagecolortransparent($plaatje, imagecolorallocate($plaatje, $palette[0][0], $palette[0][1], $palette[0][2]));
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


// This works perfectly
for ($l = 0; $l < 3; $l++) {
    $baseX = $l * $w1;

    $xs = [
        0, 4, 8, 12, 16,
        1, 5, 9, 13, 17,
        2, 6, 10, 14, 18,
        3, 7, 11, 15
    ];
    foreach ($xs as $x) {
        for ($y = 0; $y < $h1; $y++) {
            $n = ord(fread($handle, 1));
            $col = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
            imagesetpixel($plaatje, $baseX + $x, $y, $col);
        }
    }
}


// This works for l=3, but not 4 or 5
$l = 3;
$baseX = (3 * $w1);

$xs = [
    0, 4, 8, 12, 16,
    1, 5, 9, 13, 17,
    2, 6, 10, 14,
    3, 7, 11, 15
];
foreach ($xs as $x) {
    for ($y = 0; $y < $h2; $y++) {
        $n = ord(fread($handle, 1));
        $col = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
        imagesetpixel($plaatje, $baseX + $x, $y, $col);
    }
}


// This works for l=4
$l = 4;
$baseX = (3 * $w1) + $w2;

$xs = [
    0, 4, 8, 12, 16,
    1, 5, 9, 13, 17,
    2, 6, 10, 14, 18,
    3, 7, 11, 15
];
foreach ($xs as $x) {
    for ($y = 0; $y < $h2; $y++) {
        $n = ord(fread($handle, 1));
        $col = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
        imagesetpixel($plaatje, $baseX + $x, $y, $col);
    }
}


// This works for l=4
$l = 5;
$baseX = (3 * $w1) + $w2 + 19;

$xs = [
    0, 4, 8, 12,
    1, 5, 9, 13,
    2, 6, 10, 14,
    3, 7, 11,
];
foreach ($xs as $x) {
    for ($y = 0; $y < $h3; $y++) {
        $n = ord(fread($handle, 1));
        $col = imagecolorallocate($plaatje, $palette[$n][0], $palette[$n][1], $palette[$n][2]);
        imagesetpixel($plaatje, $baseX + $x, $y, $col);
    }
}


fclose($handle);

header("Content-type: image/png");
imagepng($plaatje);
