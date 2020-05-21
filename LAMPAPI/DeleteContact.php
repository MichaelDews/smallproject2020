<?php
	// Assumes the input is JSON in the format of {"userID":"", "contactID":""}
	
	$inData = getRequestInfo();
	
	$userID = nosql($inData["userID"]);
	$contactID = nosql($inData["contactID"]);

	$conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
	if ($conn->connect_error){
		returnWithError($conn->connect_error);
	}
	else{
		$sql = "select Name from Contacts where UserID = " . $userID . " and ID = " . $contactID;
		$result = $conn->query($sql);
		if ($result->num_rows == 0){
			returnWithError("Contact not found");
		}
		else{
			$sql = "delete from Contacts where UserID = " . $userID . " and ID = " . $contactID;
			if( $result = $conn->query($sql) != TRUE ){
				returnWithError( $conn->error );
			}
		}
		$conn->close();
	}
	
	returnWithError("");
	
	// Parse JSON file input
	function getRequestInfo(){
		return json_decode(file_get_contents('php://input'), true);
	}
	
	function sendAsJSON($obj){
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendAsJson( $retValue );
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