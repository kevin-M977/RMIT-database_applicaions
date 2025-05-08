<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Australia Ballot Paper</title>
    <link rel="stylesheet" href="1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="address.js"></script>
</head>
<body style="background-color:rgb(128, 128, 128);">
    <style>
        img {
            width: 12%;
        }
    </style>
    <div class="container">
        <div class="row">
            <img src="Logo.webp" alt="Logo">
            <h1 class="title">House of Representatives </br>Ballot Paper</h1>
            <h1 class="title"></h1>
            <div class="card">
                <div class="card-header">
                    <h1 class="title1">Thank you for your vote</h1>
                </div>
                <?php
                $PreferResult = $_POST['preference'];
                $username = 's3831370';
                $password = '3831370!';
                $servername = 'talsprddb01.int.its.rmit.edu.au';
                $servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
                //
                $connection = $servername . "/" . $servicename;
                $conn = oci_connect($username, $password, $connection);

                $CandidatesID[] = null;
                $CandidatesFName[] = null;
                $CandidatesSName[] = null;
                $CandidateResult[] = null;

                $i = 0;

                $SSQL = "select CANDIDATES_ID, FIRSTNAME,LASTNAME from CANDIDATE";

                $Fstid = oci_parse($conn, $SSQL);

                oci_execute($Fstid);

                while ($Frow = oci_fetch_array($Fstid, OCI_ASSOC + OCI_RETURN_NULLS)) {

                    $CandidatesID[] = $Frow["CANDIDATES_ID"];
                    $CandidatesFName[] = $Frow["FIRSTNAME"];
                    $CandidatesSName[] = $Frow["LASTNAME"];
                    $CandidatesName[] = $Frow["LASTNAME" + "FIRSTNAME"];
                    $i++;
                }

                for ($a = 1; $a <= $i; $a++) {
                    if ($PreferResult[$a] == 1) {
                        $CandidateResult[1] = $CandidatesID[$a];
                    } elseif ($PreferResult[$a] == 2) {
                        $CandidateResult[2] = $CandidatesID[$a];
                    } else if ($PreferResult[$a] == 3) {
                        $CandidateResult[3] = $CandidatesID[$a];
                    } else if ($PreferResult[$a] == 4) {
                        $CandidateResult[4] = $CandidatesID[$a];
                    } else {
                    }
                }

                ksort($CandidateResult);

                for ($b = 1; $b <= sizeof($CandidateResult) - 1; $b++) {
                    $CandidateResult = $CandidateResult[$b];
                    echo "<li>You Choose Candidate $b: $CandidatesName[$CandidateResult]</li>";
                }


                $InquireLBallotID = "Select BALLOTID FROM BALLOT ORDER BY BALLOTID DESC FETCH FIRST 1 ROWS ONLY";
                $PrepareLBallotID = oci_parse($conn, $InquireLBallotID);
                oci_execute($PrepareLBallotID);

                $row = oci_fetch_array($PrepareLBallotID, OCI_ASSOC + OCI_RETURN_NULLS);

                if (!empty($row)) {
                    foreach ($row as $item) {
                        $LBallotID = $item;
                    }
                }

                $queryWriteBallot = "INSERT INTO BALLOT (BALLOTID, FIRSTPRECANDI, SECONDPRECANDI, THIRDPRECANDI, FORTHPRECANDI, POLLINGSTATIONNAME, ELECEVENT_ELECTIONCODE) VALUES ($LBallotID+1,  $resultCandidate[1], $resultCandidate[2], $resultCandidate[3], $resultCandidate[4], 1, 1)";
                $prepareWriteBallot = oci_parse($conn, $queryWriteBallot);
                oci_execute($prepareWriteBallot);




                ?>
                </h2><a class="btn btn-danger" href="Voter_details.html" role="button">Back to user details</a>
            </div>

            <div class="card-footer text-muted">
            </div>
        </div>
    </div>
</body>

</html>