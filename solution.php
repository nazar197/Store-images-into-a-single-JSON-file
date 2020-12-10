<?php 

if(isset($_POST['dir-path__input'])){

  $input_dir_path = $_POST['dir-path__input'];

  function getListOfFiles(string $dir_path) {
    if ( !is_dir($dir_path) || !is_readable($dir_path) ) 
      return false;

    // Iterate files in current catalog and it's subcatalogs
    /*
    $directory = new RecursiveDirectoryIterator($dir_path, RecursiveDirectoryIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD);
    */

    // Iterate files only in current catalog 
    $iterator = new FilesystemIterator($dir_path);

    $files = [];

    foreach($iterator as $file) {
      if ( preg_match("/.*\.(jpg|png|gif|jpeg|bmp)/", $file) ) 
        $files[] = $file -> getPathname();
    }

    sort($files);

    return $files;
  }

  // Displays an array of files that would be 
  // encoded to base64 and stored in JSON file;

  echo "<pre>";
  print_r (getListOfFiles($input_dir_path));
  echo "</pre>";

  function base64_encode_image (string $file_name, string $file_type) {
    if ($file_name) {
      $img_binary = fread(fopen($file_name, "r"), filesize($file_name));
      return 'data:image/' . $file_type . ';base64,' . base64_encode($img_binary);
    }
  }

  $array_of_encoded_images = array();
  foreach (getListOfFiles($input_dir_path) as $image) {
    $encoded_image = base64_encode_image($image, pathinfo($image, PATHINFO_EXTENSION));
    $array_of_encoded_images[] = $encoded_image;
    
    $encoded_json = str_replace("\\/", "/", 
      json_encode($array_of_encoded_images));
  }

  $folder_name = basename($input_dir_path);

  $json_file_name = "images_{$folder_name}.json"; 
  if( file_put_contents("$json_file_name", "$encoded_json") ) { 
    echo 'File "' . $json_file_name . '" is created successful!'; 
  } else { 
    echo 'There were no files to processed, so "' . $json_file_name . '" is empty!'; 
  } 

}