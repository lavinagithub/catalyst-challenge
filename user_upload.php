<?php

//var_dump($argv);  
if (isset($argv[1])){
  $credErr = "";
  $fileArr = (preg_grep('/^--file=*/', $argv));
  $fileArrKey = array_keys((preg_grep('/^--file=*/', $argv)));
  if (count($fileArrKey) > 0){
    $fileArr2 = explode("=",$fileArr[$fileArrKey[0]]);
    $pathTofile = $fileArr2[1];
  }else {
    //$credErr .= "\n File path and name not provided \n";
    $fileErr = "\n File path and name not correctly provided \n";
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

  if (in_array('--help', $argv)){
    printHelp();
  }
  if (isset($h) && isset($u) && isset($p) && isset($db)){
    $conn =  @mysqli_connect($h, $u, $p, $db);
    if (!$conn){
      die("\nConnection failed: " . mysqli_connect_error());
    } 
  } else{ // if credentials are not set
        echo "\n Could not connect \n";
        echo $credErr;
        echo "\n Use --help for help with directives \n";
        exit;
  }
  
  if (in_array('--create_table', $argv)){
    if (connectDB($conn) === true){
        echo " \nSuccessfully connected to the db \n";
        createTable($conn);
    }else{
      die("\nConnection failed: " . mysqli_connect_error());
    }
  }
  //dry_run
  if (in_array('--dry_run', $argv)){
    if (connectDB($conn) === true){
      echo "\nSuccessfully connected to the database \n";
      if ((checkTableExists($conn)) === true) {
        //DO A DRY RUN
        dryRun($conn,$pathTofile);
      }
      else
      {
        echo "\nTable does not exist \n 
              Use this command to create a table \n
              php user_upload.php --file={pathToFile} -u=root -p=root -h=localhost -db=catalyst_test --create_table \n\n";
      }   
    } else {
        die("\nConnection failed: " . mysqli_connect_error());
        // check if table exists
    }
  }

  // insert_data

  if (in_array('--insert_data', $argv)){
    if (connectDB($conn) === true){
      echo "\nSuccessfully connected to the database \n";
      if ((checkTableExists($conn)) === true) {
        //DO A DRY RUN
        readFileInsertData($conn,$pathTofile);
      }
      else
      {
        echo "\nTable does not exist \n 
              Use this command to create a table \n
              php user_upload.php --file={pathToFile} -u=root -p=root -h=localhost -db=catalyst_test --create_table \n\n";
      }   
    } else {
        die("\nConnection failed: " . mysqli_connect_error());
        // check if table exists
    }
  }

  if (in_array('--drop_table', $argv)){
    dropTable($conn);
  }


  
}else{
  echo "Invalid arguments - Try the following command for HELP \n 
  php user_upload.php --help \n\n";
  exit;
}

/*** Functions  */

// connection to database
function connectDB($conn){
    if (!$conn){
      // die("\nConnection failed: " . mysqli_connect_error());
        return false;
      } else {
        //echo "\nSuccessfully connected to the database \n";
        return true;
      }
  }

// print Help menu
function printHelp(){
  $helpText = "\n Help with directives \n
              • --file [csv file name] – this is the name of the CSV to be parsed\n
              • --create_table – this will cause the MySQL users table to be built (and no further  action will be taken)\n
              • --drop_table – this will cause the MySQL users table to be dropped\n
              • --dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered\n
              • -u – MySQL username\n
              • -p – MySQL password\n
              • -h – MySQL host\n
              • -db – MySQL database name\n
              • --help – which will output the above list of directives with details.\n" ;
              echo $helpText;
}

// create the table
function createTable($conn){
   if ((checkTableExists($conn)) === false) {
      $sql_create = "CREATE TABLE IF NOT EXISTS `users` (
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
      }
    }else{
       echo "\nTable already exists \n Use --help for help with directives \n\n";
    }
}

// drop the table
function dropTable($conn){
  if ((checkTableExists($conn)) === true) {
        //Drop the table
    $sql_drop = "DROP TABLE  `users` ";
    if ($conn->query($sql_drop) === TRUE) {
      echo "Table dropped\n";
      return true;
    }else {
      echo "Error: " . $sql_drop . "<br>" . $conn->error;   
    }
  }else{
    echo "\nTable does not exist \n 
          Use --help for help with directives \n\n";
  }
}

// function checks if the table exists 
function checkTableExists($conn){
  if ($conn->query("select 1 from `users` LIMIT 1") !== false) {
    return true;
  }else{
    return false;
  }
}
// function to check valid email address
function checkValidEmail($email){
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return false;
  }
}

// dry run counts the number of records to be inserted / STDOUT the records not inserted
function dryRun($conn,$pathTofile){
  // Open the file for reading
  if (($myFile = fopen($pathTofile, "r")) !== FALSE) {
    $cntr = 0;
    $record_count = 0;
    $emailError = "";
    $email_arr  = array();
    while (($data = fgetcsv($myFile, 1000, ",")) !== FALSE) 
    {
      if ($cntr >=1 ){
        $email = strtolower(trim($data[2]));
        // if (in_array($data[2], $email_arr)){ // store the error for email duplication
        //   $dupEmailErr .= "\nDuplicate email . $data[2] \n";
        // }
        // if conditions checks for a valid email address and checks for duplicates
        if(((checkValidEmail($email) !== false)) && !(in_array($data[2], $email_arr))){
          $record_count++;
        }else{
          $emailError .= "\n $cntr - $email \n";
        }
        // fill all email addresses in an array to check for duplicates in if condition
        array_push($email_arr, $email);      
      }
      $cntr++;
    }
    
    // Close the file
    fclose($myFile);
    $dry_run_results = "\nTotal records to be inserted are $record_count \n
          The following records will not be inserted $emailError \n";
          echo $dry_run_results;
    
  }
}

// Enter data into table users
function readFileInsertData($conn,$pathTofile){
  // Open the file for reading
  if (($myFile = fopen($pathTofile, "r")) !== FALSE) {
    $cntr = 0;
    // Convert each line into the local $data variable
    $sql_head = " INSERT INTO users (firstname, surname, email) VALUES ";
    $sql_val = "";
    $emailError = "";
    $email_arr  = array();
    while (($data = fgetcsv($myFile, 1000, ",")) !== FALSE) 
    {
      if ($cntr >=1 ){
        $email = strtolower(trim($data[2]));

        // if (in_array($data[2], $email_arr)){ // store the error for email duplication
        //   $dupEmailErr .= "\nDuplicate email . $data[2] \n";
        // }
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
        //echo $dupEmailErr;
      } else {
        echo "\nError: " . $sqlstring . "<br>" . $conn->error;
      }
    }else{
      echo " \nError" .$conn->error;
    }
    // Close the file
    fclose($myFile);
  }
  $insert_results = "\n The following records are not inserted $emailError \n";
  echo $insert_results;
}
?>