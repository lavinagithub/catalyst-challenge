<?php
//var_dump($argv);  
if (isset($argv[1])){
  $credErr = "";
  $fileArr = (preg_grep('/^--file=*/', $argv));
  $fileArrKey = array_keys((preg_grep('/^--file=*/', $argv)));
  if (count($fileArrKey) > 0){
    $fileArr2 = explode("=",$fileArr[$fileArrKey[0]]);
    $file = $fileArr2[1];
  }else {
    $credErr .= "\n File path and name not provided \n";
  }

  $uArr = (preg_grep('/^-u=*/', $argv));
  $uArrKey = array_keys((preg_grep('/^-u=*/', $argv)));
  if (count($uArrKey) > 0){
    $uArr2 = explode("=",$uArr[$uArrKey[0]]);
    $u = $uArr2[1];
  }else{
      $credErr .= "\n Username not provided \n";
  }

  $pArr = (preg_grep('/^-p=*/', $argv) );
  $pArrKey = array_keys((preg_grep('/^-p=*/', $argv)));
  if (count($pArr) > 0){
    $pArr2 = explode("=",$pArr[$pArrKey[0]]);
    $p = $pArr2[1];
  }else{
      $credErr .= "\n Password not provided \n";
  }

  $hArr = (preg_grep('/^-h=*/', $argv) );
  $hArrKey = array_keys((preg_grep('/^-h=*/', $argv)));
  if (count($hArr) > 0){
    $hArr2 = explode("=",$hArr[$hArrKey[0]]);
    $h = $hArr2[1];
  }else{
      $credErr .= "\n Hostname not provided \n";
  }

  $dbArr = (preg_grep('/^-db=*/', $argv));
  $dbArrKey = array_keys((preg_grep('/^-db=*/', $argv)));
  if (count($dbArr) > 0){
    $dbArr2 = explode("=",$dbArr[$dbArrKey[0]]);
    $db = $dbArr2[1];
  }else{
      $credErr .= "\n Database name not provided \n";
  }

  //echo "$h, $u, $p, $db, $file";
  //$fileArr = (preg_grep('/^--file=*/', $argv));

  if (in_array('--create_table', $argv)){
    if (isset($h) && isset($u) && isset($p) && isset($db)){
      $conn =  mysqli_connect($h, $u, $p, $db);
      // Check connection
      if (!$conn){
        die("\nConnection failed: " . mysqli_connect_error());
      } else {
        echo "\nSuccessfully connected to the database \n";
        createTable($conn);
      }
    }else{ // if credentials are not set
      echo "\n Could not connect \n";
      echo $credErr;
      echo "\n Use --help for help with directives \n";
      exit;
    }
  }

  if (in_array('--help', $argv)){
    printHelp();
  }

}else{
  echo "Invalid arguments - Try the following command for HELP \n 
  php user_upload.php --help \n\n";
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
              • -db – MySQL database name\n
              • --help – which will output the above list of directives with details.\n" ;
              echo $helpText;
}


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
      echo "Error: " . $sql_create . "<br>" . $conn->error;
      //return $conn->error;
  }
}

// function to check valid email address
function checkValidEmail($email){
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return false;
  }
}

function readFileInsertData($conn,$file){
  // Open the file for reading
  if (($fo = fopen($file, "r")) !== FALSE) {
    $cntr = 0;
    // Convert each line into the local $data variable
    $sql_head = " INSERT INTO users2 (firstname, surname, email) VALUES ";
    $sql_val = "";
    $emailError = "";
    $email_arr  = array();
    while (($data = fgetcsv($fo, 1000, ",")) !== FALSE) 
    {
      if ($cntr >=1 ){
        $email = strtolower(trim($data[2]));

        if (in_array($data[2], $email_arr)){ // store the error for email duplication
          $dupEmailErr .= "\nDuplicate email . $data[2] \n";
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
        echo $dupEmailErr;
      } else {
        echo "\nError: " . $sqlstring . "<br>" . $conn->error;
      }
    }else{
      echo " \nError" .$conn->error;
    }
    // Close the file
    fclose($h);
  }
}
?>