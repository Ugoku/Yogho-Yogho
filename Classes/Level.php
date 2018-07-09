<?php
namespace Yogho;

class Level extends Yogho
{
	private $blockImg;
    private $level;
	public $levelData;

    const INCREASE_INDEX_HIGH = 1;
    const INCREASE_INDEX_LOW = 2;
	const FLIP_X = 4;
	const FLIP_Y = 8;
	// 16 is unused in the entire game
	const LADDER = 32;
	const DEATH = 64;
	const CRAWL = 128;

	const BLOCKSIZE = 32;
	const DIMENSIONS = [
		1 => [160, 15],
		2 => [80, 40],
		3 => [160, 40],
		4 => [160, 45],
		5 => [80, 60],
		6 => [90, 80],
		'bonus1' => [20, 35],
		'bonus2' => [30, 22],
		'bonus3' => [30, 22],
		'bonus4' => [30, 22],
	];


	public function __construct(string $level = '1')
	{
	    ini_set('memory_limit', '1G');
        $offsets = new Offsets;

        $this->level = $level;
        $this->blockImg = imagecreatefrompng ('./blocks/' . $level . '.png');

		$handle = fopen ($this::FILENAME, 'rb');
		fseek($handle, $offsets::getOffset('levelData', $level));

		$levelWidth = $this::DIMENSIONS[$level][0];
		$levelHeight = $this::DIMENSIONS[$level][1];

		for ($y = 0; $y < $levelHeight; $y++) {
			for ($x = 0; $x < $levelWidth; $x++) {
				$block = ord(fread($handle, 1));
				$special = ord(fread($handle, 1));

				if ($special & $this::INCREASE_INDEX_HIGH) {
					$block += 256;
				}
				if ($special & $this::INCREASE_INDEX_LOW) {
					$block++;
				}

				$thisBlock = $this->getBlock($block);
				if ($special & $this::FLIP_X) {
					imageflip($thisBlock, IMG_FLIP_HORIZONTAL);
				}
				if ($special & $this::FLIP_Y) {
					imageflip($thisBlock, IMG_FLIP_VERTICAL);
				}

				$this->levelData[$x][$y] = $thisBlock;
			}
		}
	}

	private function getBlock($index)
    {
        $tempBlock = imagecreatetruecolor($this::BLOCKSIZE, $this::BLOCKSIZE);

        $blockX = 0; //($index % Blocks::BLOCKS_PER_ROW) * $this::BLOCKSIZE;
        $blockY = $index * $this::BLOCKSIZE; // floor($index / Tiles::TILES_PER_ROW)

        imagecopy($tempBlock, $this->blockImg, 0, 0, $blockX, $blockY, $this::BLOCKSIZE, $this::BLOCKSIZE);
        return $tempBlock;
    }

	public function toImage($filename = null)
	{
        if (is_null($filename)) {
            header('Content-type: image/png');
        }

        $levelWidth = $this::DIMENSIONS[$this->level][0];
        $levelHeight = $this::DIMENSIONS[$this->level][1];
        $image = imagecreatetruecolor($levelWidth * $this::BLOCKSIZE, $levelHeight * $this::BLOCKSIZE);

        for ($y = 0; $y < $levelHeight; $y++) {
            for ($x = 0; $x < $levelWidth; $x++) {
                imagecopy($image, $this->levelData[$x][$y], $x * $this::BLOCKSIZE, $y * $this::BLOCKSIZE, 0, 0, $this::BLOCKSIZE, $this::BLOCKSIZE);
            }
        }

        @mkdir('./levels');

        imagepng($image, './levels/' . $filename . '.png');
	}
}