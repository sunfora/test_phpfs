<?php
$cacheFile = '/tmp/tinyexpr_h_cache';
$url = "https://raw.githubusercontent.com/codeplea/tinyexpr/refs/heads/master/tinyexpr.h";

// Refresh cache only if it's older than 60 seconds
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > 60)) {
    $data = file_get_contents($url);
    if ($data) file_put_contents($cacheFile, $data);
}

echo "/* Cached Proxy */\n";
echo file_get_contents($cacheFile);

