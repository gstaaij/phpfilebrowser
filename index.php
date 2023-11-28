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


// These are things you can change to your liking
$BASE_URL = "/";
$FILE_LOCATION = "download/";
$EXCLUSIONS = [".", "..", "index.php"];


// This variable is used to show the error message with CSS applied to it
$error = false;

// If REQUEST_URI doesn't start with the $BASE_URL, give an error. This should never happen if set up correctly.
if (!str_starts_with($_SERVER["REQUEST_URI"], $BASE_URL)) {
    // Set the response code
    http_response_code(404);
    // Set the error to insert into the HTML later
    $error = "<h1>404 Not Found</h1><hr><p>Invalid download folder. <a href=\"$BASE_URL\">Go back</a>.</p>";
    // Skip the PHP code and go to the `end` label
    goto end;
}

// Take off the $BASE_URL from the REQUEST_URI
$strippedUrl = substr($_SERVER["REQUEST_URI"], strlen($BASE_URL));

$files = scandir($FILE_LOCATION . $strippedUrl);

// If scandir failed, 404
if (!$files) {
    http_response_code(404);
    $error = "<h1>404 Not Found</h1><hr><p>The file you are looking for does not exist. <a href=\"$BASE_URL\">Go back</a>.</p>";
    goto end;
}

// Remove the $EXCLUSIONS from the $files array
for ($i = count($files) - 1; $i >= 0; $i--) {
    $file = $files[$i];
    if (in_array($file, $EXCLUSIONS)) {
        array_splice($files, $i, 1);
    }
}

end:

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index of <?php echo $_SERVER["REQUEST_URI"]; ?></title>

    <link rel="stylesheet" href="<?php echo $BASE_URL . (str_ends_with($BASE_URL, "/") ? "" : "/") . "fonts/fonts.css"; ?>">
    <link rel="stylesheet" href="<?php echo $BASE_URL . (str_ends_with($BASE_URL, "/") ? "" : "/") . "style.css"; ?>">
</head>
<body>
    <?php
        if ($error) {
            // Insert the $error into the HTML and stop outputting or executing anything
            echo $error;
            echo "\n</body>\n</html>";
            exit;
        }
    ?>
    <h1>Index of <?php echo $_SERVER["REQUEST_URI"]; ?></h1>
    <hr>
    <div class="files">
        <?php if ($strippedUrl != "") { ?>
            <p class="file"><a href="<?php echo $_SERVER["REQUEST_URI"] . (str_ends_with($_SERVER["REQUEST_URI"], "/") ? "" : "/") . ".."; ?>">Parent Directory</a></p>
        <?php } else { ?>
            <p class="file disabled">Parent Directory</p>
        <?php }
        
        // Go through all files and 
        foreach ($files as $file) {
            // Do some mostly redundant logic to prevent any potential extra slashes
            $shouldSlash0 = ((str_ends_with($FILE_LOCATION, "/") || str_starts_with($strippedUrl, "/")) ? "" : "/");
            $shouldSlash1 = (($strippedUrl == "" || str_ends_with($strippedUrl, "/") || str_starts_with($file, "/")) ? "" : "/");
            // The path to the file
            $filePath = $FILE_LOCATION . $shouldSlash0 . $strippedUrl . $shouldSlash1 . $file;
            // The link to the file, if it's a file
            $fileLink = "/" . $filePath;
            // The download attribute, to allow for immediate downloading on click
            $downloadAttr = "download=\"" . $file . "\"";
            if (is_dir($filePath)) {
                $shouldSlash0 = ((str_ends_with($BASE_URL, "/") || str_starts_with($strippedUrl, "/")) ? "" : "/");
                // If the file is actually a directory, remove the download attribute and change the link
                $fileLink = $BASE_URL . $shouldSlash0 . $strippedUrl . $shouldSlash1 . $file;
                $downloadAttr = "";
            } ?>
            <p class="file"><a href="<?php echo $fileLink; ?>" <?php echo $downloadAttr; ?>><?php echo $file; ?></a></p>
        <?php } ?>
    </div>
</body>
</html>