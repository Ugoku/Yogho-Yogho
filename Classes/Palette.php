<?php
namespace Yogho;

class Palette extends Yogho
{
	public $palette;


	/**
	 * Set the palette for use in other functions
	 *
	 * @param int|string $level Level to get the palette for. This can also be 'start' to get the palette for the start screen
	 */
	public function __construct(string $level = 'start')
	{
		$offsets = new Offsets;
		if ($level === 'start') {
			$offsetMain = $offsets::getOffset('palette', $level);
			$offsetSecond = null;
		} else {
			$offsetMain = $offsets::getOffset('palette', 'sprites');
			$offsetSecond = $offsets::getOffset('palette', $level);
		}

		$handle = fopen($this::FILENAME, 'rb');

		// Start screens use a palette of 256 colors. Levels use a base palette of 192 colors and a level-specific palette of 64 colors
		if ($offsetSecond) {
            fseek($handle, $offsetSecond);
            for ($color = 0; $color < 64; $color++) {
                $this->palette[] = $this->readRGB($handle);
			}
		}

        fseek($handle, $offsetMain);
		$colors = (is_null($offsetSecond) ? 256 : 192);
		for ($color = (256 - $colors); $color < 256; $color++) {
			$this->palette[] = $this->readRGB($handle);
		}

		fclose($handle);
	}


	/**
	 * Read RGB values from a file handle
	 *
	 * @param resource $handle
	 *
	 * @return array Array consisting of [R, G, B]
	 */
	private function readRGB(&$handle): array
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
	public function toImage(string $filename = null)
	{
		if (is_null($filename)) {
			header('Content-type: image/png');
		}
		$image = $this->toResource();

		@mkdir('./palettes');

		if ($filename) {
            $filename = './palettes/' . $filename . '.png';
        }
		imagepng($image, $filename);
		imagedestroy($image);
	}


	/**
	 * Returns the palette as image resource
	 *
	 * @return resource
	 */
	public function toResource()
	{
		$image = imagecreatetruecolor(256, 256);

		for ($color = 0; $color <= 255; $color++) {
			$y = floor($color / 16);
			$x = $color - ($y * 16);

			list($r, $g, $b) = $this->palette[$color];
			$currentColor = imagecolorallocate($image, $r, $g, $b);

			imagefilledrectangle($image, $x * 16, $y * 16, ($x * 16) + 15, ($y * 16) + 15, $currentColor);
		}
		return $image;
	}
}