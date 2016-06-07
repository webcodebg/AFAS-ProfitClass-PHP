<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\Connection;
use \Exception;
use \SimpleXMLElement;

@require_once(dirname(__FILE__) . '/Connector.php');

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

class OSCConnector extends Connector {
	private $M_a_results;
	private $M_b_MethodSet;

	private $M_s_Element_contactEmailAddresses;
	private $M_s_Element_emailAddresses;
	private $M_s_Element_emailHashesXML;
	private $M_s_Element_id;
	private $M_s_Element_fileId;
	private $M_s_Element_data;
	private $M_s_Element_fileName;
	private $M_s_Element_url;
	private $M_s_Element_startSession;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);
		$this->setConnectorNameIsSet('_null_');
		$this->M_b_MethodSet = false;
	}

	final public function SetToken($P_s_Token){
		parent::SetToken($P_s_Token);
		$this->SetRequiredElement(Connector::USERTOKEN);
	}

	/* - - - - - - - - - - - - - - - - - - - */

	final public function SetContactEmailAddresses($P_s_ContactEmailAddresses){
		$this->M_s_Element_contactEmailAddresses = trim($P_s_ContactEmailAddresses);
	}

	final public function SetEmailAddresses($P_s_EmailAddresses){
		$this->M_s_Element_emailAddresses = trim($P_s_EmailAddresses);
	}

	final public function SetEmailHashesXML($P_s_EmailHashesXML){
		$this->M_s_Element_emailHashesXML = trim($P_s_EmailHashesXML);
	}

	final public function SetId($P_s_Id){
		$this->M_s_Element_id = trim($P_s_Id);
	}

	final public function SetFileId($P_s_FileId){
		$this->M_s_Element_fileId = trim($P_s_FileId);
	}

	final public function SetData($P_s_Data){
		$this->M_s_Element_data = $P_s_Data;
	}

	final public function SetFileName($P_s_FileName){
		$this->M_s_Element_fileName = trim($P_s_FileName);
	}

	final public function SetUrl($P_s_Url){
		$this->M_s_Element_url = trim($P_s_Url);
	}

	final public function SetStartSession($P_s_StartSession){
		$this->M_s_Element_startSession = trim($P_s_StartSession);
	}

	/* - - - - - - - - - - - - - - - - - - - */

	public function setMethod_CreateContactLink(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('CreateContactLink');
		$this->SetResponseObject('CreateContactLinkResult');

		$this->SetRequiredElement('contactEmailAddresses');
		// $this->SetContactEmailAddresses('');
	}

	public function setMethod_GetAllContacts(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetAllContacts');
		$this->SetResponseObject('GetAllContactsResult');
	}

	public function setMethod_GetBasicContacts(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetBasicContacts');
		$this->SetResponseObject('GetBasicContactsResult');

		$this->SetRequiredElement('emailAddresses');
		// $this->SetEmailAddresses('');
	}

	public function setMethod_GetSomeContacts(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetSomeContacts');
		$this->SetResponseObject('GetSomeContactsResult');

		$this->SetRequiredElement('emailHashesXML');
		// $this->SetEmailHashesXML('');
	}

	public function setMethod_GetSubjectTypes(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetSubjectTypes');
		$this->SetResponseObject('GetSubjectTypesResult');
	}

	public function setMethod_GetVersion(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetVersion');
		$this->SetResponseObject('GetVersionResult');
	}

	public function setMethod_RefreshHashes(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('RefreshHashes');
		$this->SetResponseObject('RefreshHashesResult');
	}

	public function setMethod_RemoveContactLink(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('RemoveContactLink');
		$this->SetResponseObject('RemoveContactLinkResult');

		$this->SetRequiredElement('id');
		// $this->SetId('');
	}

	public function setMethod_UpdateSubjectAttachment(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('UpdateSubjectAttachment');
		$this->SetResponseObject('UpdateSubjectAttachmentResult');

		$this->SetRequiredElement('subjectId');
		$this->SetRequiredElement('fileId');
		// $this->SetSubjectId('');
		// $this->SetFileId('');
	}

	public function setMethod_UploadFile(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('UploadFile');
		$this->SetResponseObject('UploadFileResult');

		$this->SetRequiredElement('data');
		$this->SetRequiredElement('fileName');
		// $this->SetData('');
		// $this->SetFilename('');
	}

	public function setMethod_ValidateInSiteUrl(){
		$this->M_b_MethodSet = true;
		$this->SetMethodName('ValidateInSiteUrl');
		$this->SetResponseObject('ValidateInSiteUrlResult');

		$this->SetRequiredElement('url');
		$this->SetRequiredElement('startSession');
		// $this->SetUrl('');
		// $this->SetStartSession('');
	}

	/* - - - - - - - - - - - - - - - - - - - */

	final public function GetContactEmailAddresses(){
		return (string) $this->M_s_Element_contactEmailAddresses;
	}

	final public function GetEmailAddresses(){
		return (string) $this->M_s_Element_emailAddresses;
	}

	final public function GetEmailHashesXML(){
		return (string) $this->M_s_Element_emailHashesXML;
	}

	final public function GetId(){
		return (string) $this->M_s_Element_id;
	}

	final public function GetFileId(){
		return (string) $this->M_s_Element_fileId;
	}

	final public function GetData(){
		return (string) $this->M_s_Element_data;
	}

	final public function GetFileName(){
		return (string) $this->M_s_Element_fileName;
	}

	final public function GetUrl(){
		return (string) $this->M_s_Element_url;
	}

	final public function GetStartSession(){
		return (string) $this->M_s_Element_startSession;
	}

	/* - - - - - - - - - - - - - - - - - - - */

	final public function Execute(){
		if(!$this->M_b_MethodSet){
			throw new Exception("OSC Connector Method not set! Use [ setMethod_* ] ", 6000);
		}

		parent::Execute();
	}

	final public function GetResults(){
		$L_s_Hash = strip_tags(trim($this->GetEncodedXML()));

		if(preg_match("@^[a-zA-Z0-9]{32}$@", $L_s_Hash)){
			// We've got a hash!
			$this->M_a_results = $L_s_Hash;
		}else{
			// No MD5 Hash, maybe XML
			if($L_s_Results = @html_entity_decode($this->GetEncodedXML())){
				try {
					$L_o_Results = @ new SimpleXMLElement($L_s_Results);
					$this->M_a_results = $L_o_Results;
				}
				catch (Exception $e){
					throw new Exception ("Response [data] cannot be parsed as XML", 1002, $e);
				}
			}else{
				throw new Exception ("Response from connector cannot be entity-decoded (or not executed yet)", 1000);
			}
		}

		return $this->M_a_results;
	}

}


