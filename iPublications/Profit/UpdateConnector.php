<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\Connection;
use \Exception;
use \SimpleXMLElement;

/**
 *
 * iPublications Connector / Soap class V3 for AFAS Profit. Abstract.
 * Replaces iPublications Soap Class + iPublications NTLM Soap Class V2
 *
 * @author Wietse Wind <w.wind@ipublications.net>
 * @copyright iPublications BV 2013
 * @license Closed Source, contact iPublications
 * @package iPublicationsProfitV3
 * @category Connectivity
 * @throws Exception
*/

class UpdateConnector extends Connector {
	private $M_a_fields;
	private $M_a_results;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);

		$this->SetRequiredElement(Connector::LOGONAS);
		$this->SetRequiredElement(Connector::CONNECTORTYPE);
		$this->SetRequiredElement(Connector::CONNECTORVERSION);
		$this->SetRequiredElement(Connector::DATAXML);

		$this->SetMethodName('Execute');
		$this->SetResponseObject('ExecuteResponse');
	}

	final public function SetConnector($P_s_connectorName){
		$L_s_connectorName = trim($P_s_connectorName);
		$this->setConnectorNameIsSet($L_s_connectorName);
		$this->SetConnectorType($P_s_connectorName);
	}

	final public function SetConnectorId($P_s_connectorName){
		$this->SetConnectorType($P_s_connectorName);
	}

	final public function SetXML($P_s_xml){
		$L_s_xml = trim($P_s_xml);
		$this->SetDataXml($this->XMLEncode($L_s_xml));
	}
}


