#  Catalyst Challenge Test
##### PHP version 7.4.2
#####  Database name is _'catalyst-test'_ / Table name is _'users'_
##### The MySQL table has these fields:
- name
- surname
- email (email isset to a UNIQUE index)

#####  File name with the code is _'user_upload.php'_
#####  CSV file name with data is _'users.csv'_

###  HELP
- --file [csv file name] – this is the name of the CSV to be parsed
- --create_table – this will cause the MySQL users table to be built (and no further action will be taken) 
- --dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered
- -u – MySQL username
- -p – MySQL password
- -h – MySQL host
- -db – MySQL host

### Run the following commands
- (Create table) 
php user_upload.php -u=root -p=root -h=localhost -db=catalyst_test --create_table 
- (Dry run)
php user_upload.php -u=root -p=root -h=localhost -db=catalyst_test --file={PathToFile}/users.csv --dry_run
- (Insert data) 
 php user_upload.php  -u=root -p=root -h=localhost -db=catalyst_test --file={PathToFile}/users.csv --insert_data
- (Drop table) 
php user_upload.php -u=root -p=root -h=localhost -db=catalyst_test --drop_table
- (Help) 
php user_upload.php --help 

### Version control used is _'git'_ 
- development process history can be seen in the commits history


#### Data is validated before insertion
-  Name and surname field is be set to be capitalised e.g. from “john” to “John” before being inserted into DB
- Emails are converted to lower case before being inserted into DB
- The script validates the email address before inserting,  e.g. format “xxxx@asdf@asdf” is not a legal format). 
- In case that an email is invalid, no insert is made to database and an error message is reported to STDOUT.

### Thank you
> I'd like to thank you for giving me the opportunity to attempt this test. 
> I have tried different combinations to make sure the code displays 
> correct messages with different combinations.
> There is definitely scope for refactoring. 
> I will work on it further. However, my code is here for your review 