<?php
namespace Yogho;

class Blocks extends Yogho
{
	private $level;
	private $tiles;
	public $blocks;

	const FLIP_X = 2;
	const FLIP_Y = 4;
	const UNKNOWN = 8; //TODO: figure out what this is
	const INCREASE_INDEX = 16;

	const BLOCKS = 512;
	const TILESIZE = 16;
	const BLOCKS_PER_ROW = 16; //TODO: not yet used


	public function __construct($level = 1)
	{
		$this->level = $level;

		$offsets = new Offsets;
		$baseOffset = $offsets::getOffset('blocks', $level);
		$tiles = new Tiles($level);
		$this->tiles = $tiles->tiles;

		$handle = fopen ($this::FILENAME, 'rb');

		for ($block = 0; $block < $this::BLOCKS; $block++) {
			$offset = $baseOffset + $block;
			if ($block & 1) {
				$offset += 511;
			}

			// Top left
			fseek($handle, $offset);
			$tileTL = $this->getTile($handle);

			// Top right
			fseek($handle, 1022, SEEK_CUR);
			$tileTR = $this->getTile($handle);

			// Bottom left
			fseek($handle, 1022, SEEK_CUR);
			$tileBL = $this->getTile($handle);

			// Bottom right
			fseek($handle, 1022, SEEK_CUR);
			$tileBR = $this->getTile($handle);

			$this->addTile($this->blocks[$block], $tileTL,  0,  0);
			$this->addTile($this->blocks[$block], $tileTR, 16,  0);
			$this->addTile($this->blocks[$block], $tileBL,  0, 16);
			$this->addTile($this->blocks[$block], $tileBR, 16, 16);
		}

		fclose($handle);
	}


	private function addTile(&$block, $tile, $atX, $atY)
	{
		for ($y = 0; $y < $this::TILESIZE; $y++) {
			for ($x = 0; $x < $this::TILESIZE; $x++) {
				$block[$atX + $x][$atY + $y] = $tile[$x][$y];
			}
		}
	}

	private function flipTileX($tile)
	{
		$halfWidth = (int) floor(($this::TILESIZE - 1) / 2);

		for ($x = $halfWidth; $x >= 0; $x--) {
			for ($y = 0; $y < $this::TILESIZE; $y++) {
				$temp = $tile[($this::TILESIZE - 1) - $x][$y];
				$tile[($this::TILESIZE - 1) - $x][$y] = $tile[$x][$y];
				$tile[$x][$y] = $temp;
			}
		}

		return $tile;
	}


	private function flipTileY($tile)
	{
		$halfHeight = (int) floor(($this::TILESIZE - 1) / 2);

		for ($y = $halfHeight; $y >= 0; $y--) {
			for ($x = 0; $x < $this::TILESIZE; $x++) {
				$temp = $tile[$x][($this::TILESIZE - 1) - $y];
				$tile[$x][($this::TILESIZE - 1) - $y] = $tile[$x][$y];
				$tile[$x][$y] = $temp;
			}
		}
		return $tile;
	}


	private function getTile(&$handle)
	{
		$special = ord(fread($handle, 1));
		$index = ord(fread($handle, 1));
		if ($special & $this::INCREASE_INDEX) {
			$index += 256;
		}
		$tile = $this->tiles[$index];
		if ($special & $this::FLIP_X) {
			$tile = $this->flipTileX($tile);
		}
		if ($special & $this::FLIP_Y) {
			$tile = $this->flipTileY($tile);
		}
		return $tile;
	}


	public function toImage($filename = null)
	{
		set_time_limit(60);
		if (is_null($filename)) {
			header('Content-type: image/png');
		}
		$image = imagecreatetruecolor(2 * $this::TILESIZE, 2 * $this::TILESIZE * $this::BLOCKS);

		for ($block = 0; $block < $this::BLOCKS; $block++) {
			for ($y = 0; $y < (2 * $this::TILESIZE); $y++) {
				for ($x = 0; $x < (2 * $this::TILESIZE); $x++) {
					list($r, $g, $b) = $this->blocks[$block][$x][$y];
					$color = imagecolorallocate($image, $r, $g, $b);
					$trueY = ($block * 2 * $this::TILESIZE) + $y;
					imagesetpixel($image, $x, $trueY, $color);
				}
			}
		}
		ImagePNG ($image, $filename);
		ImageDestroy ($image);
	}
}