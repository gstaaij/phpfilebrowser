
# PHP File Browser

This is a directory listing program to make a certain folder available for browsing.

## Usage

1. Put `index.php` in the folder where you want your directory listing to be
2. **Put the actual directory structure in a folder within that folder**
3. Change `index.php`:
   ```php
   $BASE_URL = "<put the URL path where index.php resides here>";
   $FILE_LOCATION = "<put the path — relative to the index.php file — to the actual file structure here>";
   $EXCLUSIONS = [".", "..", "<any other exclusions you want>"];
   ```
