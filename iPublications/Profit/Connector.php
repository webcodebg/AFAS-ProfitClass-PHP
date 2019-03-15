<?php

namespace iPublications\Profit;
use \Exception;
use \stdClass;

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
 * 		1 = Required Element undefined
 * 		2 = Client error
 * 	    3 = Hard Socket/Call error
 * 		4 = Soft-HTTP error
 * 		5 = (Profit) ANTA-Error
 * 	    6 = Unexpected results
 */

abstract class Connector {
    /**
     * @var Client
     */
    private $M_o_Client;
	private $M_o_Connect;
	private $M_s_MethodName;
	private $M_s_ResponseObject;
	private $M_s_SoapRequest;
	private $M_a_SoapRequestHeaders;

	private $M_a_RequiredElements = array();
	private $M_b_noCredentials    = false;

	private $M_s_Element_environmentId;
	private $M_s_Element_userId;
	private $M_s_Element_password;
	private $M_s_Element_logonAs;
	private $M_s_Element_connectorId;
	private $M_s_Element_filtersXml;
	private $M_s_Element_connectorType;
	private $M_i_Element_connectorVersion;
	private $M_s_Element_dataXml;
	private $M_s_Element_dataID;
	private $M_s_Element_parametersXml;
	private $M_s_Element_subjectID;
	private $M_s_Element_fileID;
	private $M_s_Element_reportID;
	private $M_s_Element_token;
	private $M_s_Element_messageType;
	private $M_s_Element_externalMessageId;
	private $M_s_Element_messageContent;
	private $M_s_Element_messagePdf;
	private $M_s_Element_messageStatusFilter;
	private $M_s_Element_messageId;
	private $M_s_Element_skip          = -1;
	private $M_s_Element_take          = -1;
	private $M_s_Element_options       = '';
	private $M_s_Element_sortfield     = false;
	private $M_i_Element_sortdirection = 1;
	private $M_b_ProfitOldSorting      = false;

	private $M_s_Client_Response;
	private $M_i_Client_HardError;
    private $M_s_Client_ErrorDesc;
    private $M_s_Client_HttpCode;
	private $M_o_ANTA_Error;

	protected $M_s_OUTPUTXML;
	private $M_b_connectorNameSet;

	const ENVIRONMENTID       = 'environmentId';
	const USERID              = 'userId';
	const PASSWORD            = 'password';
	const LOGONAS             = 'logonAs';
	const CONNECTORID         = 'connectorId';
	const FILTERSXML          = 'filtersXml';
	const CONNECTORTYPE       = 'connectorType';
	const CONNECTORVERSION    = 'connectorVersion';
	const DATAXML             = 'dataXml';
	const DATAID              = 'dataID';
	const PARAMETERSXML       = 'parametersXml';
	const SUBJECTID           = 'subjectID';
	const FILEID              = 'fileId';
	const REPORTID            = 'reportID';
	const TOKEN               = 'token';
	const USERTOKEN           = 'userToken';
	const SKIP                = 'skip';
	const TAKE                = 'take';
	const OPTIONS             = 'options';
	const MESSAGETYPE         = 'messageType';
	const MESSAGECONTENT      = 'messageContent';
	const EXTERNALMESSAGEID   = 'externalMessageId';
	const MESSAGEPDF          = 'messagePdf';
	const MESSAGEID           = 'messageId';
	const MESSAGESTATUSFILTER = 'messageStatusFilter';

	const NON_ANTA_ERROR   = '{NON-ANTA}';

