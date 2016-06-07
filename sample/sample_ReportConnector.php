<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\ConnectorFilter;
use \iPublications\Profit\Connection;
use \iPublications\Profit\ReportConnector;
use \Exception;

////////////////////////
include_once (dirname(__FILE__) . '/../iPublications/Profit/ReportConnector.php');
include_once (dirname(__FILE__) . '/../iPublications/Profit/ConnectorFilter.php');
include_once (dirname(__FILE__) . '/../iPublications/Profit/Connection.php');
////////////////////////

$c = new Connection;
$c->SetTargetURL('http://deserver:8080/ProfitServices/GetConnector.asmx');
$c->SetTimeout(20);
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
 * Een nieuw ReportConnector object vereist een connectie-object als parameter.
 * Wanneer eenmaal één connector heeft gefaald is het connectie-object als
 * "failed" gemarkeerd, en kan het niet worden herbruikt: het connectie-
 * object bevat foutdetails. Om toch een volgende GET (of zelfs Update, etc.)
 * uit te kunnen voeren als een eerdere actie is mislukt, kan het 'clone'
 * argument worden opgegeven voor het meegeven van het connectie-object.
 * Het oorspronkelijke connectie-object blijft dan ongewijzigd, en is opnieuw
 * (eventueel gecloned) herbruikbaar voor volgende calls.
 **/ 
$g = new ReportConnector(clone $c);

/**
 * Onderstaande waarden (User, Pass, Environment) zijn niet nodig
 * wanneer op het connectie-object de SetSoapCallxxxxx setters 
 * reeds zijn gebruikt, en hiervan niet hoeft te worden afgeweken.
 **/
$g->SetEnvironmentId('IPUB');
$g->SetUserId('xxxxxxx');
$g->SetPassword('yyyyyyy');

// Eventueel een uitgebreid filter-object, anders in EXECUTE enkel het target ID opgeven voor een U002 filter.
// $filter = new ConnectorFilter;
// $filter->add('U002', 1, '10003799');
// $g->SetFilter($filter);

// All debugging info
// 		print_r($g->GetRequiredElements());
// 		print_r($g->GetSoapRequestBody());
// 		print_r($g->GetSoapRequestHeaders());
// 		print_r($g->GetFilter());

try {
	$g->SetReportID('881428BF413A9D571D2C1D8DDC69B1F5');
	$g->Execute('10003799');
	file_put_contents("/Users/Wietse/Desktop/output.pdf", $g->GetResults());
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
