<?php
	// Assumes the input is JSON in the format of {"userID":"", "contactID":""}
	
	$inData = getRequestInfo();
	
	$contactID = nosql($inData["contactID"]);

	$conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $sql = "DELETE from Contacts where ID=$contactID";
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