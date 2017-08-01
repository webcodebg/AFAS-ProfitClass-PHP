<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\ConnectorFilter;
use \iPublications\Profit\Connection;
use \iPublications\Profit\AppConnectorReport;
use \Exception;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('https://profitweb.afasonline.nl/ProfitServices/GetConnector.asmx');
$c->SetTimeout(20);

/**
 * $c->SetTargetURL('https://xxxxxx:yyyyyyyy@profitweb.afasonline.com/ProfitServices/GetConnector.asmx');
 * > When no auth in URL (or when NTLM, domain cannot be passed in URL)
 * 		$c->SetUsername('xxxxxxxx');
 * 		$c->SetPassword('*****');
 * 		$c->SetAuthDomain('AOL');
 **/

/**
 * Een nieuw AppConnectorReport object vereist een connectie-object als parameter.
 * Wanneer eenmaal één connector heeft gefaald is het connectie-object als
 * "failed" gemarkeerd, en kan het niet worden herbruikt: het connectie-
 * object bevat foutdetails. Om toch een volgende GET (of zelfs Update, etc.)
 * uit te kunnen voeren als een eerdere actie is mislukt, kan het 'clone'
 * argument worden opgegeven voor het meegeven van het connectie-object.
 * Het oorspronkelijke connectie-object blijft dan ongewijzigd, en is opnieuw
 * (eventueel gecloned) herbruikbaar voor volgende calls.
 **/
$g = new AppConnectorReport(clone $c);

/**
 * Onderstaande waarden (User, Pass, Environment) zijn niet nodig
 * wanneer op het connectie-object de SetSoapCallxxxxx setters
 * reeds zijn gebruikt, en hiervan niet hoeft te worden afgeweken.
 **/
$g->SetToken('xxxxxxx');

// Eventueel een uitgebreid filter-object, anders in EXECUTE enkel het target ID opgeven voor een U002 filter.
// $filter = new ConnectorFilter;
// $filter->add('U002', 1, '10003799');
// $g->SetFilter($filter);

// All debugging info
// 		print_r($g->GetRequiredElements());
//  		print_r($g->GetSoapRequestBody());
// 		print_r($g->GetSoapRequestHeaders());
// 		print_r($g->GetFilter());

try {
	$g->SetReportID('xxxxxxx');
	$g->Execute('10217');
	file_put_contents("/Users/wrw/Desktop/output.pdf", $g->GetResults());
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