	public function __construct(Connection $P_o_ConnectionSettings, Client $P_o_Client = null){
		// This function NEEDS to be extended, calling (this) parent

        $this->SetClient($P_o_Client ?? new CurlClient());
		$this->SetConnectObject($P_o_ConnectionSettings);

		$this->SetMethodName('Execute');
		$this->SetResponseObject('ExecuteResult');

		if(!$this->M_b_noCredentials){
			$this->SetRequiredElement(Connector::ENVIRONMENTID);
			$this->SetRequiredElement(Connector::USERID);
			$this->SetRequiredElement(Connector::PASSWORD);
		}

		$this->SetElementDummyValues();

		// If connection object contains Soap User/Pass/Env values: enherit them!
		if($P_o_ConnectionSettings->HasSoapCallUsername())    $this->SetUserId($P_o_ConnectionSettings->GetSoapCallUsername());
		if($P_o_ConnectionSettings->HasSoapCallPassword())    $this->SetPassword($P_o_ConnectionSettings->GetSoapCallPassword());
		if($P_o_ConnectionSettings->HasSoapCallEnvironment()) $this->SetEnvironmentId($P_o_ConnectionSettings->GetSoapCallEnvironment());
		if($P_o_ConnectionSettings->HasSoapCallToken())       $this->SetToken($P_o_ConnectionSettings->GetSoapCallToken());
	}

    /**
     * @param Client $P_o_Client
     */
	public function SetClient(Client $P_o_Client)
    {
        if($this->M_o_Client !== null){
            unset($this->M_o_Client);
        }

        $this->M_o_Client = $P_o_Client;
    }

	final public function setConnectorNameIsSet($L_s_connectorName = ''){
		if(!empty($L_s_connectorName)) $this->M_b_connectorNameSet = true;
	}

	final public function checkConnectorNameSet(){
		if(isset($this->M_b_connectorNameSet) && $this->M_b_connectorNameSet){
			return true;
		}else{
			throw new \Exception('Please set the Get/Update Connector Name (ID) first, using "SetConnector()" or "SetConnectorId()"', 1);
		}
	}

	final public function __destruct(){
		if(!$this->M_o_Client->CheckClient()){
			throw new Exception ("Client not ready", 2);
		}
	}

	/* CALLER */

	public function Execute(){
		// Sample Connection-Getter: [enter-here] echo PHP_EOL . $this->Connection()->GetEndpoint();
		// Sample Connection-Getter: [enter-here] echo PHP_EOL . (int) $this->Connection()->GetUseSSL();
		// Sample Connection-Getter: [enter-here] echo PHP_EOL . $this->Connection()->GetAuthType();
		// Sample Connection-Getter: [enter-here] echo PHP_EOL . $this->Connection()->GetUsername();
		// Sample Connection-Getter: [enter-here] echo PHP_EOL . $this->Connection()->GetPassword();
		// Sample Connection-Getter: [enter-here] echo PHP_EOL . $this->Connection()->GetAuthDomain();

		$this->checkConnectorNameSet();

		$this->M_o_Client->Init();

		$this->M_o_Client->SetUrl($this->GetPreparedEndpoint());
        $this->M_o_Client->SetConnectTimeout($this->Connection()->GetConnectTimeout()*1000);
        $this->M_o_Client->SetTimeout($this->Connection()->GetTimeout()*1000);

        $this->M_o_Client->SetUseSSL($this->Connection()->GetUseSSL());
        $this->M_o_Client->SetSslAllowInsecure(true);

        $this->M_o_Client->SetPostData($this->GetSoapRequestBody());
        $this->M_o_Client->SetHeaders(array_merge($this->GetSoapRequestHeaders(),array('Content-length: '.strlen($this->GetSoapRequestBody()))));

	    if($this->Connection()->GetAuthType() !== Connection::AUTH_NONE){
	        if($this->Connection()->GetAuthType() === Connection::AUTH_BASIC){
	            $this->M_o_Client->SetHttpAuth($this->Connection()->GetUsername(), $this->Connection()->GetPassword());
            } elseif ($this->Connection()->GetAuthType() === Connection::AUTH_NTLM){
                $this->M_o_Client->SetNtlmAuth($this->Connection()->GetAuthDomain() . "\\" );
            }
	    }

	    try {
            $this->M_o_Client->Execute();
            $this->SetClientResponse($this->M_o_Client->GetResponseBody());
            $this->SetClientHttpCode($this->M_o_Client->GetResponseHttpCode());
        } catch (ClientException $e) {
	        $this->SetClientError($e->getMessage());
        }

	    $this->CheckClientResult();

	    return @$this->GetResponseData();
	}

