<?php
// Directory where folders are stored
$folderPath = '../images/';

if (is_dir($folderPath)) {
    // Scan for folders (ignores '.' and '..')
    $folders = array_filter(scandir($folderPath), function ($item) use ($folderPath) {
        return is_dir($folderPath . $item) && $item !== '.' && $item !== '..';
    });

    // Return folder names as JSON
    echo json_encode(array_values($folders));
} else {
    echo json_encode([]);
}
?>
