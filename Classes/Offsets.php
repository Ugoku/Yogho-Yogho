<?php
namespace Yogho;

class Offsets
{
	public static function getOffset($type, $level)
	{
		$offsets = [
			'palette' => [
				1 => 698137,
				2 => 806041,
				3 => 917849,
				4 => 1055513,
				5 => 1190937,
				6 => 1321049,
				'bonus1' => 1463897,
				'bonus2' => 1588625,
				'bonus3' => 1723513,
				'bonus4' => 1852769,
				'start' => 515825,
				'sprites' => 66496,
			],
		];

		if (!in_array($type, array_keys($offsets))) {
			return false;
		}
		if (!isset($offsets[$type][$level])) {
			return false;
		}
		return $offsets[$type][$level];
	}
}