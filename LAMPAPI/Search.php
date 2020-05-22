<?php
	$inData = getRequestInfo();
	
	$search = "";
	$searchCount = 0;
	$searchName = nosql($inData["search"]);
	$userID = nosql($inData["ID"]);

	$conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");
	
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// search is by first name- easy to change
		$sql = "select * from CONTACTS where FirstName like '%" . $searchName . "%' AND $userID = " . $userID;
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				if( $searchCount > 0 )
				{
					$search .= ",";
				}
				$searchCount++;
				$search .= '"' . $row["ID"] . ' | ' . $row["FirstName"] . ' | ' . $row["LastName"] . ' | ' . $row["Phone"] . ' | ' . $row["Email"] . ' | ' . $row["DateRecorded"] . '"';
			}
		}
		else
		{
			returnWithError( "Contact not found" );
		}
		$conn->close();
	}
	
	returnWithInfo( $search );
	
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
		$retValue = '{"result":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $search )
	{
		$retValue = '{"result":[' . $search . '],"error":""}';
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
		$string = str_replace( '"', '\\"', $string );
		$string = str_replace( "%", "\\%", $string );
		$string = str_replace( "'", "\\'", $string );
		$string = str_replace( "\\", "\\\\", $string );
		$string = str_replace( "_", "\\_", $string );
		return $string;
    }
	
?>