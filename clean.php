<?php
$files = ['index.php', 'config.php', 'app/Controllers/AdminController.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            file_put_contents($file, substr($content, 3));
            echo "Cleaned: $file <br>";
        } else {
            echo "Checked: $file <br>";
        }
    }
}
echo "Done.";