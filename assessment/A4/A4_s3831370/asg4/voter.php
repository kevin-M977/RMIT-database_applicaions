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

<body>

  <body style="background-color:rgb(233, 61, 61);">
    <style>
      img {
        width: 12%;
      }
    </style>
    <div class="container">
      <div class="row">
        <img src="Logo.webp" alt="Logo">
        <h1 class="title">House of Representatives </br>Ballot Paper</h1>
        <div class="card">
          <div class="card-header">
            <h1 class="title1">Victoria </br> Electoral Division of Higgins</h1>
          </div>

          <?php

          $username = 's3831370';
          $password = '3831370!';
          $servername = 'talsprddb01.int.its.rmit.edu.au';
          $servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
          //
          $connection = $servername . "/" . $servicename;
          $conn = oci_connect($username, $password, $connection);
          $DoubleCheckVoter = false;
          //
          $Fname = $_POST['Firstname'];
          $Lname = $_POST['Lastname'];
          $Dob = $_POST['DOB'];
          $Address = $_POST['Address'];
          $ApartmentNo = $_POST['ApartmentNo'];
          $Suburb = $_POST['Suburb'];
          $State = $_POST['State'];
          $Postcode = $_POST['Postcode'];
          $PhoneNumber = $_POST['PhoneNumber'];
          //
          $FSQL = "select * from VOTER_REGISTRY where FIRSTNAME in '$Fname' and LASTNAME in '$Lname' and RESIDENTIALADDRESS in '$Address' FETCH FIRST 1 ROWS ONLY";
          $Fstid = oci_parse($conn, $FSQL);
          oci_execute($Fstid);
          $Frow = oci_fetch_array($Fstid, OCI_ASSOC + OCI_RETURN_NULLS);
          //
          $UserID = null;
          if (!empty($Frow)) {
            foreach ($Frow as $item) {
              $Fcache[] = $item;
              $UserID = $Fcache[0];
              $electorateID = 0;
            }
            if (empty($Fcache[12]) | $Fcache[12] == 0) {
              $DoubleCheckVoter = false;
            } else {
              $DoubleCheckVoter = true;
            }

            if (!$DoubleCheckVoter) {
              Voting($UserID, $electorateID);
            } else {
              echo "Please check your details again !! please try again";
            }
          } else {
            echo "<h3> Error Please Return User Details Page!!</h3> ";
            echo '<a class="btn btn-danger" href="Voter_details.html" role="button">Back to user details</a>';
          }
          //
          function Voting($VoterID, $electorateID)
          {
            $username = 's3831370';
            $password = '3831370!';
            $servername = 'talsprddb01.int.its.rmit.edu.au';
            $servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
            //
            $connection = $servername . "/" . $servicename;
            $conn = oci_connect($username, $password, $connection);

            avoidDoubleVote($VoterID);
            $CandidatesID[] = null;
            $CandidatesFName[] = null;
            $CandidatesLName[] = null;
            $CandidatesParty[] = null;
            $CandidatesLogo[] = null;
            $i = 0;
            $SSQL = "select CANDIDATER_ID, FIRSTNAME, LASTNAME,PARTYCODE from CANDIDATES";
            $Sstid = oci_parse($conn, $SSQL);
            oci_execute($Sstid);
            while ($Srow = oci_fetch_array($Sstid, OCI_ASSOC + OCI_RETURN_NULLS)) {


              $CandidatesID[] = $Srow["CANDIDATE_NO"];
              $CandidatesFName[] = $Srow["FIRSTNAME"];
              $CandidatesLName[] = $Srow["LASTNAME"];
              $PartyCode = $Srow["PARTYCODE"];

              $TSQL = "SELECT NAMEOFTHEPARTY, PARTYLOGO FROM POLOTICAL_PARTY WHERE PARTYCODE IN '$PartyCode'";
              $Tstid = oci_parse($conn, $TSQL);
              oci_execute($Tstid);

              while ($Trow = oci_fetch_array($Tstid, OCI_ASSOC + OCI_RETURN_NULLS)) {

                $CandidatesParty[] = $Trow["NAMEOFTHEPARTY"];
                $CandidatesLogo[] = $Trow["PARTYLOGO"];
              }

              $i++;
            }
            echo "<p>Tick the boxes from 1 to $i in the order of your choice.</p>";
            echo "<p>The order of selection is the order of voting. You select result will show you after submit.</p>";
            echo "<form action='Final.php' method='post'>";
            for ($a = 1; $a <= $i; $a++) {
              echo '<div class="form-inline">';
              echo '<img src="data:image/jpeg;base64,' . base64_encode($CandidatesLogo[$a]->load()) . '" />';
              echo "<label class='my-1 mr-2' for='inlineFormCustomSelectPref'> $CandidatesFName[$a] ($CandidatesLName[$a])</label>";
              echo "<input type='text' class='form-control' name='preference[$a]' id='preference' placeholder='Enter number for your preference' required>";
              echo "</br></div>";
            }
            echo "</div>";

            echo "<button type='submit' class='btn btn-dark'>Submit</button>";
          }

          function avoidDoubleVote($voterID)
          {
            $username = 's3831370';
            $password = '3831370!';
            $servername = 'talsprddb01.int.its.rmit.edu.au';
            $servicename = 'CSAMPR1.ITS.RMIT.EDU.AU';
            //
            $connection = $servername . "/" . $servicename;
            $conn = oci_connect($username, $password, $connection);


            $querySetVotedFlag = "UPDATE VOTERREGISTRY SET PREVIOUSVOTEE = 1 WHERE VOTERID IN '$voterID'";
            $prepareSetVotedFlag = oci_parse($conn, $querySetVotedFlag);
            oci_execute($prepareSetVotedFlag);
          }

          oci_close($conn);
          ?>
          </h2><a class="btn btn-submit" href="Voter_details.html" role="button">Return</a>
        </div>
        <div class="card-footer text-muted">
        </div>
      </div>