<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\Connection;
use \iPublications\Profit\TokenConnector;
use \Exception;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('https://theserver:443/ProfitServices');
$c->SetTimeout(20);

/**
 * $c->SetTargetURL('https://xxxxxx:yyyyyyyy@profitweb.afasonline.com/ProfitServices/GetConnector.asmx');
 * > When no auth in URL (or when NTLM, domain cannot be passed in URL)
 * 		$c->SetUsername('xxxxxxxx');
 * 		$c->SetPassword('*****');
 * 		$c->SetAuthDomain('AOL');
 **/

/**
 * Een nieuw TokenConnector object vereist een connectie-object als parameter.
 * Wanneer eenmaal één connector heeft gefaald is het connectie-object als
 * "failed" gemarkeerd, en kan het niet worden herbruikt: het connectie-
 * object bevat foutdetails. Om toch een volgende GET (of zelfs Update, etc.)
 * uit te kunnen voeren als een eerdere actie is mislukt, kan het 'clone'
 * argument worden opgegeven voor het meegeven van het connectie-object.
 * Het oorspronkelijke connectie-object blijft dan ongewijzigd, en is opnieuw
 * (eventueel gecloned) herbruikbaar voor volgende calls.
 **/ 
$g = new TokenConnector(clone $c);

/**
 * Onderstaande waarden (User, Api, Environment) zijn niet nodig
 * wanneer op het connectie-object de SetSoapCallxxxxx setters 
 * reeds zijn gebruikt, en hiervan niet hoeft te worden afgeweken.
 **/
$g->SetApiKey('BBB2524964B444FEA8F208F916D1CCCC');
$g->SetEnvironmentKey('AAA1FB3C4AC663EEE996ABA09A56DDDD');

// All debugging info
//	print_r($g->GetRequiredElements());
//	print_r($g->GetSoapRequestBody());
//	print_r($g->GetSoapRequestHeaders());

try {
	// GenerateOTP: username, description
	// Step 1. Profit sends e-mail containing activation token.
	// Method will not return true / false, Profit does not give callbacks
	// Returns void
	
	// #1
	// $g->GenerateOTP('john.doe', 'Sample User :)');

	// GetTokenFromOTP: username, OTP
	// Step 2. OTP is sent by Mail by Profit to user 
	// to the e-mail addr set at user-level.
	// Returns token.
	
	// #2
	// $token = $g->GetTokenFromOTP('john.doe', '12345678');

	// Delete a token? Use: 
	// #3
	// $g->DeleteToken('CC63C11E944A4FCBB82AB58C42F6AB279BA635B64FF3C26B96DE2798C8A027DD');
}
catch (Exception $e){
	echo "Caught 'Exception \$e' " . PHP_EOL;
	echo "  > " . $e->GetCode() . ' - ' . $e->GetMessage();
	echo PHP_EOL;
}

echo PHP_EOL;
