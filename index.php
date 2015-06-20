<?php
	//TODO: use an autoloader
	include('Classes\Yogho.php');
	include('Classes\Offsets.php');

	include('Classes\Palette.php');
	include('Classes\Tiles.php');
	include('Classes\Blocks.php');

	// Show the palette for the title screens
	/*$palette = new Yogho\Palette;
	$palette->toImage();*/

	// Show the tiles for the first level
/*	$tiles = new Yogho\Tiles(1);
	$tiles->toImage();*/

// Show the blocks for the third level
	$blocks = new Yogho\Blocks(3);
	$blocks->toImage();