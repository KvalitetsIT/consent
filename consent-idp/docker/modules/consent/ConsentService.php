<?php
class sspmod_consent_Consent_Store_ConsentService extends sspmod_consent_Store
{
    private $_consentserviceurl;
    private $_useridattr;
    private $_correlationidheadername;

    public function __construct($config)
    {
        parent::__construct($config);


        if (!array_key_exists('consentserviceurl', $config)) {
            throw new Exception('consent:ConsentService - Missing required option \'consentserviceurl\'.');
        }

        if (!array_key_exists('useridattr', $config)) {
            throw new Exception('consent:ConsentService - Missing required option \'useridattr\'.');
        }

        $this->_consentserviceurl = $config['consentserviceurl'];
        $this->_spserviceurl = $config['spserviceurl'];
        $this->_useridattr = $config['useridattr'];
        
        $config = SimpleSAML_Configuration::getInstance();
    	
    	$this->correlationidheadername = $config->getString('correlationidheadername', 'correlation-id');
    }

    /**
     * Called before serialization.
     *
     * @return array The variables which should be serialized.
     */
    public function __sleep()
    {
        return array(
            '_consentserviceurl',
            '_useridattr'
        );
    }

    public function getMunicipality($userId,$attributeSet, $state)
    {
        assert('is_string($userId)');
        assert('is_string($attributeSet)');
        $serviceurl = $this->_spserviceurl;

        if($serviceurl == '') {
            return 0;
        }

        $attributeSet = $state['Attributes'];
        $spId = $state['core:SP'];
        $citizenIdArray = $attributeSet[$this->_useridattr];
        $citizenId = "";
        if (is_array($citizenIdArray) && count(citizenIdArray) > 0) {
            $citizenId = array_values($citizenIdArray)[0];
        }

        $headers = getallheaders();
        $corrId = $headers[$this->correlationidheadername];

        $jsonContent = '{"userId": "'.$citizenId.'"}';
        $curl = curl_init($serviceurl);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonContent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
    	    'Content-Length: ' . strlen($jsonContent),
    	    $this->correlationidheadername.': '.$corrId
        ));
        $curl_response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($curl_response);
        curl_close($curl);
        $kommuneKode = $result->{'kommunekode'};
        return $kommuneKode;
    }
 
    public function hasConsent($userId, $municipality, $destinationId, $attributeSet) {
		hasConsentMore($userId, $destinationId, $attributeSet, array());  
	}
	
    /**
     * Check for consent.
     *
     * This function checks whether a given user has authorized the release of
     * the attributes identified by $attributeSet from $source to $destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool True if the user has given consent earlier, false if not
     *              (or on error).
     */
    public function hasConsentMore($userId, $municipality, $destinationId, $attributeSet, &$state)
    {
        assert('is_string($userId)');
        assert('is_string($destinationId)');
        assert('is_string($attributeSet)');

        $attributeSet = $state['Attributes'];
        $spId = $state['core:SP'];
        $citizenIdArray = $attributeSet[$this->_useridattr];
        $citizenId = "";
        if (is_array($citizenIdArray) && count(citizenIdArray) > 0) {
        	$citizenId = array_values($citizenIdArray)[0];	
        } 
        
         
        $qry_str = '?userId='.$citizenId.'&appId='.$spId.'&municipalityId='.$municipality;
        $serviceurl = $this->_consentserviceurl.$qry_str;
        
        $headers = getallheaders();    	
    	$corrId = $headers[$this->correlationidheadername];
        
        $curl = curl_init($serviceurl); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($this->correlationidheadername.': '.$corrId));                                                                                                                   
        
        
       	$curl_response = curl_exec($curl);

       	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($curl_response);
       	curl_close($curl);
        // TODO tjek assert('$httpcode == 200');
        SimpleSAML_Logger::info($curl_response);
       	$status = $result->{'status'};
       	
       	$state['consent:canAccept'] = true;
       	if ($status == 'UNANSWERED' || $status == 'DEPRECATED' || $status == 'NO_MUNICIPALITY') {
       		$state['consent:template.content'] =  $result->{'templateContent'};
       		$state['consent:template.mimetype'] = $result->{'templateMimeType'}; 
       		$state['consent:friendlyName'] = $result->{'friendlyName'};  
       		
       		if ($status == 'UNANSWERED') {
       			$state['consent:acceptText'] = '{consent:consent:consent_accept}';
       			$state['consent:municipality'] = $result->{'municipalityId'};;
       		} else if ($status == 'DEPRECATED'){
       			$state['consent:acceptText'] = '{consent:consent:consent_accept_deprecated}';
       			$state['consent:municipality'] = $result->{'municipalityId'};;
       		}  else if ($status == 'NO_MUNICIPALITY') {
       		    $state['consent:acceptText'] = '{consent:consent:consent_accept_no_municipality}';
       		    $state['consent:canAccept'] = false;
       		}
       	}   	
       	
        return ($status == 'ACCEPTED'); 	    
    }

    /**
     * Save consent.
     *
     * Called when the user asks for the consent to be saved. If consent information
     * for the given user and destination already exists, it should be overwritten.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return void|true True if consent is deleted 
     */
    public function saveConsent($userId, $municipality, $destinationId, $attributeSet)
    {
        assert('is_string($userId)');
        assert('is_string($destinationId)');
        assert('is_string($attributeSet)');

        return saveConsentMore($userId, $municipality, $destinationId, $attributeSet, array());
    }

    public function saveConsentMore($userId, $municipality, $destinationId, $attributeSet, $state)
    {
        assert('is_string($userId)');
        assert('is_string($destinationId)');
        assert('is_string($attributeSet)');

        $attributeSet = $state['Attributes'];
        $citizenIdArray = $attributeSet[$this->_useridattr];
        $citizenId = "";
        if (is_array($citizenIdArray) && count(citizenIdArray) > 0) {
        	$citizenId = array_values($citizenIdArray)[0];	
        }
        
        $spId = $state['core:SP'];
        
        $updateConsent = array(
    		"userId" => $citizenId,
    		"municipalityId" =>  $municipality,
    		"appId" => $spId,
    		"consent" => "true"
		);
		$jsonConsent = json_encode($this->utf8ize($updateConsent));
        
        $serviceurl = $this->_consentserviceurl;
        
        $headers = getallheaders();    	
    	$corrId = $headers[$this->correlationidheadername];
  
        $curl = curl_init($serviceurl); 
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonConsent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
    		'Content-Type: application/json',  
    		'Content-Length: ' . strlen($jsonConsent),
    		$this->correlationidheadername.': '.$corrId                                                                             
		));
       	$curl_response = curl_exec($curl);

       	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
       	curl_close($curl);

        return true;
    }


    /**
     * Delete consent.
     *
     * Called when a user revokes consent for a given destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     *
     * @return int Number of consents deleted
     */
    public function deleteConsent($userId, $municipality, $destinationId)
    {
        assert('is_string($userId)');
        assert('is_string($municipality)');
        assert('is_string($destinationId)');

    }

    /**
     * Delete all consents.
     * 
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return int Number of consents deleted
     */
    public function deleteAllConsents($userId)
    {
        assert('is_string($userId)');

    }

    /**
     * Retrieve consents.
     *
     * This function should return a list of consents the user has saved.
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return array Array of all destination ids the user has given consent for.
     */
    public function getConsents($userId)
    {
        assert('is_string($userId)');

    }
    public function utf8ize($d) { 
    	if (is_array($d) || is_object($d))
    	    foreach ($d as &$v) $v = $this->utf8ize($v);
    	else
       	 return utf8_encode($d);

    	return $d;
	}
	
    
}
