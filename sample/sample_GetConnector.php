<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\ConnectorFilter;
use \iPublications\Profit\Connection;
use \iPublications\Profit\GetConnector;
use \Exception;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('https://deserver/ProfitServices/GetConnector.asmx');
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
$g = new GetConnector(clone $c);

/**
 * Onderstaande waarden (User, Pass, Environment) zijn niet nodig
 * wanneer op het connectie-object de SetSoapCallxxxxx setters
 * reeds zijn gebruikt, en hiervan niet hoeft te worden afgeweken.
 **/
$g->SetEnvironmentId('xxxx');
$g->SetUserId('yyyyyy');
$g->SetPassword('zzzzzz');

/**
 * Het apart setten van een GetConnector ID (naam van de GetConnector)
 * uit Profit is mogelijk, het alternatief is om deze gewoon mee te geven
 * aan de functie "Execute()" (zie verder op in dit script)
 **/
$g->SetConnectorId('ProfitCountries');

/**
 * You can set Required Fields before executing (case sensitive). The class
 * will check if these fields are present in the Response from Profit.
 * When missing, a ResponseRequirementException will be thrown.
 **/
$g->SetRequiredFields(array('CoId', 'Co', 'DbId'));

/**
 * Het opgeven van een filter kan (zie onderstaand) met setters.
 * Het opgeven van filters in array-formaat is ook mogelijk, de
 * eerste index (nummeriek) staat hierbij voor "indien gelijk: EN relatie"
 * en "indien hoger getal: OF relatie t.o.v. eerder lager en hoger getal".
 * De 2e index is vervolgens het veld, een pipe | en het filtertype:
 *
 * 		$filter[0]["Co|" . ConnectorFilter::LIKE] = 'N%';
 * 		$g->SetFilter($filter);
 **/

$filter = new ConnectorFilter;
$filter->add('Co', ConnectorFilter::LIKE, 'N%');
$filter->add('Co', ConnectorFilter::LIKE, '%L');
$filter->add_or();
$filter->add('Co', ConnectorFilter::LIKE, '%M%');
$filter->add_or();
$filter->add('Co', ConnectorFilter::LIKE, '%L%');
$filter->add('Co', ConnectorFilter::LIKE, '%R%');

$g->SetFilter($filter);

/**
 * Gaat het echt helemaal niet goed? Debugging informatie is beschikbaar met:
 * 		print_r($g->GetRequiredElements());
 * 		print_r($g->GetSoapRequestBody());
 * 		print_r($g->GetSoapRequestHeaders());
 * 		print_r($g->GetFilter());
 **/

try {
	/**
	 * Skip/Take maakt het mogelijk een deel van de dataset op te halen,
	 * om te voorkomen dat heel veel data in 1 request moet worden
	 * opgehaald. Skip is het aantal over te slaan regels, Take is het
	 * aantal op te halen regels. Wanneer in een Loop gebruikt en het
	 * aantal resultaatregels < take, heb je het einde van de dataset bereikt.
	 **/
	$g->SetSkip(0);
	$g->SetTake(10);
	$g->SetSorting('CoId', GetConnector::SORT_DESC);

	/**
	 * Voer de daadwerkelijke connector uit, hier gaat de verwerkingstijd
	 * in zitten. Hoe meer data, hoe langer de Execute.
	 **/
	$g->Execute();

	echo $g->GetConnectorId() . ' results: ' . PHP_EOL;
	echo count($g->GetResults());

	echo PHP_EOL;
	echo "FIELDS:";
	echo PHP_EOL;

	print_r($g->GetFields());
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
