<?php
	include('Classes\Yogho.php');

	$handle = fopen(Yogho\Yogho::FILENAME, 'rb');
	$songs = [
		1986121 => 7396,
		1993517 => 5026,
		1998543 => 3874,
		2002417 => 4850,
		2007267 => 2180,
		2009447 => 2192,
		2011639 => 8246,
		2019885 => 6276,
	];

	$count = 1;
	foreach ($songs as $offset => $length) {
		fseek($handle, $offset);
		$buffer = fread($handle, $length);
		file_put_contents('music' . $count . '.s3m', $buffer);
		++$count;
	}

	fclose($handle);