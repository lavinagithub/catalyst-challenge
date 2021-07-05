<?php
// file includes
include("./cred.php");

//var_dump($argv);  
if (isset($argv[1])){
  if ($argv[1] === "--file"){
    echo "users.csv";
  }else if ($argv[1] === "-u"){
      echo "Username = ". $u ."\n";
  }else if ($argv[1] === "-p"){
    echo "Password = ". $p."\n";
  }else if ($argv[1] === "-h"){
    echo "Hostname = ". $h."\n";
  }else if ($argv[1] === "--help"){
    printHelp();
  }else if ($argv[1] === "--create_table"){
    createTable($conn);
  }else{
     echo "Invalid arguments - Try the following command for HELP \n 
     php user_upload.php --help \n\n";
     exit;
  }
  exit;
}

function printHelp(){
  $helpText = "\n Help with directives \n
              • --file [csv file name] – this is the name of the CSV to be parsed\n
              • --create_table – this will cause the MySQL users table to be built (and no further  action will be taken)\n
              • --dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered\n
              • -u – MySQL username\n
              • -p – MySQL password\n
              • -h – MySQL host\n
              • --help – which will output the above list of directives with details.\n" ;
              echo $helpText;
}

include("./gifnoc.php");

function createTable($conn){
  $sql_create = "CREATE TABLE IF NOT EXISTS `users2` (
      `user_id` int(11) AUTO_INCREMENT PRIMARY KEY,
      `firstname` varchar(255) NOT NULL ,
      `surname` varchar(255) NOT NULL ,
      `email` varchar(255) UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
  if ($conn->query($sql_create) === TRUE) {
    echo "Table created successfully\n";
    return true;
  } else {
      //echo "Error: " . $sql_create . "<br>" . $conn->error;
      return $conn->error;
  }
}

// function to check valid email address
function checkValidEmail($email){
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return false;
  }
}

// Open the file for reading
if (($h = fopen("users.csv", "r")) !== FALSE) {
  $cntr = 0;
  // Convert each line into the local $data variable
  $sql_head = " INSERT INTO users2 (firstname, surname, email) VALUES ";
  $sql_val = "";
  $emailError = "";
  $email_arr  = array();
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
  {
    if ($cntr >=1 ){
      $email = strtolower(trim($data[2]));

      if (in_array($data[2], $email_arr)){ // store the error for email duplication
        $dupEmailErr = "\nDuplicate email . $data[2] \n";
      }
      // if conditions checks for a valid email address and checks for duplicates
      if(((checkValidEmail($email) !== false)) && !(in_array($data[2], $email_arr))){
        // remove trailing and leading spaces, covert to lowercase, capitalise first letter, remove special characters from firstname
        $firstname = ucfirst(strtolower(trim($data[0])));
        $firstname = preg_replace('/[^A-Za-z0-9\-]/', '', $firstname); 
        $lastname =  ucfirst(strtolower(trim($data[1]))); 
        $sql_val .= "('".addslashes($firstname)."', '".addslashes($lastname)."', '".addslashes($email)."'),";   
      }else{
        $emailError .= " $cntr - $email";
      }
      // fill all email addresses in an array to check for duplicates in if condition
      array_push($email_arr, $email);      
    }
    $cntr++;
  }
  $sqlstring = $sql_head . $sql_val;

  $sqlstring = rtrim($sqlstring, ",");
   
  if (createTable($conn) === true){
    if ($conn->query($sqlstring) === TRUE) {
      echo "\nNew records added successfully\n ";
    } else {
      echo "\nError: " . $sqlstring . "<br>" . $conn->error;
    }
  }else{
    echo " \nError" .$conn->error;
  }
  // Close the file
  fclose($h);
}
?>