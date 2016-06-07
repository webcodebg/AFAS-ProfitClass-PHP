<?php

namespace iPublications\Profit;
use \Exception;

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

class Connection {
	private $M_s_TargetURL;

	private $M_s_AuthType;
	private $M_s_Username;
	private $M_s_Password;
	private $M_s_Domain;

	private $M_b_UseSSL;

	private $M_i_ConnectTimeout;
	private $M_i_Timeout;

	private $M_s_SoapCall_Token;
	private $M_s_SoapCall_Username;
	private $M_s_SoapCall_Password;
	private $M_s_SoapCall_Environment;

	const AUTH_NONE  = 'AUTH_NONE';
	const AUTH_NTLM  = 'AUTH_NTLM';
	const AUTH_BASIC = 'AUTH_BASIC';
	
	final public function __construct(){
		$this->SetDummyValues();
	}

	/* SETTERS */

	final private function SetDummyValues(){
		$this->SetTargetURL('http://127.0.0.1/');
		$this->SetAuthType(Connection::AUTH_NONE);
		$this->SetUseSSL(false);
		$this->SetUsername('');
		$this->SetPassword('');
		$this->SetAuthDomain('');
		
		$this->SetConnectTimeout(10);
		$this->SetTimeout(60);
	}

	final public function HasSoapCallUsername(){
		return (bool) isset($this->M_s_SoapCall_Username);
	}

	final public function HasSoapCallToken(){
		return (bool) isset($this->M_s_SoapCall_Token);
	}
	
	final public function HasSoapCallPassword(){
		return (bool) isset($this->M_s_SoapCall_Password);
	}
	
	final public function HasSoapCallEnvironment(){
		return (bool) isset($this->M_s_SoapCall_Environment);
	}

	final public function SetSoapCallUsername($L_s_in){
		$this->M_s_SoapCall_Username = $L_s_in;
	}

	final public function SetSoapCallToken($L_s_in){
		$this->M_s_SoapCall_Token = $L_s_in;
	}

	final public function SetSoapCallPassword($L_s_in){
		$this->M_s_SoapCall_Password = $L_s_in;	
	}

	final public function SetSoapCallEnvironment($L_s_in){
		$this->M_s_SoapCall_Environment = $L_s_in;
	}

	final public function GetSoapCallUsername(){
		return (string) $this->M_s_SoapCall_Username;
	}

	final public function GetSoapCallToken(){
		return (string) $this->M_s_SoapCall_Token;
	}

	final public function GetSoapCallPassword(){
		return (string) $this->M_s_SoapCall_Password;
	}

	final public function GetSoapCallEnvironment(){
		return (string) $this->M_s_SoapCall_Environment;
	}

	final public function SetTargetURL($P_s_TargetURL){
		$L_s_TargetURL = $this->SanitizeWebserviceURL($P_s_TargetURL);
		$this->M_s_TargetURL = (string) $L_s_TargetURL;
	}

	final public function SetAuthType($P_s_AuthType){
		$this->M_s_AuthType = (string) $P_s_AuthType;
	}

	final public function SetUsername($P_s_Username){
		$this->M_s_Username = (string) $P_s_Username;
	}

	final public function SetPassword($P_s_Password){
		$this->M_s_Password = (string) $P_s_Password;
	}

	final public function DropSoapCallUsername(){
		if(isset($this->M_s_SoapCall_Username)) unset($this->M_s_SoapCall_Username);
	}

	final public function DropSoapCallPassword(){
		if(isset($this->M_s_SoapCall_Password)) unset($this->M_s_SoapCall_Password);
	}

	final public function SetAuthDomain($P_s_Domain){
		$L_s_Domain = preg_replace("@[\@\\\]+$@", "", $P_s_Domain);
		$this->M_s_Domain = (string) $L_s_Domain;

		if($this->M_s_Domain !== '') $this->SetAuthType(Connection::AUTH_NTLM);
	}

	final public function SetUseSSL($P_b_UseSSL = true){
		$this->M_b_UseSSL = (bool) $P_b_UseSSL;
	}

	final public function SetTimeout($P_i_Seconds){
		$this->M_i_Timeout = (int) $P_i_Seconds;
	}

	final public function SetConnectTimeout($P_i_Seconds){
		$this->M_i_ConnectTimeout = (int) $P_i_Seconds;
	}

	/* VALIDATORS */

