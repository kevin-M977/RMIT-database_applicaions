<html>
 <head>
  <title>PHP Test with Input Forms</title>
 </head>
 <body>
 <?php echo '<p>Establishing a connection to an Oracle database.</p>'; 


// establish a database connection to your Oracle database.
$username = 'USERNAME';
$password = 'PASSWORD';
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

    echo 'User Input 1: Director Firstname: ' . $_POST["dir_fname"] . '<BR>';
    echo 'User Input 2: Director Surname: ' . $_POST["dir_surname"] . '<BR>';


    $query = 'SELECT m.mvnumb, m.mvtitle, d.dirname 
                  FROM movie m JOIN director d ON m.dirnumb = d.dirnumb
                  WHERE LOWER (dirname) LIKE :fn_bv AND
                        LOWER (dirname) LIKE :sn_bv';

    echo 'SQL query built with bind variables: <BR>' . $query . '<BR>';

    $stid = oci_parse($conn, $query);

    // Assign form inputs to PHP variables
    $fn = '%' . $_POST["dir_fname"] . '%'; 
    $sn = '%' . $_POST["dir_surname"] . '%'; 

    // Convert form inputs to lower-case
    $fn = strtolower($fn);
    $sn = strtolower($sn);

    // Run-time binding of PHP variables to Oracle bind variables.
    oci_bind_by_name($stid, ":fn_bv", $fn);
    oci_bind_by_name($stid, ":sn_bv", $sn);

    oci_execute($stid);

    echo "<table border='1'>\n";

    $ncols = oci_num_fields($stid);

    echo "<tr>";

    // Build HTML table Header using fieldnames from Oracle Table
    for ($i = 1; $i <= $ncols; $i++) {
        $column_name  = oci_field_name($stid, $i);
        $column_type  = oci_field_type($stid, $i);

        echo "<td><B>$column_name";
        echo " ($column_type)</B></td>";
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
