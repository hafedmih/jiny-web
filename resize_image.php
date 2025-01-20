<?php

require __DIR__ . '/vendor/autoload.php'; // Make sure to include the autoloader

use Intervention\Image\ImageManagerStatic as Image;

// Define source and output directories
$sourceDir = '/var/www/html/lahagni/storage/app/public/images/temp';
$outputDir = '/var/www/html/lahagni/storage/app/public/images/temp/resized';

// Ensure the output directory exists
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Fetch all image files from the source directory
$files = glob($sourceDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);

// Process each image
foreach ($files as $file) {
    try {
        // Open the image file
        $img = Image::make($file);

        // Resize the image (e.g., to 300x300 pixels)
        $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio(); // Maintain aspect ratio
            $constraint->upsize();      // Prevent enlarging smaller images
        });

        // Save the resized image in the output directory
        $outputFile = $outputDir . '/' . basename($file);
        $img->save($outputFile);

        echo "Resized: " . $outputFile . "\n";
    } catch (Exception $e) {
        echo "Error processing file {$file}: " . $e->getMessage() . "\n";
    }
}

echo "Image resizing complete.\n";

