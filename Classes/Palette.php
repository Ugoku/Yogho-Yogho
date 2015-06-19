<?php
namespace Yogho;

class Palette extends Yogho
{
	const OFFSET_SPRITES = 66496;
	const OFFSET_STARTSCREEN = 515825;
	const OFFSET_LEVEL_1 = 698137;
	const OFFSET_LEVEL_2 = 806041;
	const OFFSET_LEVEL_3 = 917849;
	const OFFSET_LEVEL_4 = 1055513;
	const OFFSET_LEVEL_5 = 1190937;
	const OFFSET_LEVEL_6 = 1321049;
	const OFFSET_BONUS_1 = 1463897;
	const OFFSET_BONUS_2 = 1588625;
	const OFFSET_BONUS_3 = 1723513;
	const OFFSET_BONUS_4 = 1852769;

	public $palette;


	/**
	 * Set the palette for use in other functions
	 *
	 * @param int $offsetMain Offset for the palette. If none is specified, the palette for the start screens is used
	 * @param int|null $offsetSecond Offset for the extra colors (optional)
	 */
	public function __construct($offsetMain = 515825, $offsetSecond = null)
	{
		$handle = fopen ($this::FILENAME, 'rb');
		fseek ($handle, $offsetMain);

		// Start screens use a palette of 256 colors. Levels use a base palette of 192 colors and a level-specific palette of 64 colors
		$colors = (is_null($offsetSecond) ? 256 : 192);
		for ($color = 0; $color < $colors; $color++) {
			$this->palette[] = $this->readRGB($handle);
		}

		if (!is_null($offsetSecond)) {
			fseek ($handle, $offsetSecond);
			for ($color = 192; $color < 256; $color++) {
				$this->palette[] = $this->readRGB($handle);
			}
		}

		fclose ($handle);
	}


	/**
	 * Read RGB values from a file handle
	 *
	 * @param resource $handle
	 *
	 * @return array Array consisting of [R, G, B]
	 */
	private function readRGB(&$handle)
	{
		// Colors were 6-bit in those days, so need to multiply by 4 (2^8 / 2^6 = 4) to get the 8-bit value
		$r = ord(fread($handle, 1)) * 4;
		$g = ord(fread($handle, 1)) * 4;
		$b = ord(fread($handle, 1)) * 4;

		return [$r, $g, $b];
	}


	/**
	 * Output the palette as image
	 *
	 * @param string|null $filename File to output the palette to. If null, it will be rendered to the browser
	 */
	public function toImage($filename = null)
	{
		if (is_null($filename)) {
			header('Content-type: image/png');
		}
		$image = imagecreatetruecolor(256, 256);

		for ($color = 0; $color <= 255; $color++) {
			$y = floor ($color / 16);
			$x = $color - ($y * 16);

			list($r, $g, $b) = $this->palette[$color];
			$currentColor = ImageColorAllocate ($image, $r, $g, $b);

			ImageFilledRectangle ($image, $x * 16, $y * 16, ($x * 16) + 15, ($y * 16) + 15, $currentColor);
		}

		ImagePNG ($image, $filename);
		ImageDestroy ($image);
	}
}