	final private function SanitizeWebserviceURL($P_s_URL){
		$L_s_URL = (string) $P_s_URL;
		
		$L_s_URL = trim($L_s_URL);
		if(!preg_match("@^http@i", $L_s_URL))   $L_s_URL = 'http://' . $L_s_URL;
		if(preg_match("@\?wsdl$@i", $L_s_URL)) $L_s_URL = preg_replace("@\?wsdl$@i", '', $L_s_URL);
		if(preg_match("@\?wsdl@i", $L_s_URL))  $L_s_URL = preg_replace("@[\?\&]wsdl\&(.+)@i", '?\\1', $L_s_URL);

		if(preg_match("@^(http.{0,1}:\/\/)(.+?):(.+)\@(.+)$@", $L_s_URL, $L_a_Match)){
			$this->SetUsername($L_a_Match[2]);
			$this->SetPassword($L_a_Match[3]);
			$L_s_URL = $L_a_Match[1].$L_a_Match[4];
			if($this->GetAuthType() == Connection::AUTH_NONE){
				$this->SetAuthType(Connection::AUTH_BASIC);
			}
		}

		if(preg_match("@^https@i", $L_s_URL)) $this->SetUseSSL(true);

		$L_s_URL = preg_replace("@(Get|Update|Subject|Report|Data)Connector(\.asmx)@i", "{CLASSNAME}\\2", $L_s_URL);

		if(!preg_match("@\{CLASSNAME\}@", $L_s_URL)){
			if(!preg_match("@\/$@", $L_s_URL)){
				$L_s_URL .= '/';
			}
			$L_s_URL .= '{CLASSNAME}.asmx';
		}

		return $L_s_URL;
	}

	/* GETTERS */

	final public function GetEndpoint(){
		return (string) $this->M_s_TargetURL;
	}

	final public function GetAuthType(){
		return (string) $this->M_s_AuthType;
	}

	final public function GetUsername(){
		return (string) $this->M_s_Username;
	}

	final public function GetPassword(){
		return (string) $this->M_s_Password;
	}

	final public function GetAuthDomain(){
		return (string) $this->M_s_Domain;
	}

	final public function GetUseSSL(){
		return (bool) $this->M_b_UseSSL;
	}

	final public function GetTimeout(){
		return (int) $this->M_i_Timeout;
	}

	final public function GetConnectTimeout(){
		return (int) $this->M_i_ConnectTimeout;
	}

	final public function GetHTTPStatusText($P_i_StatusCode = 501){
		$L_s_StatusText = '';

        switch ($P_i_StatusCode) {
            case 100: $L_s_StatusText = 'Continue'; break;
            case 101: $L_s_StatusText = 'Switching Protocols'; break;
            case 200: $L_s_StatusText = 'OK'; break;
            case 201: $L_s_StatusText = 'Created'; break;
            case 202: $L_s_StatusText = 'Accepted'; break;
            case 203: $L_s_StatusText = 'Non-Authoritative Information'; break;
            case 204: $L_s_StatusText = 'No Content'; break;
            case 205: $L_s_StatusText = 'Reset Content'; break;
            case 206: $L_s_StatusText = 'Partial Content'; break;
            case 300: $L_s_StatusText = 'Multiple Choices'; break;
            case 301: $L_s_StatusText = 'Moved Permanently'; break;
            case 302: $L_s_StatusText = 'Moved Temporarily'; break;
            case 303: $L_s_StatusText = 'See Other'; break;
            case 304: $L_s_StatusText = 'Not Modified'; break;
            case 305: $L_s_StatusText = 'Use Proxy'; break;
            case 400: $L_s_StatusText = 'Bad Request'; break;
            case 401: $L_s_StatusText = 'Unauthorized'; break;
            case 402: $L_s_StatusText = 'Payment Required'; break;
            case 403: $L_s_StatusText = 'Forbidden'; break;
            case 404: $L_s_StatusText = 'Not Found'; break;
            case 405: $L_s_StatusText = 'Method Not Allowed'; break;
            case 406: $L_s_StatusText = 'Not Acceptable'; break;
            case 407: $L_s_StatusText = 'Proxy Authentication Required'; break;
            case 408: $L_s_StatusText = 'Request Time-out'; break;
            case 409: $L_s_StatusText = 'Conflict'; break;
            case 410: $L_s_StatusText = 'Gone'; break;
            case 411: $L_s_StatusText = 'Length Required'; break;
            case 412: $L_s_StatusText = 'Precondition Failed'; break;
            case 413: $L_s_StatusText = 'Request Entity Too Large'; break;
            case 414: $L_s_StatusText = 'Request-URI Too Large'; break;
            case 415: $L_s_StatusText = 'Unsupported Media Type'; break;
            case 500: $L_s_StatusText = 'Internal Server Error'; break;
            case 501: $L_s_StatusText = 'Not Implemented'; break;
            case 502: $L_s_StatusText = 'Bad Gateway'; break;
            case 503: $L_s_StatusText = 'Service Unavailable'; break;
            case 504: $L_s_StatusText = 'Gateway Time-out'; break;
            case 505: $L_s_StatusText = 'HTTP Version not supported'; break;
            default:
                $L_s_StatusText = 'Unkown HTTP Status-code (by internal Class)';
            break;
        }
        
        return (string) $L_s_StatusText;
	}

}
