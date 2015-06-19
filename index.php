<?php
	//TODO: use an autoloader
	include('Classes\Yogho.php');
	include('Classes\Offsets.php');

	include('Classes\Palette.php');

	// Show the palette for the title screens
	$palette = new Yogho\Palette;
	$palette->toImage();