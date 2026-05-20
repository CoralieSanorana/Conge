<?php
// Simple PHP downloader: run from project root: php tools/download-chart.php
$uri = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
$dest = __DIR__ . '/../public/assets/js/chart.umd.min.js';
$dir = dirname($dest);
if (!is_dir($dir)) mkdir($dir, 0755, true);

echo "Downloading $uri ...\n";
$ctx = stream_context_create(['http' => ['timeout' => 30]]);
$contents = @file_get_contents($uri, false, $ctx);
if ($contents === false) {
    echo "Failed to download. Check network or run the PowerShell script.\n";
    exit(1);
}
file_put_contents($dest, $contents);
echo "Saved to $dest\n";
