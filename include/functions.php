<?php
function display_error() {
    if (isset($_SESSION['error'])) { ?>
        <p class="text-warning">
            <?php echo $_SESSION['error_description']; ?>
        </p>
        <?php unset($_SESSION['error']);
    }
}

/**
 *  Source: http://stackoverflow.com/questions/6930150/file-exists-returns-false-but-the-file-does-exist
 */
function custom_file_exists($file_path=''){
    $file_exists=false;

    //trim path
    $file_dir=trim(dirname($file_path));

    //normalize path separator
    $file_dir=str_replace('/',DIRECTORY_SEPARATOR,$file_dir).DIRECTORY_SEPARATOR;

    //trim file name
    $file_name=trim(basename($file_path));

    //rebuild path
    $file_path=$file_dir."{$file_name}";

    //If you simply want to check that some file (not directory) exists, 
    //and concerned about performance, try is_file() instead.
    //It seems like is_file() is almost 2x faster when a file exists 
    //and about the same when it doesn't.

    $file_exists=is_file($file_path);

    return $file_exists;
}