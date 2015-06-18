<?php
	$file = 'YOGHODAT.DAT';

	printf("%u\n", crc32(file_get_contents($file))); echo '<br>';
	echo md5_file($file) . '<br>';
	echo sha1_file($file) . '<br>';