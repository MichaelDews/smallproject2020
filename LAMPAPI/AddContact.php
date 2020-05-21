<?php
	$inData = getRequestInfo();
	
    $firstName = $inData["firstname"];
    $lastName = $inData["lastname"];
    $email = $inData["email"];
    $phone = $inData["phone"];
    $userId = $inData["userId"];
    $date = date("Y-m-d");

	$conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $sql = "insert into Contacts (FirstName,LastName,Email,Phone,DateRecorded,UserID) VALUES ('" . $firstName . "','" . $lastName . "','" . $email . "','" . $phone . "','" . $date . "','" . $userId . "')";
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
