<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($request, '/');
$baseDir = __DIR__;

// 1. DYNAMIC FILE MAPPING (The priority)
// Check if they are asking for "file.c" and "file.c.php" exists
$real = $baseDir . ($path ? '/' . $path : ''); // Defines $real based on URL $path
$types = ['c' => 'text/x-csrc', 'h' => 'text/x-chdr', 'cpp' => 'text/x-c++src'];
$ext = pathinfo($path, PATHINFO_EXTENSION);

// 1. FILE GENERATOR
if (isset($types[$ext]) && file_exists($real . '.php')) {
    ob_start();
    include $real . '.php';
    $output = ob_get_clean();

    header("Content-Type: {$types[$ext]}");
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Content-Length: ' . strlen($output));
    header('Connection: close');

    echo $output;
    exit;
}

// 2. DIRECTORY LISTING
// Only runs if the path is a real directory (like /code/)
$realDir = rtrim($baseDir . '/' . $path, '/');
if (is_dir($realDir)) {
    echo "<a href='../'>../</a><br>\n";
    $files = scandir($realDir);
    $exts = implode('|', array_keys($types));

    foreach ($files as $f) {
        if ($f === '.' || $f === '..' || $f === '.serve.php') continue;

        $fullPath = $realDir . '/' . $f;
        $is_dir = is_dir($fullPath);

        // If it's x.c.php, show x.c to the user
        if (preg_match("/^(.+\.($exts))\.php$/", $f, $m)) {
            $name = $m[1];
        } else {
            $name = $f . ($is_dir ? '/' : '');
        }
        echo "<a href='$name'>$name</a><br>\n";
    }
    exit;
}

// 3. FALLBACK: Serve actual .php or other files
return false;
