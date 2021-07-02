<?php
include("./gifnoc.php");

// Open the file for reading
if (($h = fopen("users.csv", "r")) !== FALSE) 
{
  $cntr =0;
  // Convert each line into the local $data variable
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
      
   if ($cntr >=1 ){
     //print_r($data);
     $firstname = ucfirst($data[0]); 
     $lastname =  ucfirst($data[1]); 
     $email = $data[2];
     $sql .= "INSERT INTO users (firstname, surname, email) VALUES ('John', 'Doe', 'john@example.com');";

      
   }
     $cntr++;
  }
  echo $sql;
  // Close the file
  fclose($h);


}

?>