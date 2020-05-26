<?php
	$inData = getRequestInfo();

    $contactID = $inData["ID"];
    $newFirstName = $inData["newfirstName"];
		$newLastName = $inData["newlastName"];
		$newEmail = $inData["newEmail"];
		$newPhone = $inData["newPhone"];
		$newDate = $inData["newDate"];


	$conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
	if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
        $sql = "update Contacts set FirstName='$newFirstName', LastName='$newLastName', Email='$newEmail', Phone='$newPhone', DateRecorded='$newDate' where ID=$contactID";
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
		}
		$conn->close();
	}

	returnWithError("");

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
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

?>
