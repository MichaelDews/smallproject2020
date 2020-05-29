<?php
	$inData = getRequestInfo();

	$searchName = nosql($inData["query"]);
	$userID = nosql($inData["ID"]);
	$search = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "username_group3", "cop4331Group3!", "username_group3");

	if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		// search is by first name- easy to change
		$sql = "SELECT * FROM Contacts where (FirstName like '$searchName%' or LastName like '$searchName%' or Email like '$searchName%' or Phone like '$searchName%') and UserID=$userID";
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
				$search .= '{"id": "' . $row["ID"] . '", "firstname": "' . $row["FirstName"] . '", "lastName": "' . $row["LastName"] . '", "email": "' . $row["Email"] . '", "phone": "' . $row["Phone"] . '", "DateRecorded": "' . $row["DateRecorded"] . '"}';
			}
		}
		else {
			
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
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
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
