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
	public function __construct(string $level)
	{
		$this->level = $level;

		$offsets = new Offsets;
		$palette = new Palette($level);
		$palette = $palette->palette;

		$nrOfTiles = $this::TILECOUNTS[$level];

		$handle = fopen($this::FILENAME, 'rb');
		fseek($handle, $offsets::getOffset('tiles', $level));

		for ($tile = 0; $tile < $nrOfTiles; $tile++) {
			$this->tiles[$tile] = imagecreatetruecolor($this::TILESIZE, $this::TILESIZE);
			for ($pixel = 0; $pixel < ($this::TILESIZE * $this::TILESIZE); $pixel++) {
				// I hate arithmetic like this
				$x = 15 - ((((3 - ($pixel % 4)) * 4) + floor($pixel / 64)));
				$y = floor($pixel / 4) % 16;
				$value = ord(fread($handle, 1));

				list($r, $g, $b) = $palette[$value];
				$color = imagecolorallocate($this->tiles[$tile], $r, $g, $b);
				imagesetpixel($this->tiles[$tile], $x, $y, $color);
			}
		}

		fclose($handle);
	}


	/**
	 * Output the tiles to an image
	 *
	 * @param string|null $filename
	 */
	public function toImage(string $filename = null)
	{
		set_time_limit(60);
		if (is_null($filename)) {
			header('Content-type: image/png');
		}
		$image = $this->toResource();

        @mkdir('./tiles');

        imagepng($image, './tiles/' . $filename . '.png');
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
			$x = (($tile % $this::TILES_PER_ROW) * $this::TILESIZE);
			$y = (floor($tile / $this::TILES_PER_ROW) * $this::TILESIZE);
			imagecopy($image, $this->tiles[$tile], $x, $y, 0, 0, $this::TILESIZE, $this::TILESIZE);
		}
		return $image;
	}
}