<?php
// file includes
//include("./gifnoc.php");

// function to check valid email address
function checkValidEmail($email){
 if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       return false;
      }
}

function createTable($conn){
  $sql_create = "CREATE TABLE `users` (
      `user_id` int(11) INT AUTO_INCREMENT PRIMARY KEY,
      `firstname` varchar(255) NOT NULL ,
      `surname` varchar(255) NOT NULL ,
      `email` varchar(255) UNIQUE,
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
     if ($conn->query($sql_create) === TRUE) {
      echo "Table created successfully";
      } else {
        echo "Error: " . $sql_create . "<br>" . $conn->error;
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
  $email_arr  = array();
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
    if ($cntr >=1 ){
      //echo $cntr;
      //print_r($data); 
  
      $email = strtolower(trim($data[2]));
       //$checkarr = (in_array($data[2], $email_arr));
     // echo $data[2] . " " .$checkarr;

      if(((checkValidEmail($email) !== false)) && !(in_array($data[2], $email_arr))){
        // echo "here";
        $firstname = ucfirst(strtolower(trim($data[0]))); 
        $lastname =  ucfirst(strtolower(trim($data[1]))); 
        $sql_val .= "('".addslashes($firstname)."', '".addslashes($lastname)."', '".addslashes($email)."'),";   
      }else{
        $emailError .= " $cntr - $email";
      }
      array_push($email_arr, $email);
     // print_r($email_arr);
      
      
    }
    $cntr++;
  }
  echo $sqlstring = $sql_head . $sql_val;

  // echo $sqlstring = rtrim($sqlstring, ",");
  //  if (createTable($conn)){
  //   if ($conn->query($sqlstring) === TRUE) {
  //     echo "New record created successfully";
  //     } else {
  //       echo "Error: " . $sqlstring . "<br>" . $conn->error;
  //     }
  //   }

  // Close the file
  fclose($h);
}

?>