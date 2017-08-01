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

class SubjectConnector extends Connector {
	private $M_a_results;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);

		$this->setConnectorNameIsSet('_null_');
		$this->SetRequiredElement(Connector::SUBJECTID);

		$this->SetMethodName('GetAttachment');
		$this->SetResponseObject('GetAttachmentResult');
	}

	final public function SubjectID($P_i_subject){
		$this->SetSubjectID( (int) $P_i_subject );
	}

	final public function GetResults($P_i_SubjectID=false){
		if(!isset($this->M_s_OUTPUTXML) && $P_i_SubjectID !== false){
			$this->Execute($P_i_SubjectID);
		}
		if(!isset($this->M_a_results)){
			$L_s_data = $this->GetEncodedXML();
			$L_s_data = base64_decode($L_s_data);
			$this->M_a_results = $L_s_data;
		}
		return $this->M_a_results;
	}

	final public function Execute($P_i_SubjectID=false){
		if($P_i_SubjectID !== false) $this->SubjectID($P_i_SubjectID);

		parent::Execute();
	}
}


