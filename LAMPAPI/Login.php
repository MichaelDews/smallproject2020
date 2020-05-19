<?php

  $inData = getRequestInfo();

  $id = 0;
  firstName = "";
  lastName = "";

  $conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
  if($conn->connect_error)
  {
    returnWithError( $conn->connect->connect_error);
  }
  else
  {
    $sql = "SELECT ID, FirstName, LastName FROM Users where Username = '" . $inData["Username"] . "' and Password='" . $inData["Password"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0)
    {
      $row = $result->fetch_assoc();
      $firstName = $row["FirstName"];
			$lastName = $row["LastName"];
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
 ?>