	/* GETTERS */

    private function GetClientResponse(){
        return $this->M_o_Client->GetResponseBody();
    }

	private function GetConnectorName(){
		return substr(strrchr(get_called_class(), "\\"), 1);
	}

	public function NoCredentials(){
		$this->M_b_noCredentials = true;
	}

	public function ClearCommonCredentials(){
		if(isset($this->M_s_Element_userId))    unset($this->M_s_Element_userId);
		if(isset($this->M_s_Element_logonAs))   unset($this->M_s_Element_logonAs);
		if(isset($this->M_s_Element_password))  unset($this->M_s_Element_password);
	}

	private function GetPreparedEndpoint($P_s_EndpointURL = null){
		if($P_s_EndpointURL === null){
			$L_s_EndpointURL = $this->Connection()->GetEndpoint();
		}else{
			$L_s_EndpointURL = $P_s_EndpointURL;
		}
		return str_replace("{CLASSNAME}", $this->GetConnectorName(), $L_s_EndpointURL);
	}

	/* ALIAS METHODS */

	public function call(){
		return $this->Execute();
	}

	/* SETTERS */

	final private function SetClientResponse($P_s_HTTPResponse){
		$this->M_s_Client_Response = $P_s_HTTPResponse;
	}

	final private function SetClientHttpCode($P_s_HTTPCode){
	    $this->M_s_Client_HttpCode = $P_s_HTTPCode;
    }

	final private function SetClientError($P_s_HTTPResponse){
		$this->M_s_Client_ErrorDesc = $P_s_HTTPResponse;
		$this->M_i_Client_HardError = 1;
	}

	final private function SetConnectObject(Connection $P_o_Connect){
		$this->M_o_Connect = $P_o_Connect;
	}

	final public function SetMethodName($P_s_MethodName){
		$this->M_s_MethodName = trim($P_s_MethodName);
	}

	final public function SetResponseObject($P_s_ResponseObject){
		$this->M_s_ResponseObject = trim($P_s_ResponseObject);
	}

	final public function SetOutputXML($P_s_OutputXML){
		$L_s_OutputXML = trim($P_s_OutputXML);
		$this->M_s_OUTPUTXML = $L_s_OutputXML;
	}

	final private function SetElementDummyValues(){
		$this->SetEnvironmentId('');
		$this->SetUserId('');
		$this->SetPassword('');
		$this->SetLogonAs('');
		$this->SetConnectorId('');
		$this->SetFiltersXml('');
		$this->SetConnectorType('');
		$this->SetConnectorVersion(1);
		$this->SetDataXml('');
		$this->SetDataID('');
		$this->SetParametersXml('');
		$this->SetSubjectID('');
		$this->SetFileID('');
		$this->SetReportID('');
		$this->SetUserToken('');
		$this->SetToken('');
		$this->SetSkip(-1);
		$this->SetTake(-1);
		$this->SetSortField(false);
		$this->SetSortDirection(1);
		$this->SetOptions('');
		$this->SetMessageType('');
		$this->SetExternalMessageId('');
		$this->SetMessageContent('');
		$this->SetMessagePdf('');
		$this->SetMessageStatusFilter('');
		$this->SetMessageId('');
	}

	public function SetRequiredElement($P_s_RequiredElement){
		$L_s_SetterFunction = 'Set' . ucwords(trim($P_s_RequiredElement));
		if(method_exists($this, $L_s_SetterFunction)){
			$this->M_a_RequiredElements[md5(strtolower($P_s_RequiredElement))] = $P_s_RequiredElement;
		}else{
			throw new Exception ("There is no Required Element [" . $P_s_RequiredElement . "]", 1);
		}
	}

