<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\Connection;
use \iPublications\Profit\CommSvcConnector;
use \Exception;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('https://xxxxxxxxxx/ProfitServices/');
$c->SetTimeout(10);

/**
 * Het connectie-object kan ook gebruikt worden om alvast de credentials
 * voor AFAS profit zelf op te geven, zodat dit niet meer hoeft op
 * GetConnector niveau; deze waarden gelden vervolgens voor alle connectoren
 * waarvoor het connectie-object geldt.
 * 		$c->SetSoapCallUsername('xxxx');
 * 		$c->SetSoapCallPassword('yyyy');
 * 		$c->SetSoapCallEnvironment('zzzz');
 **/

/**
 * $c->SetTargetURL('https://xxxxxx:yyyyyyyy@profitweb.afasonline.com/ProfitServices/GetConnector.asmx');
 * > When no auth in URL (or when NTLM, domain cannot be passed in URL)
 * 		$c->SetUsername('xxxxxxxx');
 * 		$c->SetPassword('*****');
 * 		$c->SetAuthDomain('AOL');
 **/

/**
 * Een nieuw GetConnector object vereist een connectie-object als parameter.
 * Wanneer eenmaal één connector heeft gefaald is het connectie-object als
 * "failed" gemarkeerd, en kan het niet worden herbruikt: het connectie-
 * object bevat foutdetails. Om toch een volgende GET (of zelfs Update, etc.)
 * uit te kunnen voeren als een eerdere actie is mislukt, kan het 'clone'
 * argument worden opgegeven voor het meegeven van het connectie-object.
 * Het oorspronkelijke connectie-object blijft dan ongewijzigd, en is opnieuw
 * (eventueel gecloned) herbruikbaar voor volgende calls.
 **/
$g = new CommSvcConnector(clone $c);

/**
 * Onderstaande waarden (User, Pass, Environment) zijn niet nodig
 * wanneer op het connectie-object de SetSoapCallxxxxx setters
 * reeds zijn gebruikt, en hiervan niet hoeft te worden afgeweken.
 **/
$g->SetEnvironmentId('xxxxxx');
$g->SetUserId('zzzzzz');
$g->SetPassword('yyyyy');

/**
 * Gaat het echt helemaal niet goed? Debugging informatie is beschikbaar met:
 * 		print_r($g->GetRequiredElements());
 * 		print_r($g->GetSoapRequestBody());
 * 		print_r($g->GetSoapRequestHeaders());
 * 		print_r($g->GetFilter());
 **/

try {

	$g->SetMessageType('KVK');
	$g->SetExternalMessageId('123465');
	$g->SetMessagePdf('');
	$g->SetMessageContent('');

	print_r($g->UploadMessage());

	print_r($g->GetResults());
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
