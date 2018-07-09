<?php
namespace Yogho;

class Blocks extends Yogho
{
	private $level;
	private $tileImg;
	public $blocks;

	const FLIP_X = 2;
	const FLIP_Y = 4;
	const UNKNOWN = 8; //TODO: figure out what this is
	const INCREASE_INDEX = 16;

	const BLOCKS = 512;
	const TILESIZE = 16;
	const BLOCKS_PER_ROW = 16; //TODO: not yet used


	public function __construct(string $level = '1')
	{
		$this->level = $level;
        $this->tileImg = imagecreatefrompng('./tiles/' . $level . '.png');

		$offsets = new Offsets;
		$baseOffset = $offsets::getOffset('blocks', $level);

		$handle = fopen ($this::FILENAME, 'rb');

		for ($block = 0; $block < $this::BLOCKS; $block++) {
			$offset = $baseOffset + $block;
			if ($block & 1) {
				$offset += 511;
			}

			$this->addBlock($handle, $offset, $block);
		}

		fclose($handle);
	}

	private function addBlock(&$handle, int &$offset, int $idx)
    {
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

        $this->blocks[$idx] = [
            [$tileTL, $tileTR],
            [$tileBL, $tileBR],
        ];
    }

    private function copyTile($fromImg, &$toImg, int $destX, int $destY, int $srcX = 0, int $srcY = 0)
    {
        imagecopy($toImg, $fromImg, $destX, $destY, $srcX,$srcY, $this::TILESIZE, $this::TILESIZE);
    }

	private function getTile(&$handle)
	{
		$special = ord(fread($handle, 1));
		$index = ord(fread($handle, 1));
		if ($special & $this::INCREASE_INDEX) {
			$index += 256;
		}

        $tileX = ($index % Tiles::TILES_PER_ROW) * $this::TILESIZE;
        $tileY = floor($index / Tiles::TILES_PER_ROW) * $this::TILESIZE;

        $temptile = imagecreatetruecolor($this::TILESIZE, $this::TILESIZE);
        $this->copyTile($this->tileImg, $temptile, 0, 0, $tileX, $tileY);

		if ($special & $this::FLIP_X) {
            imageflip($temptile, IMG_FLIP_HORIZONTAL);
		}
		if ($special & $this::FLIP_Y) {
            imageflip($temptile, IMG_FLIP_VERTICAL);
        }
		return $temptile;
	}


	public function toImage(string $filename = null)
	{
		set_time_limit(60);
		if (is_null($filename)) {
			header('Content-type: image/png');
		}
		$image = imagecreatetruecolor(2 * $this::TILESIZE, 2 * $this::TILESIZE * $this::BLOCKS);

		for ($block = 0; $block < $this::BLOCKS; $block++) {
            $trueY = ($block * 2 * $this::TILESIZE);
            $this->copyTile($this->blocks[$block][0][0], $image, 0, $trueY);
            $this->copyTile($this->blocks[$block][0][1], $image, $this::TILESIZE, $trueY);
            $this->copyTile($this->blocks[$block][1][0], $image, 0, $trueY + $this::TILESIZE);
            $this->copyTile($this->blocks[$block][1][1], $image, $this::TILESIZE, $trueY + $this::TILESIZE);
		}

        @mkdir('./blocks');

        imagepng($image, './blocks/' . $filename . '.png');
		ImageDestroy ($image);
	}
}