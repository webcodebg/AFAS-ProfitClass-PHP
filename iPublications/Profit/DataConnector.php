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

class DataConnector extends Connector {	
	private $M_a_results;

	final public function __construct(Connection $P_o_ConnectionSettings){
		parent::__construct($P_o_ConnectionSettings);

		$this->SetRequiredElement(Connector::DATAID);
		$this->SetRequiredElement(Connector::PARAMETERSXML);

		$this->SetMethodName('Execute');
		$this->SetResponseObject('ExecuteResult');
	} 

	final public function GetResults($P_s_dataId=false){
		if(!isset($this->M_s_OUTPUTXML) && $P_s_dataId !== false){
			$this->Execute($P_s_dataId);
		}
		if(!isset($this->M_a_results)){
			$this->ParseResults();
		}
		return (array) $this->M_a_results;
	}

	final public function Execute($P_s_dataId=false){
		if($P_s_dataId !== false) $this->SetDataID($P_s_dataId);

		parent::Execute();
	}

	final public function SetParametersXml($P_s_XML){
		$L_s_XML = htmlentities($P_s_XML);
		parent::SetParametersXml($L_s_XML);
	}

	final private function ParseResults(){
		$L_a_Result  = array();
		$L_s_ConnectorName = 'ConnectorData';

		if($L_s_Results = @html_entity_decode($this->GetEncodedXML())){
			try {
				$L_o_Results = @ new SimpleXMLElement($L_s_Results);

				if(isset($L_o_Results->$L_s_ConnectorName)){
					foreach($L_o_Results->$L_s_ConnectorName as $L_m_result){
						$L_a_Result[] = $L_m_result;
					}

					$this->M_a_results = $L_a_Result[0];
				}else{
					throw new Exception ("Response container is not an Connector-object", 1001);
				}
			}
			catch (Exception $e){
				throw new Exception ("Response [data] cannot be parsed as XML", 1002, $e);
			}
		}else{
			throw new Exception ("Response from connector cannot be entity-decoded", 1000);
		}
	}

}


