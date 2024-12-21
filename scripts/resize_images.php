<?php

// Directory where the images are located
$directory = __DIR__ . '/../storage/app/public/images/document/';

// New width for resizing the image
$newWidth = 800;

// Scan the directory and get all files
$files = scandir($directory);

foreach ($files as $file) {
    $filePath = $directory . $file;

    // Check if it's a valid image file
    if (is_file($filePath) && exif_imagetype($filePath)) {
        // Get the original size of the image
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Calculate new height based on the aspect ratio
        $newHeight = ($originalHeight / $originalWidth) * $newWidth;

        // Create a new true color image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Load the image based on its MIME type
        $imageType = exif_imagetype($filePath);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($filePath);
                break;
            default:
                echo "Unsupported image type for: " . $file . PHP_EOL;
                continue 2; // Skip to next file
        }

        // Resize the image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Save the image with compression (adjust quality if needed)
        if ($imageType == IMAGETYPE_JPEG) {
            imagejpeg($newImage, $filePath, 70); // 70 is the quality (0-100)
        } elseif ($imageType == IMAGETYPE_PNG) {
            imagepng($newImage, $filePath, 6);   // 0 (no compression) - 9 (max compression)
        } elseif ($imageType == IMAGETYPE_GIF) {
            imagegif($newImage, $filePath);      // GIF doesn't support compression
        }

        // Free up memory
        imagedestroy($newImage);
        imagedestroy($image);

        echo "Resized and compressed: " . $file . PHP_EOL;
    }
}

echo "All images processed.\n";

