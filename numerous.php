<?php

namespace GX;

use DateTime;
use DateTimeZone;

/** 
 * Numerous REST API client
 * API docs: http://docs.numerous.apiary.io/
 * 
 * @author    Gerwert
 */
class Numerous {

    /**
     * Fixed portion of the Resource URL
     */
    const API_BASE_URL = 'https://api.numerousapp.com';

    /**
     * Date format used by API
     */
    const DATE_NUMEROUS = 'Y-m-d\TH:i:s.000\Z';

    /**
     * Secret Numerous API key
     */
    private $_key = null;
    
    public function __construct($key) {
        if (empty($key)) {
            throw new \InvalidArgumentException('You must supply an API key');
        }        
        $this->_key = $key;
    }
   
    /**
     * List a user's Metrics.
     * Optional: user id (default 'me' for authenticted user)
     */
    public function metrics($user_id = 'me') {
    	$result = $this->get("/v2/users/{$user_id}/metrics");
    	return $result;
    }

    /**
     * Retrieve a Metric
     *
     * @param string    $metric_id  Id of metric to retrieve
     */
    public function metric($metric_id) {
        $result = $this->get("/v2/metrics/{$metric_id}", array(
            "expand" => "owner"
        ));
        return $result;        
    }

    /**
     * Create a Metric, containing at least a label.
     * Other properties can be supplied in de $fields array.
     *
     * @param string    $label      Metric label (name)
     * @param array     $fields     Properties to be updated
     * @param boolean   $private    Listed or not
     * @param boolean   $writeable  Who can update, everyone (true) or only owner (false)
     */
    public function createMetric($label, $fields = array(), $private = true, $writeable = false) {
        // Merge supplied properties with default values
        $data = array_merge(array(
            "label" => (string)$label,            
            "private" => (bool)$private,
            "writeable" => (bool)$writeable
        ), $fields);                
        $result = $this->post("/v2/metrics", $data);
        return $result;          
    }

    /**
     * Update metric properties.
     * Some useful optional properties:
     * - kind ("number", "currency", "percent", or "timer")
     * - units (e.g., "kWh" or "miles")
     * - moreURL
     */
    public function updateMetric($metric_id, $fields) {
        $data = array_merge(array(           
        ), $fields);
        $result = $this->post("/v2/metrics/{$metric_id}", $data, 'PUT');
        return $result;          
    }

    /**
     * Create/update a metric's photo
     *
     * @param string    $metric_id  Metric id
     * @param string    $filePath   Path to local PNG file to be uploaded
     */
    public function setPhoto($metric_id, $filePath) {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException('File not found: ' . $filePath);
        }
        if (exif_imagetype($filePath) != IMAGETYPE_PNG) {
           throw new \InvalidArgumentException('Supplied image is not of type PNG');
        }        
        $url = self::API_BASE_URL . "/v1/metrics/{$metric_id}/photo";

        // Post fields
        $fields = array(
            "image" => "@$filePath;type=image/png",             
        );

        $ch = curl_init(); //open connection                       
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);                                                                                             
        return $this->curl_exec($ch);                   
    }

    public function setPhotoFromUrl($metric_id, $url) {
        // Generate temp. file
        $temp = tmpfile();
        // Retrieve image form URL
        $img = file_get_contents($url);        
        fwrite($temp,$img);
        // Get temp. file name
        $meta_data = stream_get_meta_data($temp);
        $filename = $meta_data["uri"];        
        try {
            return $this->setPhoto($metric_id, $filename);
        } catch (Exception $e) {
            throw $e;
        } finally {            
            fclose($temp); // this removes the file;
        }
    }

    /**
     * Create a new event
     *
     * @param string    $metric_id  Metric id
     * @param string    $value      New value
     * @param DateTime  $updated    Give value to create an event with an updated value different than the current time
     * @param boolean   $add        If true sends the "action: ADD" (the value is added to the metric)
     */
    public function createEvent($metric_id, $value, DateTime $updated = null, $add = false) {        
        $data = array(
            "value" => $value
        );
        if ($updated) {
            $updated->setTimezone(new DateTimeZone('GMT'));
            $data['updated'] = $updated->format(self::DATE_NUMEROUS);
        }
        if ($add === true) {
            $data['action'] = 'ADD';
        }
        $result = $this->post("/v1/metrics/{$metric_id}/events", $data);
        return $result;  
    }

    /**
     * Create interaction (e.g. comment)
     */
    public function createInteraction($metric_id, $fields = array()) {        
        $data = array_merge(array( 
            "authorId"=> "me",
            "kind" => "comment"
        ), $fields);     
        $result = $this->post("/v1/metrics/{$metric_id}/interactions", $data);
        return $result;         
    }

    /**
     * Generic POST action
     */
    private function get($path, $params = array()) {
        $headers = array(                                                                          
            'Accept: application/json',
            'Content-Type: application/json'                                                                              		    
        );
        $url = self::API_BASE_URL . $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params, null, '&');            
        }
        
        $ch = curl_init(); //open connection                       
        curl_setopt($ch, CURLOPT_URL, $url);     
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);   

        return $this->curl_exec($ch);     	
    }

    /**
     * Generic POST/PUT action
     */
    private function post($path, array $data, $method = 'POST', $endpoint = false) {
    	// Data payload
        $data_string = json_encode($data);

        // Construct URL
        $url = self::API_BASE_URL . $path;

        // Headers
        $headers = array(                                                                          
            'Accept: application/json',
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data_string)                                                                      
        );

        $ch = curl_init(); //open connection                       
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  		
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        return $this->curl_exec($ch);
    } 

    private function curl_exec($ch) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);           
        curl_setopt($ch, CURLOPT_USERAGENT, "gx-numerous-api (github.com/onderweg)");              
        // Authentication to the Numerous API occurs via Basic HTTP Auth.
        curl_setopt($ch, CURLOPT_USERPWD, $this->_key . ':');

        $result = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        if(curl_errno($ch)) { // Check if any error occurred
            throw new NumerousException(curl_error($ch), NumerousException::NET_ERROR);
        }      
        curl_close($ch);

        $response = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
        if ($httpCode != 200 && $httpCode != 201) {     
            throw new NumerousException("HTTP error {$httpCode}, URL: {$url}", NumerousException::HTTP_ERROR);  
        }                          
        if ($response === null) { // Json can't be decoded
            throw new NumerousException("Invalid JSON response server: $response", NumerousException::JSON_ERROR);
        }
                
        return $response;        
    }

}

/**
 * NumerousException
 *
 * @author Gerwert
 */
class NumerousException extends \Exception {

    const NET_ERROR = 1;
    const HTTP_ERROR = 2;
    const JSON_ERROR = 3;

    /**
     * Returns exception information as JSON string
     */
    public function toJson() {
        return json_encode(array(
            "exception" => array(
                "message" => $this->getMessage(),
                "code" => $this->code,
                "in" => get_class($this) . " {$this->file}:{$this->line}"
            )
        ), JSON_PRETTY_PRINT);
    }

}