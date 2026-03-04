<?php
$imagePath = 'icon.png';

if (!file_exists($imagePath)) {
    die("#error [PHP Tool] Source image file '$imagePath' not found.\n");
}

$img = @imagecreatefrompng($imagePath);

if (!$img) {
    die("#error [PHP Tool] Failed to load PNG. The file might be corrupt or not a valid PNG.\n");
}

$width = imagesx($img);
$height = imagesy($img);

$baseName = pathinfo($imagePath, PATHINFO_FILENAME);
$structName = preg_replace('/[^a-zA-Z0-9_]/', '_', $baseName . "_png");

$hueOffset = 10 * time() % 360;

function rotateHue($r, $g, $b, $angle) {
    $angle = $angle * (M_PI / 180);
    $cosA = cos($angle);
    $sinA = sin($angle);

    // Matrix rotation for Hue shift
    $r_new = (.299 + .701 * $cosA + .168 * $sinA) * $r 
           + (.587 - .587 * $cosA + .330 * $sinA) * $g 
           + (.114 - .114 * $cosA - .497 * $sinA) * $b;

    $g_new = (.299 - .299 * $cosA - .328 * $sinA) * $r 
           + (.587 + .413 * $cosA + .035 * $sinA) * $g 
           + (.114 - .114 * $cosA + .292 * $sinA) * $b;

    $b_new = (.299 - .3 * $cosA + 1.25 * $sinA) * $r 
           + (.587 - .588 * $cosA - 1.05 * $sinA) * $g 
           + (.114 + .886 * $cosA - .203 * $sinA) * $b;

    return [
        max(0, min(255, (int)$r_new)),
        max(0, min(255, (int)$g_new)),
        max(0, min(255, (int)$b_new))
    ];
}

?>
/* Image: <?=$hueOffset?> <?=$imagePath?> (<?=$width?> x <?=$height?>) */

static const struct {
  unsigned int width;
  unsigned int height;
  unsigned int data[<?= $width * $height ?>];
} <?=$structName?> = {
  .width=<?=$width?>,
  .height=<?=$height?>,
  .data={
<?
for ($y = 0; $y < $height; $y++) {
    echo "    "; 
    for ($x = 0; $x < $width; $x++) {
        $colorIndex = imagecolorat($img, $x, $y);
        $colors = imagecolorsforindex($img, $colorIndex);

        [$r, $g, $b] = rotateHue($colors['red'], $colors['green'], $colors['blue'], $hueOffset);
 
        $hex = sprintf("0x%02X%02X%02X", $r, $g, $b);
        echo $hex;

        if (!($x == $width - 1 && $y == $height - 1)) {
            echo ", ";
        }
    }
    echo "\n";
}
?>
  }
};

<?
imagedestroy($img);
?>
