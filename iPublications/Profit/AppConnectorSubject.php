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

class AppConnectorSubject extends Connector {
	private $M_a_results;

	final public function __construct(Connection $P_o_ConnectionSettings){
		$this->NoCredentials();

		parent::__construct($P_o_ConnectionSettings);

		$this->setConnectorNameIsSet('_null_');
		$this->SetRequiredElement(Connector::TOKEN);
		$this->SetRequiredElement(Connector::SUBJECTID);

		$this->SetMethodName('GetAttachment');
		$this->SetResponseObject('GetAttachmentResult');
	}

	final public function SubjectID($P_i_subject,$P_s_FileId=null){
		$this->SetSubjectID( (int) $P_i_subject );
		$this->SetFileID( (string) $P_s_FileId );

		if(!empty($P_s_FileId)){
			// $this->SetMethodName('GetSubjectAttachmentData');
			// $this->SetResponseObject('GetSubjectAttachmentDataResult');
			$this->SetRequiredElement(Connector::FILEID);
		}
	}

	final public function GetResults($P_i_SubjectID=false,$P_s_FileId=null){
		if(!isset($this->M_s_OUTPUTXML) && $P_i_SubjectID !== false){
			$this->Execute($P_i_SubjectID,$P_s_FileId);
		}
		if(!isset($this->M_a_results)){
			$L_s_data = $this->GetEncodedXML();
			if($L_s_data !== '&lt;/SubjectAttachmentInfo&gt;'){
				$L_s_data = base64_decode($L_s_data);
			}else{
				$L_s_data = '';
			}
			$this->M_a_results = $L_s_data;
		}
		return $this->M_a_results;
	}

	final public function Execute($P_i_SubjectID=false,$P_s_FileId=null){
		if($P_i_SubjectID !== false) $this->SubjectID($P_i_SubjectID,$P_s_FileId);

		if(!preg_match("@[A-Z0-9a-z]{32,}@", $this->GetToken())) throw new Exception("No Token given or invalid.", 1004);

		parent::Execute();
	}
}


