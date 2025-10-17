<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media — Project Bundler
 * ------------------------------------------------------------
 * Packages the full application into a single deployable ZIP
 * for Hostinger / cPanel shared hosting.
 * ------------------------------------------------------------
 */

$rootDir = dirname(__DIR__);
$zipFile = $rootDir . '/flyboost_media_ready.zip';

if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
    exit("❌ Cannot create ZIP file.\n");
}

$exclude = [
    '.git',
    'node_modules',
    'vendor',
    'build',
    '.DS_Store',
    'Thumbs.db',
    'flyboost_media_ready.zip'
];

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $file) {
    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($rootDir) + 1);

    // Skip excluded directories
    foreach ($exclude as $skip) {
        if (strpos($relativePath, $skip) === 0) {
            continue 2;
        }
    }

    if (!$file->isDir()) {
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

echo "✅ Flyboost Media project packaged successfully!\n";
echo "📦 Output: " . basename($zipFile) . "\n";
