<html>
 <head>
  <title>PHP Test with OCI_Connect</title>
 </head>
 <body>
 <?php echo '<p>Establishing a connection to an Oracle database.</p>';


// establish a database connection to your Oracle database.
$username = 'YOUR STUDENT NO: S3nnnnnn';
$password = 'YOUR ORACLE PASSWORD'; //DO NOT enter your RMIT password
$servername = 'talsprddb01.int.its.rmit.edu.au';
$servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
$connection = $servername."/".$servicename;

$conn = oci_connect($username, $password, $connection);
if(!$conn)
{
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
else
{
    echo "<p>Successfully connected to CSAMPR1.ITS.RMIT.EDU.AU.</p>";

    // Testing a generic SELECT SQL 
    // This SQL will work even if you do not have any tables created on your database accoount.
    // 
    $stid = oci_parse($conn, 'SELECT sysdate FROM dual');

    // The define MUST be done before executing
    // It assigns a php variable to receive SQL query result
    oci_define_by_name($stid, 'SYSDATE', $oracle_sys_date);

    oci_execute($stid);

    // Each fetch populates the previously defined variables with the next row's data
    // In this example, there would be only ONE row in the result.
    // However, queries do produce multi-row resulsts, it is looped through until
    // all of the query result rows are processed.
    while (oci_fetch($stid))
    {
        echo "Current System Date in Oracle Database is $oracle_sys_date<br>\n";
    }


    // testing SELECT SQL from movie table
    $stid = oci_parse($conn, 'SELECT * FROM movie');
    oci_execute($stid);

    echo "<table border='1'>\n";

    $ncols = oci_num_fields($stid);

    echo "<tr>";

    // Build HTML table Header using fieldnames from Oracle Table
    for ($i = 1; $i <= $ncols; $i++) {
        $column_name  = oci_field_name($stid, $i);
        $column_type  = oci_field_type($stid, $i);

        echo "<td><b>$column_name";
        echo " ($column_type)</b></td>";
    }
    echo "</tr>\n";

    // Populate the table with data fetched from the Oracle table
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr>\n";
        foreach ($row as $item) {
            echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
}

oci_close($conn);

?>

 </body>
</html>