	public function SetParsedANTAError(){
		$this->M_o_ANTA_Error = new stdClass;

		$this->M_o_ANTA_Error->code        = Connector::NON_ANTA_ERROR;
		$this->M_o_ANTA_Error->message     = '[Class Connector] Unparsable ANTA error';
		$this->M_o_ANTA_Error->description = '[Class Connector] Unparsable ANTA error';
		$this->M_o_ANTA_Error->body        = '[Class Connector] Unparsable ANTA error';
		$this->M_o_ANTA_Error->reference   = '-';

		$L_s_ExceptionBody = $this->GetClientResponse();

		if(preg_match("@ProfitApplicationException|faultstring>([^<]+)<\/faultstring@i", $L_s_ExceptionBody)){
			if(preg_match("@ErrorNumber>([^<]+)<\/ErrorNumber@i", $L_s_ExceptionBody, $L_a_Match)){
				if(isset($L_a_Match[1])){
					$this->M_o_ANTA_Error->code = $L_a_Match[1];
				}
			}
			if(preg_match("@faultstring>([^<]+)<\/faultstring@i", $L_s_ExceptionBody, $L_a_Match)){
				if(isset($L_a_Match[1])){
					$this->M_o_ANTA_Error->message = $L_a_Match[1];
					if($this->M_o_ANTA_Error->code == Connector::NON_ANTA_ERROR){
						$this->M_o_ANTA_Error->code = 'UNSPECIFIED';
					}
				}
			}
			if(preg_match("@Description\.+:(.+)@i", $L_s_ExceptionBody, $L_a_Match)){
				if(isset($L_a_Match[1])){
					$this->M_o_ANTA_Error->description = trim($L_a_Match[1]);
				}
			}
			if(preg_match("@Log reference\.+:([ A-Z0-9]+)@i", $L_s_ExceptionBody, $L_a_Match)){
				if(isset($L_a_Match[1])){
					$this->M_o_ANTA_Error->reference = trim($L_a_Match[1]);
				}
			}
			if(preg_match("@.+<Detail>(.+?)<\/Detail@mis", $L_s_ExceptionBody, $L_a_Match)){
				if(isset($L_a_Match[1])){
					$this->M_o_ANTA_Error->body = $L_a_Match[1];
				}
			}
		}
	}

	/* GETTERS */

	final public function GetResponseObject(){
		if(isset($this->M_s_ResponseObject)){
			return (string) $this->M_s_ResponseObject;
		}else{
			return false;
		}
	}

	public function GetRequiredElements(){
		return (object) $this->M_a_RequiredElements;
	}

	final private function GetMethodName(){
		return (string) $this->M_s_MethodName;
	}

	final public function GetEncodedXML(){
		return (string) (isset($this->M_s_OUTPUTXML) ? $this->M_s_OUTPUTXML : '');
	}

	final public function GetResponseData(){
		$L_s_EncodedXml = @html_entity_decode(@$this->GetEncodedXML());
		if(substr_count($L_s_EncodedXml, '<results><') > 0){
			try {
				$L_o_outputObj = new \SimpleXMLElement($L_s_EncodedXml);
				if(!empty($L_o_outputObj) && is_object($L_o_outputObj)){
					$L_a_outputObj   = (array) $L_o_outputObj;
					$L_s_connectorId = @$this->GetConnectorType();
					if(!empty($L_s_connectorId)){
						$L_s_connectorId = strtolower($L_s_connectorId);
						$L_a_keys        = array_keys($L_a_outputObj);
						if(isset($L_a_keys[0]) && strtolower($L_a_keys[0]) == strtolower($L_s_connectorId) && !empty($L_a_outputObj[$L_a_keys[0]])){
							$L_a_outputObj = $L_a_outputObj[$L_a_keys[0]];
							if(is_object($L_a_outputObj)){
								$L_a_outputObj = (array) $L_a_outputObj;
							}
						}
					}
					return $L_a_outputObj;
				}
			}
			catch (\Exception $e){
				// Do nothing.
			}
		}
		return (bool) empty($this->M_o_ANTA_Error);
	}

