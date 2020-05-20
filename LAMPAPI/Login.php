<?php

  $inData = getRequestInfo();

  $id = 0;
  // grab input data and prevent sql injections
  $username = nosql($inData["username"]);
  $password = nosql($inData["password"]);
  firstName = "";
  lastName = "";

  $conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
  if($conn->connect_error)
  {
    returnWithError( $conn->connect->connect_error);
  }
  else
  {
    $sql = "SELECT ID,firstName,lastName FROM Users where Login='" . $username . "' and Password='" . $password . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0)
    {
      $row = $result->fetch_assoc();
      $firstName = $row["firstName"];
			$lastName = $row["lastName"];
			$id = $row["ID"];

      returnWithInfo($firstName, $lastName, $id );
    }
    else
    {
      returnWithError( "No Recods Found" );
    }
    $conn->close();
  }

  function getRequestInfo()
  {
    return json_decode(file_get_contents('php://input'), true);
  }

  function sendResultInfoAsJson( $obj )
  {
    header('Content-type: application/json');
    echo $obj;
  }

  function returnWithError( $err )
  {
    $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
  }

  function returnWithInfo( $firstName, $lastName, $id )
  {
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
  }

  function nosql( $string )
  {
    // this gets rid of common sql injections in the user input
    $string = str_replace( "NUL", "\\0", $string );
    $string = str_replace( "BS", "\\b", $string );
    $string = str_replace( "TAB", "\\t", $string );
    $string = str_replace( "LF", "\\n", $string );
    $string = str_replace( "CR", "\\r", $string );
    $string = str_replace( "SUB", "\\z", $string );
    $string = str_replace( '"', "\\Z", $string );
    $string = str_replace( "%", "\\%", $string );
    $string = str_replace( "'", "\\'", $string );
    $string = str_replace( "\\", "\\\\", $string );
    $string = str_replace( "_", "\\_", $string );
    return $string;
  }
 ?>
