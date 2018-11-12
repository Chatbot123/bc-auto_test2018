<?php

$method = $_SERVER['REQUEST_METHOD'];
//process only when method id post
if($method == 'POST')
{
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

//Setup

	if($json->queryResult->intent->displayName=='Raise_ticket_intent - GetnameGetissue')
	{
		//if(isset($json->queryResult->queryText))
		//{ $sh_desc = $json->queryResult->queryText; }

		if(isset($json->queryResult->parameters->name))
		{ $name = $json->queryResult->parameters->name; }
		
		if(isset($json->queryResult->parameters->issue))
		{ $sh_desc = $json->queryResult->parameters->issue; }

		$sh_desc = strtolower($sh_desc);
		//$sh_desc = "Testing";
		//$name = "someone";
		$instance = "dev60887";
		$username = "admin";
		$password = "Avik.17.jan";
		$table = "incident";
		//$jsonobj = "{\"short_description\":$sh_desc,\"priority\":\"1\",\"Caller_id\":\"someone\"}";
		$jsonobj = array('short_description' => $sh_desc);
             	$jsonobj = json_encode($jsonobj);	

		//$jsonobj = "{\"short_description\":$sh_desc,\"priority\":\"1\",\"Caller_id\":$name}";
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=insert";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonobj);
		}
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$incident_no =  $jsonoutput->records[0]->number;
		$sys_id = $jsonoutput->records[0]->sys_id;
		
		$speech = "Thanks ".$name."! Incident Created Successfully for issue " . $sh_desc . " and your incident number is " . $incident_no;
		$speech .= " Sys_id is ".$sys_id;
		$speech .= "\r\n";
		$speech .= " Thanks for contacting us. Are you satisfied with the response?";
		//echo $speech;
		

	}
	if($json->queryResult->intent->displayName=='Get_Status_ticket'||$json->queryResult->intent->displayName=='Get_Status_ticket - ticketinputagain')
	{
		
		if(isset($json->queryResult->parameters->Raisedate))
		{ $Raisedate = $json->queryResult->parameters->Raisedate; }
		
		if(isset($json->queryResult->parameters->Ticketno))
		{ $Ticketno = $json->queryResult->parameters->Ticketno; }
		str_pad($Ticketno, 7, '0', STR_PAD_LEFT);
		$Raisedate = substr($Raisedate, 0, 10);
			
		$instance = "dev60887";
		$username = "admin";
		$password = "Avik.17.jan";
		$table = "incident";
		
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=getRecords&sysparm_query=numberENDSWITH".$Ticketno."^sys_created_onSTARTSWITH".$Raisedate;
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$assigned_to =  $jsonoutput->records[0]->assigned_to;
		$number =  $jsonoutput->records[0]->number;
		$state =  $jsonoutput->records[0]->state;
		$sys_updated_by = $jsonoutput->records[0]->sys_updated_by;
		$sys_updated_on = $jsonoutput->records[0]->sys_updated_on;
		$short_description = $jsonoutput->records[0]->short_description;
		
		
		if($assigned_to=='')
		{
			$assigned_to = 'no one';
		}
		
		switch($state){
		    case 1:
			$dis_state = "New";
			break;
		    case 2:
			$dis_state = "In Progress";
			break;
		    case 3:
			$dis_state = "On Hold";
			break;
		    case 7:
			$dis_state = "Closed";
			break;
		   
		}

		$speech = "Incident ".$number." is currently assigned to ".$assigned_to.". Current status of  the incident is ".$dis_state." . This incident was last updated by ".$sys_updated_by." on ".$sys_updated_on;
		$speech .= " The incident was raised for the issue ".$short_description;
		if($number == ''){ $speech="";}	
		
		//$speech = "Thanks ".$name."! Incident Created Successfully for issue " . $sh_desc . " and your incident number is " . $incident_no;
		//echo $speech;
		

	}
	if($json->queryResult->intent->displayName=='Raise_ticket_intent - GetnameGetissue - yes - yes')
	{
		//if(isset($json->queryResult->queryText))
		//{ $sh_desc = $json->queryResult->queryText; }

		if(isset($json->queryResult->parameters->name))
		{ $name = $json->queryResult->parameters->name; }
		
		if(isset($json->queryResult->parameters->issue))
		{ $sh_desc = $json->queryResult->parameters->issue; }

		$sh_desc = strtolower($sh_desc);
		//$sh_desc = "Testing";
		//$name = "someone";
		$instance = "dev60887";
		$username = "admin";
		$password = "Avik.17.jan";
		$table = "incident";
		//$jsonobj = "{\"short_description\":$sh_desc,\"priority\":\"1\",\"Caller_id\":\"someone\"}";
		$jsonobj = array('short_description' => $sh_desc);
             	$jsonobj = json_encode($jsonobj);	

		//$jsonobj = "{\"short_description\":$sh_desc,\"priority\":\"1\",\"Caller_id\":$name}";
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=insert";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonobj);
		}
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$incident_no =  $jsonoutput->records[0]->number;
		$sys_id = $jsonoutput->records[0]->sys_id;
		$json->queryResult->parameters->incident_num= $incident_no;
		$json->queryResult->parameters->sys_id= $sys_id;
		echo $json->queryResult->parameters->incident_num;
		echo $json->queryResult->parameters->sys_id;
		$speech = "Thanks ".$name."! Incident Created Successfully for issue " . $sh_desc . " and your incident number is " . $incident_no;
		$speech .= " Sys_id is ".$sys_id;
		$speech .= "\r\n";
		$speech .= " Thanks for contacting us. Are you satisfied with the response?";
		//echo $speech;
		

	}
	
	//--------------------
	if($json->queryResult->intent->displayName=='SENDMAIL')
	{
		if(isset($json->queryResult->parameters->to_email))
		{ $to_email = $json->queryResult->parameters->to_email; }
	 	/*$mail = new PHPMailer;
    require_once '/bc-auto2018/PHPMailer/src/class.PHPMailer.php';
		$hostname= "smtp.gmail.com";
		$sender = "intelligentmachine2018@gmail.com";
		$mail_password="Centurylink2018";
		$sender_name = "CTLI_BOT";
		$to = $to_email;
		$Subject = "Test mail";
		$Body = "Testing mail from bot";
		
	    //Enable SMTP debugging.	
	    $mail->SMTPDebug = 2;
	    //Set PHPMailer to use SMTP.
	   // $mail->isSMTP();
	    //Set SMTP host name                          
	    $mail->Host = $hostname;
	    //Set this to true if SMTP host requires authentication to send email
	  //  $mail->SMTPAuth = true;
	    //Provide username and password     
	    $mail->Username = $sender;
	    $mail->Password = $mail_password;
	    //If SMTP requires TLS encryption then set it
	    $mail->SMTPSecure = "ssl";
	    //Set TCP port to connect to 
	    $mail->Port = 465;
	    $mail->From = $sender;  
	    $mail->FromName = $sender_name;
	    $mail->addAddress($to);
	    $mail->isHTML(true);
	    $mail->Subject = $Subject;
	    $mail->Body = $Body;
	    $mail->AltBody = "This is the plain text version of the email content";*/
//-----------------------
		$to = $to_email;
		$subject = 'This a test';
		$message = '<h1>Hi there.</h1><p>Thanks for testing!</p>';
		$headers = "From : CTLI <intelligentmachine2018@gmail.com>\r\n";
		$headers .= "Reply-To: rachnarke@gmail\r\n";
		$headers .= "Content-type: text/html\r\n";
		
		$chk = mail($to, $subject, $message, $headers);
		
	 if(!$chk) 
	 {
	    $speech= "Mailer Error: ";
	 } else
	 {
	    $speech= "Message has been sent";
	 }

//----------------------		
}
	//--------------------
		$res = new \stdClass();
		$res->fulfillmentText = $speech;
		$res->source = "webhook";
		echo json_encode($res);
}
else
{
	echo "Method not allowed";
}

?>
