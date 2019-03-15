<?php

namespace iPublications\Profit;

include_once (dirname(__FILE__) . '/../vendor/autoload.php');

$c = new Connection;
$c->SetTargetURL('https://deserver:8080/ProfitServices/GetConnector.asmx');
$c->SetTimeout(10);

$g = new AppConnectorGet(clone $c);

$g->SetEnvironmentId('IPUB');
$g->SetToken('xxxxxxx');
$g->SetConnectorId('XXXXXXX');
