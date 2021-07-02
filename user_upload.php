<?php
include("./gifnoc.php");
// Open the file for reading
if (($h = fopen("users.csv", "r")) !== FALSE) 
{
  $cntr = 0;
  // Convert each line into the local $data variable
  $sql_head = " INSERT INTO users (firstname, surname, email) VALUES ";
  $sql_val = "";
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
   if ($cntr >=1 ){
     echo $cntr;
     print_r($data);  
     $firstname = ucfirst(strtolower(trim($data[0]))); 
     $lastname =  ucfirst(strtolower(trim($data[1]))); 
     $email = strtolower(trim($data[2]));
     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //$emailErr = "Invalid email format"; 
       $email = "invalid".$cntr;
      }
     $sql_val .= "('".addslashes($firstname)."', '".addslashes($lastname)."', '".addslashes($email)."'),";    
   }
     $cntr++;
  }
  echo $sql_head . $sql_val;
  
    // if ($conn->query($sql) === TRUE) {
    //   echo "New record created successfully";
    //   } else {
    //     echo "Error: " . $sql . "<br>" . $conn->error;
    //   }

  // Close the file
  fclose($h);


}

?>