	final public function GetSoapRequestBody(){
		$this->M_s_SoapRequest  = "<?xml version=\"1.0\"?>" . PHP_EOL;
		$this->M_s_SoapRequest .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">" . PHP_EOL;
		$this->M_s_SoapRequest .= "  <soap:Body>" . PHP_EOL;
		$this->M_s_SoapRequest .= "    <".$this->GetMethodName()." xmlns=\"urn:Afas.Profit.Services\">" . PHP_EOL;
		foreach($this->GetRequiredElements() as $L_s_ElementName){
			$L_s_ElementGetter = 'Get' . ucwords($L_s_ElementName);
			$L_s_ElementValue  = $this->$L_s_ElementGetter();
			$this->M_s_SoapRequest .= "      <".$L_s_ElementName.">".$L_s_ElementValue."</".$L_s_ElementName.">" . PHP_EOL;
		}
		$this->M_s_SoapRequest .= "    </".$this->GetMethodName().">" . PHP_EOL;
		$this->M_s_SoapRequest .= "  </soap:Body>" . PHP_EOL;
		$this->M_s_SoapRequest .= "</soap:Envelope>";
		return $this->M_s_SoapRequest;
	}

	final public function GetSoapRequestHeaders(){
	    $this->M_a_SoapRequestHeaders = array(
	      "Content-type: text/xml;charset=\"utf-8\"",
	      "Accept: text/xml",
	      "Cache-Control: no-cache",
	      "Pragma: no-cache",
	      "SOAPAction: \"urn:Afas.Profit.Services/".$this->GetMethodName()."\"",
	    );
	    // Todo: Replace ContentLength parameter before sending to Client

	    return (array) $this->M_a_SoapRequestHeaders;
	}

	/* OBJECT RETURNERS */

	final private function Connection(){
		return $this->M_o_Connect;
	}

	final public function ANTAError(){
		return (object) $this->M_o_ANTA_Error;
	}

	/* GENERAL PROFIT SOAPCLASS SETTERS */

	final public function SetEnvironmentId($P_s_Value){
		$this->M_s_Element_environmentId = (string) $P_s_Value;
		return $this;
	}

	final public function SetUserId($P_s_Value){
		$this->M_s_Element_userId = (string) $this->XMLEncode($P_s_Value);
		return $this;
	}

	final public function SetPassword($P_s_Value){
		$this->M_s_Element_password = (string) $this->XMLEncode($P_s_Value);
		return $this;
	}

	final public function SetLogonAs($P_s_Value){
		$this->M_s_Element_logonAs = (string) $P_s_Value;
		return $this;
	}

	public function SetConnectorId($P_s_Value){
		$this->setConnectorNameIsSet($P_s_Value);

		$this->M_s_Element_connectorId = (string) $P_s_Value;
		return $this;
	}

	final public function SetFiltersXml($P_s_Value){
		$this->M_s_Element_filtersXml = htmlentities($P_s_Value);
		return $this;
	}

	public function SetConnectorType($P_s_Value){
		$this->setConnectorNameIsSet($P_s_Value);
		$this->M_s_Element_connectorType = (string) $P_s_Value;
		return $this;
	}

	final public function SetConnectorVersion($P_i_Value){
		$this->M_i_Element_connectorVersion = (int) $P_i_Value;
		return $this;
	}

	final public function SetDataXml($P_s_Value){
		$this->M_s_Element_dataXml = (string) $P_s_Value;
		return $this;
	}

	final public function SetDataID($P_s_Value){
		$this->M_s_Element_dataID = (string) $P_s_Value;
		return $this;
	}

	public function SetParametersXml($P_s_Value){
		$this->M_s_Element_parametersXml = (string) $P_s_Value;
		return $this;
	}

	final public function SetSubjectID($P_s_Value){
		$this->M_s_Element_subjectID = (string) $P_s_Value;
		return $this;
	}

	final public function SetFileID($P_s_Value){
		$this->M_s_Element_fileID = (string) $P_s_Value;
		return $this;
	}

	final public function SetReportID($P_s_Value){
		$this->M_s_Element_reportID = (string) $P_s_Value;
		return $this;
	}

	public function SetUserToken($P_s_Value){
		$this->SetToken($P_s_Value);
		return $this;
	}

