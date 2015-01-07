<?php

namespace Aws\EcsBundle\Utils;

/**
 * AwsECS does requests to Amazon Commerce Services
 *
 * @author Nicolas Gieringer <webdevng@gmail.com>
 *
 */
class AwsEcs {

    /**
     * @var string The AWS Secret Key
     */
    const AWS_SECRET_KEY = 'awssecretkey';

    /**
     * @var array The list of all static parameters
     */
    private $params = array(
        'AWSAccessKeyId' => 'awsaccesskeyid',
        'AssociateTag' => 'associatetag',
        'Service' => 'AWSECommerceService',
        'Timestamp' => '0000-00-00',
        'Version' => '0000-00-00'
    );

    /**
     * @var string The main query string for the request
     */
    private $queryString = '';

    /**
     * @var string The hash string for generating a signature
     */
    private $hashString = '';

    /**
     * @var string The return type of the request
     */
    public $returnType = 'XML';

    /**
     * @var array Parts of the request uri
     */
    public $httpRequest = array(
        'method' => 'GET',
        'host' => 'webservices.amazon.com',
        'uri' =>'/onca/xml'
    );

    /**
     * @var object Instance of this class
     */
    static private $instance = null;

    /**
     * Constructor.
     *
     * Set Version and Timestamp date
     *
     */
    public function __construct() {
        $this->params['Timestamp'] = date('Y-m-d', strtotime('next day'));
        $this->params['Version'] = date('Y-m-d', strtotime('next day'));
    }

    /**
     * Singleton Pattern
     *
     */
    static public function apiRequest() {
        if(self::$instance == null) {
            self::$instance = new AwsECS();
        }

        return self::$instance;
    }

    /**
     * Set the operation of the request.
     *
     * @param string      $operation A string to set the operation
     * @return AwsEcs this object
     */
    public function operationSet($operation) {
        $this->paramSet('Operation', $operation);

        return $this;
    }

    /**
     * Set the return type of the request.
     *
     * @param string      $returnType A string to set the returnType
     *
     * @return AwsEcs this object
     */
    public function returnType($returnType) {
        $this->returnType = $returnType;

        return $this;
    }

    /**
     * Set a new request param
     *
     * @param string      $key The key of the new param
     * @param string      $value The value of the new param
     *
     * @return AwsEcs this object
     */
    public function paramSet($key, $value) {
        if($key && $value) {
            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * Sort the request params
     *
     * The params have to be sorted by byte value
     *
     * @return AwsEcs the sorted params
     */
    public function paramsSort() {
        $sortedParams = array();
        if(is_array($this->params)) {
            foreach($this->params as $key => $value) {
                $position = ord($key);
                $sortedParams[] = array(
                    'order' => $position,
                    'param' => array(
                        'name' => $key,
                        'value' => $value,
                        'string' => $key.'='.$value.'&'
                    )
                );
            }
        }

        return $sortedParams;
    }

    /**
     * Encoding of the query string
     *
     * Amazon need a special encoding, not the standard
     * url encoding
     *
     * If no string is given the queryString will be used
     * @param null|string   $string String to parse
     *
     * @return AwsEcs the encoded string
     */
    public function stringEncode($string=null) {
        if($string == null) {
            $string = $this->queryString;
        }
        $replacements = array(
            '+' => '%20',
            '%3D' => '=',
            '%26' => '&'
        );

        foreach($replacements as $search => $replace) {
            $string = str_replace($search, $replace, $string);
        }

        return $string;
    }

    /**
     * Generates the query string
     *
     * Get the sorted params and encode them
     *
     */
    public function queryStringGenerate() {
        $sortedParams = $this->paramsSort();
        asort($sortedParams);

        foreach($sortedParams as $param) {
            $this->queryString .= urlencode($param['param']['string']);
        }

        $this->queryString = $this->stringEncode();
    }

    /**
     * Generates the signature for the request
     *
     * Amazon requires signature for each api request
     * The method generates this signature out of the
     * http params and the query string
     *
     * @return AwsEcs the signature
     */
    public function signatureGenerate() {
        array_walk(
            $this->httpRequest,
            function(&$value, $key) {
                $this->hashString .= $value."\n";
            }
        );

        $signInString = $this->hashString.$this->queryString;
        $signInString = rtrim($signInString, "&");

        $signature = urlencode(base64_encode(hash_hmac(
                    'sha256',
                    $signInString,
                    self::AWS_SECRET_KEY,
                    true
                )
            )
        );

        return $signature;
    }

    /**
     * Links all parts of the request uri
     *
     */
    //todo: add a return statement
    public function requestUrl() {
        $requestUrl = sprintf(
            'http://%s%s?%sSignature=%s',
            $this->httpRequest['host'],
            $this->httpRequest['uri'],
            $this->queryString,
            $this->signatureGenerate()
        );

        echo $requestUrl;
    }


    /**
     * Runs the request
     *
     */
    public function run() {
        $this->queryStringGenerate();
        $this->requestUrl();
    }

}

