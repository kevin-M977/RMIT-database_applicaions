<?php

	if(isset($_POST['Title'])) $Title = $_POST['Title'];
	if(isset($_POST['FirstName'])) $FirstName = $_POST['FirstName'];
    if(isset($_POST['LastName'])) $LastName = $_POST['LastName'];
    if(isset($_POST['DateOfBirth'])) $DateOfBirth = $_POST['DateOfBirth'];
    if(isset($_POST['Address'])) $Address = $_POST['Address'];
    if(isset($_POST['unitNo'])) $unitNo = $_POST['unitNo'];
    if(isset($_POST['Suburb'])) $Suburb = $_POST['Suburb'];
    if(isset($_POST['State'])) $State = $_POST['State'];
    if(isset($_POST['Postcode'])) $Postcode = $_POST['Postcode'];
    if(isset($_POST['PhoneNumber'])) $PhoneNumber = $_POST['PhoneNumber'];
	
	

    $db = mysqli_connect("localhost", "root","", "collection");
    $q = "insert into artefact values(null,'$$Title', '$FirstName', '$LastName', '$DateOfBirth', '$Address','$Suburb','$State','$Postcode','$PhoneNumber')";
    mysqli_query($db, $q);
	exit(0);
?>	