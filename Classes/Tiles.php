<?php
namespace Yogho;

class Tiles extends Yogho
{
	private $level;
	public $tiles;

	const TILECOUNTS = [
		1 => 382,
		2 => 391,
		3 => 467,
		4 => 452,
		5 => 450,
		6 => 481,
		'bonus1' => 461,
		'bonus2' => 501,
		'bonus3' => 479,
		'bonus4' => 495,
	];
	const TILESIZE = 16; // Tiles are always square, so we can use a single constant for both width and height
	const TILES_PER_ROW = 16;


	/**
	 * Load tiles for a certain level
	 *
	 * @param int|string $level
	 */
	public function __construct($level)
	{
		$this->level = $level;

		$offsets = new Offsets;
		$palette = new Palette($level);
		$palette = $palette->palette;

		$nrOfTiles = $this::TILECOUNTS[$level];

		$handle = fopen($this::FILENAME, 'rb');
		fseek($handle, $offsets::getOffset('tiles', $level));

		for ($tile = 0; $tile < $nrOfTiles; $tile++) {
			for ($pixel = 0; $pixel < ($this::TILESIZE * $this::TILESIZE); $pixel++) {
				// I hate arithmetic like this
				$x = 15 - ((((3 - ($pixel % 4)) * 4) + floor($pixel / 64)));
				$y = (floor($pixel / 4) % 16);
				$value = ord(fread($handle, 1));
				$rgb = $palette[$value];
				$this->tiles[$tile][$x][$y] = $rgb;
			}
		}

		fclose($handle);
	}


	/**
	 * Output the tiles to an image
	 *
	 * @param string|null $filename
	 */
	public function toImage($filename = null)
	{
		set_time_limit(60);
		if (is_null($filename)) {
			header('Content-type: image/png');
		}
		$image = $this->toResource();

		imagepng($image, $filename);
		imagedestroy($image);
	}


	/**
	 * Returns the tile image as a resource
	 * @return resource
	 */
	public function toResource()
	{
		$nrOfTiles = $this::TILECOUNTS[$this->level];
		$image = imagecreatetruecolor($this::TILESIZE * $this::TILES_PER_ROW, ceil($nrOfTiles / $this::TILES_PER_ROW) * $this::TILESIZE);

		for ($tile = 0; $tile < $nrOfTiles; $tile++) {
			for ($y = 0; $y < $this::TILESIZE; $y++) {
				for ($x = 0; $x < $this::TILESIZE; $x++) {
					list($r, $g, $b) = $this->tiles[$tile][$x][$y];
					$color = imagecolorallocate($image, $r, $g, $b);
					// Did I mention how much I hate arithmetic like this?
					$trueX = (($tile % $this::TILES_PER_ROW) * $this::TILESIZE) + $x;
					$trueY = (floor($tile / $this::TILES_PER_ROW) * $this::TILESIZE) + $y;
					imagesetpixel($image, $trueX, $trueY, $color);
				}
			}
		}
		return $image;
	}
}