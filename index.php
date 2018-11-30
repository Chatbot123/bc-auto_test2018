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
		
		$jsonobj = array('short_description' => $sh_desc);
             	$jsonobj = json_encode($jsonobj);	

		
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
		
		
		//----------------------------------------------------------------------------
		//$json->queryResult->parameters->incident_num= $incident_no;
		//$json->queryResult->parameters->sys_id= $sys_id;
		
		
		//echo $json->queryResult->parameters->incident_num;
		//echo $json->queryResult->parameters->sys_id;
		/*curl_setopt($ch, CURLOPT_URL, "https://api.dialogflow.com/v1/query?v=20180910");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"lang\": \"en\", \"sessionId\":\"12345\",\"event\":{\"name\":\"STOREDATA\",\"data\":{\"incident_num\":\"INC001003\",\"sys_id\":\"c4aa495ddba123002e6ff36f2996197e\"}}}");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "Content-Type: application/json; charset=utf-8";
		$headers[] = "Authorization: Bearer a9c8361f6f01429bb978cc11bd571048";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_exec($ch);
		curl_close($ch);*/
		//---------------------------------------------------------------------------
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
	if($json->queryResult->intent->displayName=='Raise_ticket_intent - GetnameGetissue - yes - custom')
	{
		if(isset($json->queryResult->parameters->ticket_num))
		{ $ticket_num = $json->queryResult->parameters->ticket_num; }
		str_pad($ticket_num, 7, '0', STR_PAD_LEFT);
		
		//{"incident_state":"7","close_notes":"Resolved by Caller","close_code":"Closed/Resolved by Caller","caller_id":"System Administrator"}
		//$sh_desc = "Testing";
		//$name = "someone";
		$instance = "dev60887";
		$username = "admin";
		$password = "Avik.17.jan";
		$table = "incident";
		
		/*$jsonobj = array(
					'incident_state' => '7'
					'close_notes' => 'Resolved by Caller'
					'close_code' => 'Closed/Resolved by Caller'
					'caller_id' => 'System Administrator'
				);
             	$jsonobj = json_encode($jsonobj);*/	
		$jsonobj=1;
		
		$query = "https://$instance.service-now.com/$table.do?JSONv2&sysparm_action=update&sysparm_query=numberENDSWITH".$ticket_num;
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
			    curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"incident_state\":\"7\",\"close_notes\":\"Resolved by Caller\",\"close_code\":\"Closed/Resolved by Caller\",\"caller_id\":\"System Administrator\"}");
		}
		$response = curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$sh_desc =  $jsonoutput->records[0]->short_description;
		$inc_num =  $jsonoutput->records[0]->number;
		$speech = "Thanks! Incident ".$inc_num." closed Successfully for issue " . $sh_desc;
		$speech .= " Thanks for contacting us!";
		//echo $speech;
		
		
	}
	if($json->queryResult->intent->displayName=='SCT_UnlockSapAccount - no - yes')
	{
		
		if(isset($json->queryResult->parameters->line_manager))
		{ $line_manager = $json->queryResult->parameters->line_manager; }
		
		
			
		$instance = "dev60887";
		$username = "admin";
		$password = "Avik.17.jan";
		
		
		$query = "https://$instance.service-now.com/api/sn_sc/v1/servicecatalog/items/d292507adb3123002e6ff36f29961911/order_now";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"sysparm_quantity\": \"1\",\"variables\":{}}");
		}
		$response=curl_exec($curl);
		
		curl_close($curl);
		//echo $response;
		//$jsonoutput = json_decode($response);
		//echo $jsonoutput;
	//	$item_name =  $jsonoutput->result->items[0]->item_name;
		
		
		/*$query = "https://dev60887.service-now.com/api/sn_sc/v1/servicecatalog/cart/submit_order";
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
		echo $response;
		curl_close($curl);*/
		$jsonoutput = json_decode($response);
		//echo $jsonoutput;
		$request_num =  $jsonoutput->result->request_number;
		$speech = "Your Request number is ".$request_num." Please attach approval of your Line Manager to the ticket, so that your account will be unlocked";
 
		

	}
	if($json->queryResult->intent->displayName=='SCT_DeactivateAccount - no - yes')
	{
		
		if(isset($json->queryResult->parameters->line_manager))
		{ $line_manager_name = $json->queryResult->parameters->line_manager; }
		
		if(isset($json->queryResult->parameters->deactivation_date))
		{ $effective_date = $json->queryResult->parameters->deactivation_date; }
		$effective_date=substr($effective_date, 0, 10);
		
		
			
		$instance = "dev60887";
		$username = "admin";
		$password = "Avik.17.jan";
		
		
		$query = "https://$instance.service-now.com/api/sn_sc/v1/servicecatalog/items/a383cf67db3123002e6ff36f299619a9/order_now";
		$curl = curl_init($query);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$jsonvar = array('sysparm_quantity'=> '1',
				 'variables'=>	array('line_manager_name' => $line_manager_name,
				  			'effective_date'=> $effective_date
						     )
				);
             	$jsonvar = json_encode($jsonvar);
		$jsonobj=1;
		if($jsonobj)
		{
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonvar);
		}
		$response=curl_exec($curl);
		curl_close($curl);
		$jsonoutput = json_decode($response);
		$request_num =  $jsonoutput->result->request_number;
		$speech = "Your Request number is ".$request_num." Please attach approval of your Line Manager to the ticket, so that the account will be deactivated";
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
