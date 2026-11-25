# Introduction to Yogho Yogho
Collection of resources and script for the 1995 MS DOS game Yogho Yogho

# How to get started
You will need the original game files. [You can get the game here](https://www.myabandonware.com/game/yogho-yogho-spel-bul). I do not know if there are multiple versions of this game, but I don't think so. However, I can only guarantee these scripts work if your `YOGHODAT.DAT` file matches the following checksums:

- CRC32: `603106407`
- MD5: `f0969aab218dad90b8d9dcc9c0c51a1b`
- SHA1: `c830e4df5b461973c53849faf1534b1cdf02b03d`

You can check the checksums of your copy by running `hashes.php`

TODO: expand

# Why ?
I loved Yogho Yogho as a kid, both [the drink](https://www.yoghoyogho.nl) and the game. It was a tough game, there are no save games. So if you got "game over" on level 5, you would have to start again all the way in level 1. I *think* I managed to finish the game back then, but I am not sure.

I started these scripts in 2013 but decided to rework it into a nice open-source collection of scripts in 2015.

After that, things got quiet but I got back to it in 2018. I finally worked out the font format, level fruits, and more.

The years after, I occasionally looked at finding out more, but didn't get any far. With the advent of AI tools, I was finally able to find collision data and all the sprites. Scripts for those will follow soon.

# How did I figure this out?
From experience, I know that the easiest way to get started is by finding the palette, i.e. the colors of the game. Yogho Yogho, like many other games in those days, uses the 320x200 pixels screen mode with 256 colors. Using [DOSBox](https://www.dosbox.com/) I took a screenshot, which DOSBox saves as a indexed PNG file. This means the colors used by the game are stored in the same order in the PNG.

I looked up the RGB values of one of the colors, let's say this was the 26th color in the palette and had a value of 28,124,252. I knew that old games usually don't use 8-bit colors but 6-bit instead, so 28,124,252 would be expressed as 7,31,63. I found this value in the data file and worked out where the palette should start. Knowing there are 256 colors of 3 bytes each, I could extract the palette used by the game.

Next up, I took the offset of this palette (let's say it started at byte 0x2420) and searched for that (i.e. did a hex search for 0x24 0x20). I figured, it this offset is listed somewhere early in the file, it probably means it contains a list of all offsets used in the file. Turns out I was right! I now had a list of offsets and could calculate how large each part was.

There were a few parts that stood out: one part of exactly 12800 bytes, two of 64000 bytes and one of 48000 bytes. Since 320x200 (the size of the screen mode) is 64000, I assumed these were some kind of full screen images. So I corrupted the data file in these parts to see its effect. And yes: I found the menu screen and the high score background (both 320x200 pixels). The file of 12800 bytes turned out to be a 320x40 pixel image that is used as "status bar" in the game, the 48000 byte file is used as intro screen for the levels.

For more information, see [Classes/Offsets.php](Classes/Offsets.php)

# Where data is stored
Most game related data is stored in `YOGHODAT.DAT`. The file starts with 91 offsets, each 4 bytes, totalling 364 bytes. After this offset list, the entries start. The last offset in the list is the end of the file.

## List of entries

| Entry | Size | Contents |
|---|---|---|
| 0 | 12800 | **HUD background** (status bar: Punten / Dromen / Veld / Energie, Yogho Yogho logo): 320×40 linear 8-bit, uncompressed, uses the sprite palette.|
| 1 | 1833 | **Level fruit**: the image indicating each level (i.e. orange for level 1)|
| 2 | 51499 | **Ending picture**: RLE 320×200 + palette + RLE 216×141 text panel |
| 3 | 576 | **Sprite palette**, 192 colours (colours 64-255) |
| 4–9 | 31107 - 118399 | **Intro animations** ("Introductie"), delta-compressed 320×200 frames|
| 10 | 768 | **Title screen palette** (256 colours) |
| 11 | 64000 | **Title screen**, 320×200 linear 8-bit. Uses palette from entry 10|
| 12 | 64000 | **High-score table background**, 320×200 linear 8-bit. Uses palette from entry 10|
| 13 | 48000 | **Level-intro background**, 320×150|
| 14, 15 | 2772 | **Fonts**, 40 glyphs × 7×9 bytes |
| 16..65 | | 10 sets of 5: palette, blocks, tiles, map, collision|
| 66–73 | | **Music**: ScreamTracker 3 modules (S3M) with AdLib instruments. Entries 66/67/68 are level themes, 73 is used for all bonus levels, 69 for the ending, 70/71 loaded at startup |
| 74 | 4284 | **HUD graphics** (status bar panels, score-digit background, energy glass), planar images|
| 75 | 54779 | **Title/high-score graphics**, planar images, title palette. Uses palette from entry 10 |
| 76 | 82816 | Data for the credits program (entry 89) |
| 77–82 | 93972 … 107726 | **Overlays per level** Object/enemy code + sprites|
| 83–88 | ~37 KB | **Overlays per bonus level** |
| 89 | 14936 | **End credits program**|

# How levels work
First, there are tiles. Tiles are 16x16 pixels and are level-specific. Tiles can use 64 level-specific colors, combined with 192 global colors. Each level has ~400-500 tiles.

Those tiles are combined into 512 blocks, consisting of 2x2 tiles each. Each tile inside a block can be mirrored and/or flipped.

The blocks are the real basis for the levels. They are combined with collision data so the game knows where the player can stand and where there is air.

## Collision
There are 26 different collision shapes, each 16x16 in size. Collision, like tiles, can be mirrored and/or flipped.  _More information on collision will follow soon._

# How objects work

Objects are everything in the level that is not part of the background layer. Things as the player, doors, enemies, fruit, etc.

_Information on objects will follow soon._

# How the music works

Music is stored in ScreamTracker 3 (.s3m) format. All instruments are Adlib instruments, which are unsupported by many players (such as Winamp and OpenMPT).
[Schism Tracker](https://schismtracker.org) is a player that *is* capable of playing them.

# How the fonts works

There are two fonts in the game. One used for the start screen, and one used for all levels. They use slightly different colours and glyphs.

_More information on fonts will follow soon._

# High score table

High scores are stored in `YOGHOHI.DAT`. _More information will follow soon._

# Other things

_Will follow soon._
