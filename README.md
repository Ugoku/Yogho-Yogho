# Introduction to Yogho Yogho
Collection of resources and script for the 1995 MS DOS game Yogho Yogho

# How to get started
You will need the original game files. [You can get the game here](http://www.abandonia.com/en/games/732/) I do not know if there are multiple versions of this game, but I don't think so. However, I can only guarantee these scripts work if your YOGHODAT.DAT file matches the following checksums:

- CRC32: 603106407
- MD5: f0969aab218dad90b8d9dcc9c0c51a1b
- SHA1: c830e4df5b461973c53849faf1534b1cdf02b03d

You can check the checksums of your copy by running `hashes.php`

TODO: expand

# Why ?
I loved Yogho Yogho as a kid, both [the drink](http://www.yoghoyogho.nl/nl) and the game. But it was a tough game, there are no save games. So if you got "game over"on level 5, you would have to start again all the way in level 1. I *think* I managed to finish the game back then, but I am not sure.

TODO: expand

# How did I figure this out?
From experience, I know that the easiest way to get started is by finding the palette, i.e. the colors of the game. Yogho Yogho, like many other games in those days, uses the 320x200 pixels screen mode with 256 colors. Using [DOSBox](http://www.dosbox.com/) I took a screenshot, which DOSBox saves as a indexed PNG file. This means the colors used by the game are stored in the same order in the PNG.

I looked up the RGB values of one of the colors, let's say this was the 26th color in the palette and had a value of 31,63,255. I knew that old games usually don't use 8-bit colors but 6-bit instead, so 28,124,252 would be expressed as 7,31,63. I found this value in the data file and worked out where the palette should start. Knowing there are 256 colors of 3 bytes each, I could extract the palette used by the game.

Next up, I took the offset of this palette (let's say it started at byte 0x2420) and searched for that (i.e. did a hex search for 0x24 0x20). I figured, it this offset is listed somewhere early in the file, it probably means it contains a list of all offsets used in the file. Turns out I was right! I now had a list of offsets and could calculate how large each part was.

There were a few parts that stood out: one part of exactly 12800 bytes, two of 64000 bytes and one of 48000 bytes. Since 320x200 (the size of the screen mode) is 64000, I assumed these were some kind of full screen images. So I corrupted the data file in these parts to see its effect. And yes: I found the menu screen and the high score background (both 320x200 pixels). The file of 12800 bytes turned out to be a 320x40 pixel image that is used as "status bar" in the game, the 48000 byte file is used as intro screen for the levels.

TODO: expand