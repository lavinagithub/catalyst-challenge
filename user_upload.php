<?php
include("./gifnoc.php");

// Open the file for reading
if (($h = fopen("users.csv", "r")) !== FALSE) 
{
  $cntr =0;
  // Convert each line into the local $data variable
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
      //print_r($data);
   		     
}
}

?>