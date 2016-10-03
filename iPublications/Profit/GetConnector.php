<?php

namespace iPublications\Profit;
use \iPublications\Profit\Connector;
use \iPublications\Profit\ConnectorFilter;
use \iPublications\Profit\Connection;
use \iPublications\Profit\ConnectorException\ResponseRequirementException;
use \Exception;
use \SimpleXMLElement;

@require_once(dirname(__FILE__) . '/Connector.php');
@require_once(dirname(__FILE__) . '/ConnectorFilter.php');
@require_once(dirname(__FILE__) . '/ConnectorException/ResponseRequirementException.php');

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
 * 		1003 = Sorting / Limit shoud bel set BEFORE first GetResults!
*/

class GetConnector extends Connector {
	private $M_a_fields;
	private $M_a_results;
	private $M_i_limit;
	private $M_s_sortField;
	private $M_s_sortType;
	private $M_b_resultsParsed;
	private $M_a_requiredResponseFields;

	const SORT_ASC  = 1;
	const SORT_DESC = 0;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);

		$this->SetRequiredElement(Connector::LOGONAS);
		$this->SetRequiredElement(Connector::CONNECTORID);
		$this->SetRequiredElement(Connector::FILTERSXML);
		$this->SetRequiredElement(Connector::OPTIONS);

		$this->SetSkip(-1);
		$this->SetTake(-1);

		$this->SetResultsParsed(false);

		$this->SetMethodName('GetDataWithOptions');
		$this->SetResponseObject('GetDataWithOptionsResult');
	}

	final public function SetRequiredFields($P_m_fields){
		if(is_string($P_m_fields)){
			$this->M_a_requiredResponseFields[] = $P_m_fields;
			return true;
		}elseif(is_array($P_m_fields)){
			$this->M_a_requiredResponseFields = array_values($P_m_fields);
			return true;
		}elseif(is_object($P_m_fields)){
			$this->M_a_requiredResponseFields = array_values( (array) $P_m_fields);
			return true;
		}

		return false;
	}

	final public function Execute($P_s_ConnectorName=false){
		$this->GenerateOptions();

		if($P_s_ConnectorName !== false) $this->SetConnectorId($P_s_ConnectorName);
		parent::Execute();
	}

	final private function SetResultsParsed($P_b_resultsParsed = false){
		$this->M_b_resultsParsed = (bool) $P_b_resultsParsed;
	}

	final private function GetResultsParsed(){
		return (bool) $this->M_b_resultsParsed;
	}

	final private function SetField($P_s_fieldName,$P_s_fieldType='string'){
		$L_s_fieldType = (string) $P_s_fieldType;
		$L_s_fieldType = preg_replace("@^[a-z]{1,}:@", "", $L_s_fieldType);

		$this->M_a_fields[(string) $P_s_fieldName] = $L_s_fieldType;
	}

	final public function GetFields(){
		if(!isset($this->M_a_results)){
			$this->GetResults();
		}
		return (array) $this->M_a_fields;
	}

	final public function GetResults(){
		if(!isset($this->M_a_results)){
			$this->ParseResults();
		}
		return (array) $this->M_a_results;
	}

	final public function SetLimit($P_i_count = 1){
		if(!$this->GetResultsParsed()){
			$this->M_i_limit = (int) $P_i_count;
			if($this->M_i_limit < 1) $this->M_i_limit = 1;
		}else{
			throw new Exception("Limit should be set BEFORE first GetResults!", 1003);
		}
	}

	final public function SetSorting($P_s_fieldName = '', $P_s_sortType = self::SORT_ASC){
		if(!$this->GetDeprecatedSortingMethod()){
			$this->SetSortField($P_s_fieldName);
			$this->SetSortDirection($P_s_sortType);
		}else{
			if(!$this->GetResultsParsed()){
				$this->M_s_sortType  = ($P_s_sortType !== self::SORT_DESC ? self::SORT_ASC : SORT_DESC);
				$this->M_s_sortField = trim($P_s_fieldName);
			}else{
				throw new Exception("Sorting should be set BEFORE first GetResults!", 1003);
			}
		}
	}

	final private function SetResults($P_a_results){
		$this->M_a_results = (array) $P_a_results;
		if(!empty($P_a_results) && $this->GetDeprecatedSortingMethod()){
			if(!empty($this->M_s_sortType)) $this->applySorting();
			if(!empty($this->M_i_limit))	$this->applyLimit();
		}

		if(isset($this->M_a_requiredResponseFields)){
			foreach($this->M_a_requiredResponseFields as $L_s_field){
				if(is_string($L_s_field)){
					if(!isset($this->M_a_fields[$L_s_field])){
						throw new ResponseRequirementException("Field [ " . $L_s_field . " ] required, not in Connector Response", 1);
					}
				}
			}
		}
	}

	final private function applySorting(){
		usort($this->M_a_results, "self::applySorting_CMP");
	}

	final private function applySorting_CMP($P_m_a, $P_m_b){
		$L_s_Field = $this->M_s_sortField;
		if($this->M_s_sortType == self::SORT_ASC){
			$L_m_y = $P_m_a;
			$L_m_z = $P_m_b;
		}else{
			$L_m_y = $P_m_b;
			$L_m_z = $P_m_a;
		}
	    return strnatcasecmp($L_m_y->$L_s_Field,$L_m_z->$L_s_Field);
	}

	final private function applyLimit(){
		$this->M_a_results = array_slice($this->M_a_results, 0, $this->M_i_limit);
	}

	final private function ParseResults(){
		$this->SetResultsParsed(true);

		$L_a_Result  = array();
		$L_s_ConnectorName = $this->GetConnectorId();

		if($L_s_Results = @html_entity_decode($this->GetEncodedXML())){
			$L_s_Results_header = preg_replace("@<[a-z]+:@", "<", $L_s_Results);
			$L_s_Results_header = preg_replace("@<\/[a-z]+:@", "</", $L_s_Results_header);

			try {
				$L_s_Results_header = @ new SimpleXMLElement($L_s_Results_header);

				if(isset($L_s_Results_header->schema->element->complexType->choice->element->complexType->sequence->element)){
					foreach($L_s_Results_header->schema->element->complexType->choice->element->complexType->sequence->element as $L_o_field){
						$L_a_data = $L_o_field->attributes();
						if(isset($L_a_data->name, $L_a_data->type)){
							$this->SetField($L_a_data->name,$L_a_data->type);
						}
					}
				}
				unset($L_s_Results_header);
			}
			catch (Exception $e){
				throw new Exception ("Response [fieldlist] cannot be parsed as XML", 1002, $e);
			}

			try {
				$L_o_Results = @ new SimpleXMLElement($L_s_Results);

				// Commented out: Empty results can be valid too!
				// if(isset($L_o_Results->$L_s_ConnectorName)){
				foreach($L_o_Results->$L_s_ConnectorName as $L_m_result){
					$L_a_Result[] = $L_m_result;
				}

				$this->SetResults($L_a_Result);
				// }else{
				// 	 throw new Exception ("Response container is not an Connector-object", 1001);
				// }
			}
			catch (ResponseRequirementException $e){
				// Catch possible Exception generated by SetResponse;
				throw $e;
			}
			catch (Exception $e){
				throw new Exception ("Response [data] cannot be parsed as XML", 1002, $e);
			}
		}else{
			throw new Exception ("Response from connector cannot be entity-decoded", 1000);
		}
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


