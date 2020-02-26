<?php

function validateName( $inName ) {
	//cannot be empty
	
	if( empty($inName) ) {
		return false;	//Failed validation
	}
	else {
		return true;	//Passes validation	
	}	
}//end validateName()

function validateDescription( $inDesc) {
	//cannot be empty
	
	if( empty($inDesc) ) {
		return false;	//Failed validation
	}
	else {
		return true;
	}		
}//end validateDescription
function validatePresenter( $inPres) {
	//cannot be empty
	
	if( empty($inPres) ) {
		return false;	//Failed validation
	}
	else {
		return true;
	}		
}//end validateDescription

function validateTime( $inTime) {
	//cannot be empty
	if( empty($inTime) ) {
		return false;	//Failed validation
	}
	else {
		$time = DateTime::createFromFormat('H:i', $inTime);
		if (!$time){
			return false;;
		}else{
			return true;	//Passes validation	
		}
	}
	
}//end validateProdColor()


function validateDate( $inDate ) {
	if( empty($inDate) ) {
		return false;	//Failed validation
	}
	else {
		return true;	//Passes validation	
	}
}//end validateProdSize()
?>