	public function SetToken($P_s_Value){
		// $this->NoCredentials();
		// $this->SetRequiredElement(Connector::TOKEN);

		if(preg_match("@token@i", $P_s_Value)){
			if(preg_match("@<token>@i", $P_s_Value)){
				$this->M_s_Element_token = $this->XMLEncode($P_s_Value);
			}else{
				$this->M_s_Element_token = (string) $P_s_Value;
			}
		}else{
			$this->M_s_Element_token = $this->XMLEncode("<token><version>1</version><data>" . trim($P_s_Value) . "</data></token>");
		}
		return $this;
	}

	final public function SetSkip($P_s_Value){
		$this->M_s_Element_skip = (int) $P_s_Value;
		return $this;
	}

	final public function SetTake($P_s_Value){
		$this->M_s_Element_take = (int) $P_s_Value;
		return $this;
	}

	final public function SetSortField($P_s_Value){
		$this->M_s_Element_sortfield = (string) $P_s_Value;
		return $this;
	}

	final public function SetSortDirection($P_i_Value){
		$this->M_i_Element_sortdirection = (int) $P_i_Value;
		return $this;
	}

	final public function SetOptions($P_s_Value){
		$this->M_s_Element_options = (string) $P_s_Value;
		return $this;
	}

	final public function SetMessageType($P_s_Value){
		$this->M_s_Element_messageType = (string) $P_s_Value;
		return $this;
	}

	final public function SetExternalMessageId($P_s_Value){
		$this->M_s_Element_externalMessageId = (string) $P_s_Value;
		return $this;
	}

	final public function SetMessageContent($P_s_Value){
		$this->M_s_Element_messageContent = (string) $P_s_Value;
		return $this;
	}

	final public function SetMessagePdf($P_s_Value){
		$this->M_s_Element_messagePdf = (string) $P_s_Value;
		return $this;
	}

	final public function SetMessageStatusFilter($P_s_Value){
		$this->M_s_Element_messageStatusFilter = (string) $P_s_Value;
		return $this;
	}

	final public function SetMessageId($P_s_Value){
		$this->M_s_Element_messageId = (string) $P_s_Value;
		return $this;
	}

	/* GENERAL PROFIT SOAPCLASS GETTERS */

	final private function GetEnvironmentId(){
		return (string) $this->M_s_Element_environmentId;
	}

	final private function GetUserId(){
		return (string) $this->M_s_Element_userId;
	}

	final private function GetPassword(){
		return (string) $this->M_s_Element_password;
	}

	final private function GetLogonAs(){
		return (string) $this->M_s_Element_logonAs;
	}

	final public function GetConnectorId(){
		return (string) (isset($this->M_s_Element_connectorId) ? $this->M_s_Element_connectorId : '');
	}

	final public function GetFiltersXml($P_b_decode=false){
		if($P_b_decode){
			return html_entity_decode($this->M_s_Element_filtersXml);
		}else{
			return $this->M_s_Element_filtersXml;
		}
	}

	final private function GetMessageType(){
		return (string) $this->M_s_Element_messageType;
	}

	final private function GetExternalMessageId(){
		return (string) $this->M_s_Element_externalMessageId;
	}

	final private function GetMessageContent(){
		return (string) $this->M_s_Element_messageContent;
	}

	final private function GetMessagePdf(){
		return (string) $this->M_s_Element_messagePdf;
	}

	final private function GetMessageStatusFilter(){
		return (string) $this->M_s_Element_messageStatusFilter;
	}

	final private function GetMessageId(){
		return (string) $this->M_s_Element_messageId;
	}

	final private function GetConnectorType(){
		return (string) (isset($this->M_s_Element_connectorType) ? $this->M_s_Element_connectorType : '');
	}

	final private function GetConnectorVersion(){
		return (int) $this->M_i_Element_connectorVersion;
	}

	final private function GetDataXml(){
		return (string) $this->M_s_Element_dataXml;
	}

	final private function GetDataID(){
		return (string) $this->M_s_Element_dataID;
	}

	final private function GetParametersXml(){
		return (string) $this->M_s_Element_parametersXml;
	}

	final private function GetSubjectID(){
		return (string) $this->M_s_Element_subjectID;
	}

