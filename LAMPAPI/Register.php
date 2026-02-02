<?php
    
    $inData = getRequestInfo();
    $login = $inData["loginName"];
    $password = $inData["password"];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "contact_manager");
    if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $stmt = $conn->prepare("SELECT 1 FROM Users WHERE Login=? LIMIT 1;");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        $exists = ($stmt->num_rows > 0);

        if ($exists) 
        {
            returnWithInfo("Login name has already been used");
        } 
        else 
        {
            $stmt = $conn->prepare("INSERT INTO Users (Login, Password, FirstName, LastName) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $login, $password, $firstName, $lastName);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                returnWithInfo("User registered successfully");
            } else {
                returnWithError("Failed to register user");
            }
            
        }
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
        $retValue = '{"message":"","error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }

    function returnWithInfo( $msg )
    {
        $retValue = '{"message":"' . $msg . '","error":""}';
        sendResultInfoAsJson( $retValue );
    }

?>
