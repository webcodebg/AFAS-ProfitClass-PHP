<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\ConnectorFilter;
use \iPublications\Profit\Connection;
use \iPublications\Profit\GetConnector;
use \Exception;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('http://deserver:8080/ProfitServices');
$c->SetTimeout(20);
/**
 * Het connectie-object kan ook gebruikt worden om alvast de credentials
 * voor AFAS profit zelf op te geven, zodat dit niet meer hoeft op 
 * GetConnector niveau; deze waarden gelden vervolgens voor alle connectoren
 * waarvoor het connectie-object geldt.
 *    $c->SetSoapCallUsername('xxxx');
 *    $c->SetSoapCallPassword('yyyy');
 *    $c->SetSoapCallEnvironment('zzzz');
 **/

/**
 * $c->SetTargetURL('https://xxxxxx:yyyyyyyy@profitweb.afasonline.com/ProfitServices/GetConnector.asmx');
 * > When no auth in URL (or when NTLM, domain cannot be passed in URL)
 *    $c->SetUsername('xxxxxxxx');
 *    $c->SetPassword('*****');
 *    $c->SetAuthDomain('AOL');
 **/

/**
 * Een nieuw AppConnectorUpdate object vereist een connectie-object als parameter.
 * Wanneer eenmaal één connector heeft gefaald is het connectie-object als
 * "failed" gemarkeerd, en kan het niet worden herbruikt: het connectie-
 * object bevat foutdetails. Om toch een volgende GET (of zelfs Update, etc.)
 * uit te kunnen voeren als een eerdere actie is mislukt, kan het 'clone'
 * argument worden opgegeven voor het meegeven van het connectie-object.
 * Het oorspronkelijke connectie-object blijft dan ongewijzigd, en is opnieuw
 * (eventueel gecloned) herbruikbaar voor volgende calls.
 **/ 
$g = new AppConnectorUpdate(clone $c);

$g->SetEnvironmentId('QQQQQ');
$g->SetToken('C5D18FB272D44B6CA1EA95AAB4B1A1D940A14C8048386FC27A9FCB9AB8A39BC5');

$g->SetConnector('KnSubject');
$g->SetXML('
	<?xml version="1.0"?>
	<KnSubject>
        <Element SbId="">
          <Fields Action="insert">
            <StId>42</StId>
            <Ds>Testdossieritem</Ds>
            <SbTx></SbTx>
            <St>false</St>
            <SbBl>false</SbBl>
            <FileTrans>false</FileTrans>
          </Fields>
          <Objects>
            <KnSubjectLink>
              <Element SbId="">
              </Element>
            </KnSubjectLink>
          </Objects>
        </Element>
    </KnSubject>
');

// All debugging info
//	print_r($g->GetRequiredElements());
//	print_r($g->GetSoapRequestBody());
//	print_r($g->GetSoapRequestHeaders());

try {
	print_r($g->Execute());
	echo "OK :)";
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
