<?php

// Copyright (c) 2023 gstaaij
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.


// Change this if you put this anywhere else
$BASE_URL = "/";
$FILE_LOCATION = "download/";
$EXCLUSIONS = [".", "..", "index.php"];

if (!str_starts_with($_SERVER["REQUEST_URI"], $BASE_URL)) {
    http_response_code(404);
    echo("Invalid download folder. <a href=\"$BASE_URL\">Go back</a>");
    exit;
}

$strippedUrl = substr($_SERVER["REQUEST_URI"], strlen($BASE_URL));

$files = scandir($FILE_LOCATION . $strippedUrl);
for ($i = count($files) - 1; $i >= 0; $i--) {
    $file = $files[$i];
    if (in_array($file, $EXCLUSIONS)) {
        array_splice($files, $i, 1);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index of <?php echo $_SERVER["REQUEST_URI"]; ?></title>

    <link rel="stylesheet" href="<?php echo $BASE_URL . (str_ends_with($BASE_URL, "/") ? "" : "/") . "style.css"; ?>">
</head>
<body>
    <h1>Index of <?php echo $_SERVER["REQUEST_URI"]; ?></h1>
    <hr>
    <div class="files">
        <?php if ($strippedUrl != "") { ?>
            <p><a href="<?php echo $_SERVER["REQUEST_URI"] . (str_ends_with($_SERVER["REQUEST_URI"], "/") ? "" : "/") . ".."; ?>">Parent Directory</a></p>
        <?php } else { ?>
            <p class="disabled">Parent Directory</p>
        <?php } ?>
        <?php foreach ($files as $file) {
            $shouldSlash0 = ((str_ends_with($FILE_LOCATION, "/") || str_starts_with($strippedUrl, "/")) ? "" : "/");
            $shouldSlash1 = (($strippedUrl == "" || str_ends_with($strippedUrl, "/") || str_starts_with($file, "/")) ? "" : "/");
            $filePath = $FILE_LOCATION . $shouldSlash0 . $strippedUrl . $shouldSlash1 . $file;
            $fileLink = "/" . $filePath;
            if (is_dir($filePath)) {
                $shouldSlash0 = ((str_ends_with($BASE_URL, "/") || str_starts_with($strippedUrl, "/")) ? "" : "/");
                $fileLink = $BASE_URL . $shouldSlash0 . $strippedUrl . $shouldSlash1 . $file;
            } ?>
            <p><a href="<?php echo $fileLink; ?>"><?php echo $file; ?></a></p>
        <?php } ?>
    </div>
</body>
</html>