	final private function GetFileID(){
		return (string) $this->M_s_Element_fileID;
	}

	final private function GetReportID(){
		return (string) $this->M_s_Element_reportID;
	}

	final public function GetUserToken(){
		return $this->GetToken();
	}

	final public function GetToken(){
		return (string) $this->M_s_Element_token;
	}

	final private function GetSkip(){
		return (string) $this->M_s_Element_skip;
	}

	final private function GetTake(){
		return (string) $this->M_s_Element_take;
	}

	final private function GetSortField(){
		return (string) $this->M_s_Element_sortfield;
	}

	final private function GetSortDirection(){
		return (string) $this->M_i_Element_sortdirection;
	}

	final private function GetOptions(){
		return (string) $this->XMLEncode($this->M_s_Element_options);
	}

	final public function GenerateOptions(){
		$sorting = '';

		if($this->GetSortField()){
			$sorting = '   <Index><Field FieldId="' . $this->GetSortField() . '" OperatorType="' . $this->GetSortDirection() . '" /></Index>' . PHP_EOL ;
		}

		$L_s_optionsXml =
			'<Options>' . PHP_EOL .
			'	<Skip>' . $this->GetSkip() . '</Skip>' . PHP_EOL .
			'	<Take>' . $this->GetTake() . '</Take>' . PHP_EOL .
			$sorting .
			'</Options>';
		$this->SetOptions($L_s_optionsXml);
	}

	public function SetDeprecatedSortingMethod(){
		$this->M_b_ProfitOldSorting = true;
		return $this;
	}
	public function GetDeprecatedSortingMethod(){
		return $this->M_b_ProfitOldSorting;
	}

	/* CHECKERS */

	final public function XMLEncode($P_s_in){
	  $L_s_out = str_replace("&", "&amp;", $P_s_in);
	  $L_s_out = str_replace("%", "&#37;", $L_s_out);
	  $L_s_out = str_replace("<", "&lt;",  $L_s_out);
	  $L_s_out = str_replace(">", "&gt;",  $L_s_out);
	  return (string) $L_s_out;
	}

	final private function CheckClientResult(){

		if($this->M_o_Client->GetResponseHttpCode() == 500 && preg_match("@faultcode>[^<]+<\/faultcode@i", $this->GetClientResponse())){
			// ANTA Error!
			$this->SetParsedANTAError();
			if($this->ANTAError()->code !== Connector::NON_ANTA_ERROR){
				throw new Exception("ANTA / Profit error, Details in method ANTAError() [ " . $this->ANTAError()->code . " - " . $this->ANTAError()->message . " ]", 5);
			}
		}

		if($this->M_o_Client->GetResponseHttpCode() !== 200){
			throw new Exception("Soft-HTTP error [ " .
				$this->M_o_Client->GetResponseHttpCode()  . " " .
				$this->Connection()->GetHTTPStatusText($this->M_o_Client->GetResponseHttpCode() ) .
			" ]", 4);
		}

		// If we're here stuff should be fine, HTTP code must be 200...
		$L_b_ConnectorMatch = false;
		if(preg_match("@<(".$this->GetResponseObject()."|ExecuteResult)>(.*)<\/(".$this->GetResponseObject()."|ExecuteResult)>@ms", $this->GetClientResponse(), $L_s_ResponseMatch)){
			if(isset($L_s_ResponseMatch[2])){
				// Returnvars
				$L_b_ConnectorMatch = true;
				$this->SetOutputXML($L_s_ResponseMatch[2]);
			}
		}
		if(preg_match("@<(".$this->GetResponseObject()."|ExecuteResult)([^>]+\/)>@ms", $this->GetClientResponse(), $L_s_ResponseMatch)){
			if(isset($L_s_ResponseMatch[1])){
				// Blank results
				$L_b_ConnectorMatch = true;
			}
		}

		// If we're here stuff looks fine by HTTP status 200, but response is not from an AFAS Connector!
		if($L_b_ConnectorMatch === false){
			throw new Exception ("Unexpected results (Results are not from AFAS Connector)", 6);
		}
	}

}


