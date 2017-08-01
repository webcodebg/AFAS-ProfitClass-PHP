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

class CommSvcConnector extends Connector {
	private $M_a_results;
	private $M_b_MethodSet;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);

		$this->setConnectorNameIsSet('_null_');

		$this->SetRequiredElement(Connector::ENVIRONMENTID);
		$this->SetRequiredElement(Connector::USERID);
		$this->SetRequiredElement(Connector::PASSWORD);

		$this->M_b_MethodSet = false;

		return $this;
	}

	/* - - - - - - - - - - - - - - - - - - - */

	public function UploadMessage($P_s_messageType='',$P_s_externalMessageId='',$P_s_messageContent='',$P_s_messagePdf=''){
		$this->SetRequiredElement(Connector::MESSAGETYPE);
		$this->SetRequiredElement(Connector::EXTERNALMESSAGEID);
		$this->SetRequiredElement(Connector::MESSAGEPDF);
		$this->SetRequiredElement(Connector::MESSAGECONTENT);

		if(!empty($P_s_messageType))
			$this->SetMessageType($P_s_messageType);
		if(!empty($P_s_externalMessageId))
			$this->SetExternalMessageId($P_s_externalMessageId);
		if(!empty($P_s_messageContent))
			$this->SetMessageContent($P_s_messageContent);
		if(!empty($P_s_messagePdf))
			$this->SetMessagePdf($P_s_messagePdf);

		$this->M_b_MethodSet = true;
		$this->SetMethodName('UploadMessage');
		$this->SetResponseObject('UploadMessageResponse');

		return $this->Execute();
	}

	public function GetUpdatedMessages($P_s_messageStatusFilter = ''){
		$this->SetRequiredElement(Connector::MESSAGESTATUSFILTER);

		if(!empty($P_s_messageStatusFilter))
			$this->SetMessageStatusFilter($P_s_messageStatusFilter);

		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetUpdatedMessages');
		$this->SetResponseObject('GetUpdatedMessagesResponse');

		return $this->Execute();
	}

	public function GetMessageStatus($P_s_messageId = ''){
		$this->SetRequiredElement(Connector::MESSAGEID);

		if(!empty($P_s_messageId))
			$this->SetMessageId($P_s_messageId);

		$this->M_b_MethodSet = true;
		$this->SetMethodName('GetMessageStatus');
		$this->SetResponseObject('GetMessageStatusResponse');

		return $this->Execute();
	}

	/* - - - - - - - - - - - - - - - - - - - */

	final public function Execute(){
		if(!$this->M_b_MethodSet){
			throw new Exception("CommSvcConnector Connector Method not set! Use [ UploadMessage() , GetUpdatedMessages() , GetMessageStatus() ] ", 6000);
		}

		parent::Execute();

		return $this;
	}

	final public function GetResults(){
		$L_s_xml  = trim($this->GetEncodedXML());
		$L_s_Hash = strip_tags($L_s_xml);

		if(!empty($L_s_xml)){
			if(preg_match("@^[a-zA-Z0-9]{32}$@", $L_s_Hash)){
				// We've got a hash!
				$this->M_a_results = $L_s_Hash;
			}else{
				// No MD5 Hash, maybe XML
				if($L_s_Results = @html_entity_decode($L_s_xml)){
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
		}else{
			return false;
		}
	}

}


