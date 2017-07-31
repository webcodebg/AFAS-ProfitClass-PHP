<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\Connection;
use \iPublications\Profit\AppConnectorSubject;
use \Exception;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('https://12345.afasonlineconnector.nl/ProfitServices/');
$c->SetTimeout(20);
$c->SetSoapCallToken('<token><version>1</version><data>39F88060405C7BAA9CA6D90992BD0666D76CD7A0446C916A2EBEF68D99DC05B5</data></token>');
/**
 * $c->SetTargetURL('https://xxxxxx:yyyyyyyy@profitweb.afasonline.com/ProfitServices/GetConnector.asmx');
 * > When no auth in URL (or when NTLM, domain cannot be passed in URL)
 * 		$c->SetUsername('xxxxxxxx');
 * 		$c->SetPassword('*****');
 * 		$c->SetAuthDomain('AOL');
 **/

// $c->SetUsername('ddd');
// $c->SetPassword('dddd');
// $c->SetAuthDomain('AOL');

/**
 * Een nieuw AppConnectorSubject object vereist een connectie-object als parameter.
 * Wanneer eenmaal één connector heeft gefaald is het connectie-object als
 * "failed" gemarkeerd, en kan het niet worden herbruikt: het connectie-
 * object bevat foutdetails. Om toch een volgende GET (of zelfs Update, etc.)
 * uit te kunnen voeren als een eerdere actie is mislukt, kan het 'clone'
 * argument worden opgegeven voor het meegeven van het connectie-object.
 * Het oorspronkelijke connectie-object blijft dan ongewijzigd, en is opnieuw
 * (eventueel gecloned) herbruikbaar voor volgende calls.
 **/
$g = new AppConnectorSubject(clone $c);

/**
 * Onderstaande waarden (User, Pass, Environment) zijn niet nodig
 * wanneer op het connectie-object de SetSoapCallxxxxx setters
 * reeds zijn gebruikt, en hiervan niet hoeft te worden afgeweken.
 **/
//$g->SetToken('54A6FA5C40A7B8709E1E9B871BEB562EEE6A4F9BD5B17BC5DF8B4C8091F12204');

// $g->SubjectID(5583); // ID van het dossieritem waar de bijlage bij hoort. Can be "Execute" parameter too.


// All debugging info
//	print_r($g->GetRequiredElements());
//	print_r($g->GetSoapRequestBody());
//	print_r($g->GetSoapRequestHeaders());

try {
	//print_r($g->Execute(5583)); // Id van dossieritem, hoeft niet, mag ook direct in de Results Getter

	$data = $g->GetResults(95178,'E86564E04A7711B3E899CE93133BF614');

	echo "OK :) Bestand ontvangen, length: " . strlen($data);
	echo $data;
	file_put_contents("/Users/wrw/Desktop/output.jpg", $data);
  // En als we het wilen opslaan:
  // file_put_contents('/Users/Wietse/Desktop/test.png', $g->Getresults());

}
catch (Exception $e){
	echo "Caught 'Exception \$e' " . PHP_EOL;
	echo "  > " . $e->GetCode() . ' - ' . $e->GetMessage();
	echo PHP_EOL;

	/**
	 * ErrorCode 5 = een door AFAS Profit gegeven foutmelding,
	 * basisinformatie is beschikbaar, detailinformatie is
	 * te vinden in het AFAS Profit omgevingslogboek.
	 **/
	if($e->GetCode() == 5){
		echo PHP_EOL;
		echo "ANTA-ERROR:";
		echo PHP_EOL;
		print_r($g->ANTAError());
		echo PHP_EOL;
	}
}

echo PHP_EOL;
