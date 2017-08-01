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

class TokenConnector extends Connector {
	private $M_a_results;

	private $M_s_ApiKey;
	private $M_s_EnvironmentKey;
	private $M_s_userId;
	private $M_s_description;
	private $M_s_otp;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);
		$this->setConnectorNameIsSet('TokenConnector');
	}

	final public function SetApiKey($P_s_ApiKey){
		$this->M_s_ApiKey =  (string) $P_s_ApiKey;
	}

	final public function SetEnvironmentKey($P_s_EnvironmentKey){
		$this->M_s_EnvironmentKey =  (string) $P_s_EnvironmentKey;
	}

	final public function SetDescription($P_s_description){
		$this->M_s_description =  (string) $P_s_description;
	}

	final public function SetOTP($P_s_otp){
		$this->M_s_otp =  (string) $P_s_otp;
	}

	/* - - - - - - - - - - */

	final public function getenvironmentKey(){
		return $this->M_s_EnvironmentKey;
	}
	final public function getapiKey(){
		return $this->M_s_ApiKey;
	}
	final public function getdescription(){
		return $this->M_s_description;
	}
	final public function getotp(){
		return $this->M_s_otp;
	}

	/* - - - - - - - - - - */

	final public function GenerateOTP($P_s_username, $P_s_description = ''){
		$this->SetUserId($P_s_username);
		$this->SetDescription($P_s_description);
		$this->SetRequiredElement('environmentKey');
		$this->SetRequiredElement('apiKey');
		$this->SetRequiredElement('userId');
		$this->SetRequiredElement('description');
		$this->SetMethodName('GenerateOTP');
		$this->SetResponseObject('GenerateOTPResponse');
		parent::Execute();
		return $this->GetResults();
	}

	final public function DeleteToken($P_s_token){
		$this->SetToken($P_s_token);
		$this->SetRequiredElement('token');
		$this->SetMethodName('DeleteToken');
		$this->SetResponseObject('DeleteTokenResponse');
		parent::Execute();
		return null;
	}

	final public function GetTokenFromOTP($P_s_username, $P_s_OTP){
		$this->SetUserId($P_s_username);
		$this->SetOTP($P_s_OTP);
		$this->SetRequiredElement('environmentKey');
		$this->SetRequiredElement('apiKey');
		$this->SetRequiredElement('userId');
		$this->SetRequiredElement('otp');
		$this->SetMethodName('GetTokenFromOTP');
		$this->SetResponseObject('GetTokenFromOTPResult');

		parent::Execute();
		$L_s_entities = html_entity_decode($this->GetResults());
		if(preg_match("@>([A-F0-9]{64})<@", $L_s_entities, $M_a_match)){
			return $M_a_match[1];
		}
		return false;
	}

	final public function GetResults(){
		return $this->GetEncodedXML();
	}

	final public function Execute(){
		return false;
	}
}


