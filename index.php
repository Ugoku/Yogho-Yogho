<?php
//TODO: use an autoloader
use Yogho\Level;

include('./Classes/Yogho.php');
include('./Classes/Offsets.php');

include('./Classes/Palette.php');
include('./Classes/Tiles.php');
include('./Classes/Blocks.php');
include('./Classes/Level.php');

$levels = [1, 2, 3, 4, 5, 6]; // Add 'start' for palette

$export = 'levels';

// Export all palettes
switch ($export) {
    case 'palettes':
        foreach ($levels as $level) {
            $palette = new Yogho\Palette($level);
            $palette->toImage($level);
        }
        break;
    case 'tiles':
        foreach ($levels as $level) {
            $tiles = new Yogho\Tiles($level);
            $tiles->toImage($level);
        }
        break;
    case 'blocks':
        foreach ($levels as $level) {
            $blocks = new Yogho\Blocks($level);
            $blocks->toImage($level);
        }
        break;
    case 'levels':
        foreach ($levels as $level) {
            $l = new Level($level);
            $l->toImage($level);
        }
        break;
}
