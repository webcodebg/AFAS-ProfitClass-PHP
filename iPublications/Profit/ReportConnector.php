<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\ConnectorFilter;
use \iPublications\Profit\Connection;
use \Exception;
use \SimpleXMLElement;

@require_once(dirname(__FILE__) . '/Connector.php');
@require_once(dirname(__FILE__) . '/ConnectorFilter.php');

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
 * 		1000 = Response cannot be entity-decoded
 * 		1001 = Response container is not an Connector-object
 * 		1002 = Response cannot be parsed as XML
*/

class ReportConnector extends Connector {
	private $M_a_results;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);

		$this->setConnectorNameIsSet('_null_');

		$this->SetRequiredElement(Connector::LOGONAS);
		$this->SetRequiredElement(Connector::REPORTID);
		$this->SetRequiredElement(Connector::FILTERSXML);

		$this->SetMethodName('Execute');
		$this->SetResponseObject('ExecuteResult');
	}

	final public function Execute($P_s_FilterU002=false){
		if($P_s_FilterU002 !== false && $this->GetFilter() == ''){
			$L_o_filter = new ConnectorFilter;
			$L_o_filter->add('U002', 1, $P_s_FilterU002);
			$this->SetFilter($L_o_filter);
		}
		parent::Execute();
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

	/* ALL THE STUFF RELATED TO FILTERS */

	final public function GetFilter(){
		return $this->GetFiltersXml(true);
	}

	final public function SetFilterArray($L_a_in){
		$L_s_return = '';

		if(!is_int(key($L_a_in))){
		  $L_a_in = array($L_a_in);
		}

		$L_s_return = '<Filters>' . PHP_EOL;
		$or_filter = 1;
		foreach($L_a_in as $v){
			$L_s_return .= '  <Filter FilterId="Filter'.$or_filter.'">' . PHP_EOL;
			foreach($v as $field => $string){
		    	list($field,$operator) = explode("|", $field);
			  	$L_s_return .= '    <Field FieldId="'.(stripslashes($field)).'" OperatorType="'.$operator.'">'.$this->XMLEncode($string).'</Field>' . PHP_EOL;
		    }
	  		$L_s_return .= '  </Filter>' . PHP_EOL;
	  		$or_filter++;
		}
		$L_s_return .= '</Filters>';

		$this->SetFiltersXml($L_s_return);
	}

	final public function SetFilterObject(ConnectorFilter $P_o_filter){
		$L_a_filterArrayFromObject = $P_o_filter->get();
		$this->SetFilterArray($L_a_filterArrayFromObject);
	}

	final public function SetFilter($P_m_filter){
		if(is_array($P_m_filter)){
			$this->SetFilterArray($P_m_filter);
		}elseif(is_string($P_m_filter)){
			$this->SetFiltersXML($P_m_filter);
		}else{
			// Object...
			$this->SetFilterObject($P_m_filter);
		}
	}

}


