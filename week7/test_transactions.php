<?php

// Use the following line to change error reporting level.
// Default is report all errors, warnings, and parse errors.
error_reporting(E_ERROR);
// If you like viewing all error messages, change above to:
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

$username = 's1234567';
$password = 'SECRET';
$servername = 'talsprddb01.int.its.rmit.edu.au';
$servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
$connection = $servername."/".$servicename;

$conn = oci_connect($username, $password, $connection);
if(!$conn)
{
    $e = oci_error();
    print ("Connection failed. Report the error and exiting. <BR>");
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
else
{
    // This transaction has two statements.
    // Action 1: INSERT a row into movie table
    // Action 2: UPDATE the new row with a dirnumb.
    // First we test with a non-existing dirnumb.
    // Then, we try again with a valid dirnumb.
    $query1 = 'INSERT INTO movie (mvnumb, mvtitle) VALUES (25, \'Lion King\')';
    $stid = oci_parse($conn, $query1);

    // The OCI_NO_AUTO_COMMIT flag tells Oracle not to commit the INSERT immediately
    // This flag is very important if there are more actions within the current transaction.
    $r = oci_execute($stid, OCI_NO_AUTO_COMMIT);
    if (!$r) {
        $e = oci_error($stid);
        print ("insertion failed. Report the error and exiting. <BR>");
        // Uncomment the following line to view the system error.
        // trigger_error(htmlentities($e['message']), E_USER_ERROR);
    }
    else
    {
        // This UPDATE will fail due to ref integrity. There isn't a director with numb 15.
        $query2 = 'UPDATE movie SET dirnumb = 15 WHERE mvnumb = 25';
        // Uncomment the below line to update the dirnumb to a valid dir numb.
        // 1 is a valid direnumb.
        // $query2 = 'UPDATE movie SET dirnumb = 1 WHERE mvnumb = 25';
        $stid = oci_parse($conn, $query2);
        $r = oci_execute($stid, OCI_NO_AUTO_COMMIT);
        if (!$r) {
            $e = oci_error($stid);
            print ("Update failed. So, Insertion is rolled back. <BR>");
            oci_rollback($conn);  // rollback changes to both tables
            // Uncomment the following line to view the system error.
            //trigger_error(htmlentities($e['message']), E_USER_ERROR);
        }
        else
        {
            // Commit the changes to both tables
            // If you reached upto here, that means previous tow actions were successful,
            // so, it is possible to commit the transaction.
            $r = oci_commit($conn);
            print ("Insertion and Update successful. Transaction committed. <BR>");
            // Even at this point it is possible to fail. 
            // Similar to going from Partially Committed to Failed state.
            if (!$r) {
                $e = oci_error($conn);
                oci_rollback($conn);  // rollback changes to both tables
                // Uncomment the following line to view the system error.
                //trigger_error(htmlentities($e['message']), E_USER_ERROR);
            }
        }
    }
}
?>