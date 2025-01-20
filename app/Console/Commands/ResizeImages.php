<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ResizeImages extends Command
{
    // The name and signature of the console command.
    protected $signature = 'images:resize';

    // The console command description.
    protected $description = 'Resize images in the storage directory to smaller dimensions if larger than 2MB';

    // Execute the console command.
    public function handle()
    {
        // Define source and output directories
        $sourceDir = storage_path('app/public/images/test_doc');
        $outputDir = storage_path('app/public/images/test_doc/resized');

        // Check if source directory exists
        if (!File::exists($sourceDir)) {
            $this->error('Source directory does not exist.');
            return;
        }

        // Create output directory if it does not exist
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0777, true);
        }

        // Get all image files (jpg, jpeg, png) in the source directory
        $files = File::glob($sourceDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);

        // If no files found, exit
        if (empty($files)) {
            $this->info('No images found.');
            return;
        }

        // Loop through each file and process it
        foreach ($files as $file) {
            try {
                // Check file size in bytes (2MB = 2 * 1024 * 1024)
                $fileSize = filesize($file);
                $isLarge = $fileSize > (2 * 1024 * 1024);

                if ($isLarge) {
                    // Resize if file is larger than 2MB
                    list($width, $height, $type) = getimagesize($file);

                    // Only process valid images
                    if (!in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
                        $this->error("Unsupported image type for {$file}");
                        continue;
                    }

                    // Create image resource from the source file based on type
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            $img = imagecreatefromjpeg($file);
                            break;
                        case IMAGETYPE_PNG:
                            $img = imagecreatefrompng($file);
                            break;
                    }

                    // Set new dimensions while maintaining the aspect ratio
                    $newWidth = 300;
                    $newHeight = ($height / $width) * $newWidth;

                    // Create a new empty image with the new dimensions
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);

                    // Preserve transparency for PNGs
                    if ($type == IMAGETYPE_PNG) {
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                    }

                    // Resize the original image and copy it to the new image
                    imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    // Set output file path
                    $outputFile = $outputDir . '/' . basename($file);

                    // Save the resized image
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            imagejpeg($newImage, $outputFile, 85);  // Quality set to 85
                            break;
                        case IMAGETYPE_PNG:
                            imagepng($newImage, $outputFile, 8);   // Compression level set to 8
                            break;
                    }

                    // Free up memory
                    imagedestroy($img);
                    imagedestroy($newImage);

                    $this->info("Resized: " . $outputFile);
                } else {
                    // Copy file to the new directory if smaller than or equal to 2MB
                    $outputFile = $outputDir . '/' . basename($file);
                    File::copy($file, $outputFile);
                    $this->info("Copied: " . $outputFile);
                }
            } catch (\Exception $e) {
                $this->error("Error processing file {$file}: " . $e->getMessage());
            }
        }

        $this->info('Image processing complete.');
    }
}

