<?php 
echo "Yo";
// Open the file for reading
if (($h = fopen("users.csv", "r")) !== FALSE) 
{
   
  // Convert each line into the local $data variable
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {		
      
    print_r($data);
  }

  // Close the file
  fclose($h);
}

?>