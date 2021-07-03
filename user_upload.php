<?php
// file includes
include("./gifnoc.php");

// function to check valid email address
function checkValidEmail($email){
 if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       return false;
      }
}

// Open the file for reading
if (($h = fopen("users.csv", "r")) !== FALSE) 
{
  $cntr = 0;
  // Convert each line into the local $data variable
  $sql_head = " INSERT INTO users (firstname, surname, email) VALUES ";
  $sql_val = "";
  $emailError = "";
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
   if ($cntr >=1 ){
     //echo $cntr;
     //print_r($data);  
     $email = strtolower(trim($data[2]));
     if(checkValidEmail($email) !== false){
       echo "here";
       $firstname = ucfirst(strtolower(trim($data[0]))); 
       $lastname =  ucfirst(strtolower(trim($data[1]))); 
       $sql_val .= "('".addslashes($firstname)."', '".addslashes($lastname)."', '".addslashes($email)."'),";   
     }else{
       $emailError .= " $cntr - $email";
     }
   }
     $cntr++;
  }
  $sqlstring = $sql_head . $sql_val;

  $sqlstring = rtrim($sqlstring, ",");

    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
      } else {
        echo "Error: " . $sqlstring . "<br>" . $conn->error;
      }

  // Close the file
  fclose($h);
}

?>