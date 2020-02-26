<!-- http://localhost/wdv341/unit7/processevents/processEvents.php -->
<?php


require 'dbConnect.php';

$keys = parse_ini_file('config.ini');
$reCaptchaSiteKey =  $keys["Sitekey"] ;
$reCaptchaSecretkey =   $keys["Secretkey"];

$eventName=$eventDescription=$eventPresenter=$eventDate=$eventTime="";
$time_errMsg=$date_errMsg=$name_errMsg=$description_errMsg=$presenter_errMsg=$submit_errMsg="";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {
	// Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = $reCaptchaSecretkey;
    $recaptcha_response = $_POST['recaptcha_response'];
    // Make and decode POST request:
    $recaptchaJSON = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptchaJSON);
	
	try {

		$eventName=$_POST["event_name"];
		$eventDescription=$_POST["event_description"];
		$eventPresenter=$_POST["event_presenter"];
		$eventDate=$_POST["event_date"];
		$eventTime=$_POST["event_time"];
		$valid_form = true;
		
		include 'validationsAdvanced.php';
		
		if( !validateTime($eventTime) ) {
			$valid_form = false;
			$time_errMsg = "Please enter a valid time<br /> In older browsers, 24 hour HH:MM including leading zeroes";
		}		
		if( !validateDate($eventDate) ) {
			$valid_form = false;
			$date_errMsg = "Please enter a valid date<br />In older browsers, YYYY-MM-DD including leading zeroes";
		}
		if( !validateName($eventName) ) {
			$valid_form = false;
			$name_errMsg = "Please enter a valid name";
		}
		if( !validateDescription($eventDescription) ) {
			$valid_form = false;
			$description_errMsg = "Please enter a valid description";
		}
		if( !validatePresenter($eventPresenter) ) {
			$valid_form = false;
			$presenter_errMsg = "Please enter a valid presenter";
		}
		//This logic is not actually validating the recaptcha response.
		//The success field only indicates that the recaptcha response was
		//completed successfully.  The score field is used to determine if 
		//Google thinks that the form was submitted by a robot.
		if ( $recaptcha->success){

		}else{
			$valid_form = false;
			$submit_errMsg = "Please resubmit.<br />";
			foreach($recaptcha as $x => $x_value) {
				if ( $x == "error-codes"){
					foreach($x_value as $value){
						$submit_errMsg .= $value . "<br />" . "\n" ;
					}
				}
			}
		}
		
		//PDO Prepared statements 

		//1. create the SQL statement with name placeholders
		

		if ($valid_form){
			$sql = "INSERT INTO wdv341_event (
					event_name, 
					event_description,
					event_presenter,
					event_date,
					event_time
					)
				VALUES (
					:eventName, 
					:eventDescription, 
					:eventPresenter,
					:eventDate,
					:eventTime
					)";
					

			//2. Create the prepared statement object
			$stmt = $conn->prepare($sql);	//creates the 'prepared statement' object

			//Bind parameters to the prepared statement object, one for each parameter
			$stmt->bindParam(':eventName', $eventName);
			$stmt->bindParam(':eventDescription', $eventDescription);
			$stmt->bindParam(':eventPresenter', $eventPresenter);
			$stmt->bindParam(':eventDate', $eventDate);
			$stmt->bindParam(':eventTime', $eventTime);

			//Execute the prepared statement
			if($stmt->execute()){echo "";}
		}
	}
	catch(PDOException $e){
		echo "PDO Exception!";
	}
}

?>



<!DOCTYPE html>

<html lang="en">

	<head>
		<!-- <link href="style.css" rel="stylesheet" type="text/css" /> -->
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>WDV341 Unit 7</title>
		<style>
			body{background-color:linen}
			.errmessage{color:red}
		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo  $reCaptchaSiteKey; ?>"></script>
		<script>
			grecaptcha.ready(function () {
				grecaptcha.execute(<?php echo "'" . $reCaptchaSiteKey . "'" ; ?>, { action: 'contact' }).then(function (token) {
					var recaptchaResponse = document.getElementById('recaptchaResponse');
					recaptchaResponse.value = token;
				});
			});
		</script>

	</head>

	<body>

		<form id="eventinput_form" name="eventinput" method="post">
			<p>
				<label for="event_name">Event Name</lable>
				<input id="event_name" name="event_name" value="<?php echo $eventName?>">
				<p class='errmessage'><?php echo $name_errMsg ?></p
			</p>
			<p>
				<label for="event_description">Event Description</lable>
				<input id="event_description" name="event_description" value="<?php echo $eventDescription?>">
				<p class='errmessage'><?php echo $description_errMsg ?></p
			</p>
			<p>
				<label for="event_presenter">Event Presenter</lable>
				<input id="event_presenter" name="event_presenter" value="<?php echo $eventPresenter?>">
				<p class='errmessage'><?php echo $presenter_errMsg ?></p>				
			</p>
			<p>
				<label for="event_date">Event Date</lable>
				<input type="date" id="event_date" name="event_date" value="<?php echo $eventDate?>">
				<p></p>
				<p class='errmessage'><?php echo $date_errMsg ;?></p>
			</p>
			<p>
				<label for="event_time">Event Time</lable>
				<input type="time" id="event_time" name="event_time" value="<?php echo $eventTime?>">
				<p class='errmessage'><?php echo $time_errMsg ;?></p>
			</p>

			<input type="hidden" name="recaptcha_response" id="recaptchaResponse">

			<input type="submit" name="submit" id="submit" value="Submit">
			<p class='errmessage'><?php echo $submit_errMsg ?></p>
		</form>
		<?php
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response']) && $valid_form) {
			echo "<h3>Results of submisstion</h3>" . "\n" ;
			echo "<h4>Submitted Data</h4> ". "\n" ;
			foreach($_POST as $x => $x_value) {
				if($x != "recaptcha_response"){
					echo $x . " = " . $x_value  ;
					echo "<br>" . "\n";
				}
			}
			echo "<h4>Recaptcha</h4> ". "\n" ;
			if ( $recaptcha->success){
				foreach($recaptcha as $x => $x_value) {
					echo $x . " = " . $x_value  ;
					echo "<br>" . "\n";
				}
			}else{
				foreach($recaptcha as $x => $x_value) {
					if ( $x == "error-codes"){
						foreach($x_value as $value){
							echo $value . "<br>" . "\n" ;
						}
					}
				}
			}
		}
		?>
	</body>
	
	


</html>


