<?php
namespace AdminBundle\Utils;

use GuzzleHttp\Client;

/**
 *
 * Copy of markroland/converge-api-php with small changes
 *
 * @author Mark Roland
 * @copyright 2014 Mark Roland
 * @license http://opensource.org/licenses/MIT
 * @link http://github.com/markroland/converge-api-php
 *
 **/
class ConvergeApi
{
    /**
     * Merchant ID
     * @var string
     */
    private $merchantId = '';

    /**
     * User ID
     * @var string
     */
    private $userId = '';

    /**
     * Pin
     * @var string
     */
    private $pin = '';

    /**
     * API Live mode
     * @var boolean
     */
    private $live = true;

    /**
     * Insecure mode for old servers
     * @var boolean
     */
    private $insecure = false;

    /**
     * A variable to hold debugging information
     * @var array
     */
    public $debug = array();

    /**
     * Class constructor
     * @param string  $merchantId
     * @param string  $userId
     * @param boolean $live
     * @param boolean $insecure
     **/
    public function __construct($merchantId, $userId, $live = true, $insecure = false)
    {
        $this->merchantId = $merchantId;
        $this->userId = $userId;
        $this->live = $live;
        $this->insecure = $insecure;
    }

    /**
     * @param string $transactionType
     * @param string $terminalPin
     * @param array  $parameters
     * @return array
     */
    public function request($transactionType, $terminalPin, $merchantId, array $parameters = [])
    {
        $this->pin = $terminalPin;
	$this->merchantId = $merchantId;

        $parameters['ssl_transaction_type'] = $transactionType;

        return $this->sendRequest($parameters);
    }

    /**
     * Send a HTTP request to the API
     *
     * @param array $data Any data to be sent to the API
     * @param string $multiSplit The key in the response body to use to break multiple records on
     * @param string $multiKey The key in the response array to put the multiple results
     * @return array
     **/
    private function sendRequest($data, $multiSplit = null, $multiKey = null)
    {
        $responseBody = $this->httpRequest($data);

        // Parse and return
        return $this->parseAsciiResponse($responseBody, $multiSplit, $multiKey);
    }

    private function httpRequest($data)
    {

	// API log...
	$apiLog = fopen("/tmp/convergeApi.log","a");

        // Standard data
        $data['ssl_merchant_id'] = $this->merchantId;
        $data['ssl_user_id'] = $this->userId;
        $data['ssl_pin'] = $this->pin;
        $data['ssl_show_form'] = 'false';
        $data['ssl_result_format'] = 'ascii';

	/*
        if (!empty($data['ssl_test_mode']) && !is_string($data['ssl_test_mode'])) {
            $data['ssl_test_mode'] = $data['ssl_test_mode'] ? 'true' : 'false';
        } else {
            $data['ssl_test_mode'] = 'false';
        }
	*/
	$data['ssl_test_mode'] = $this->live ? 'false' : 'true';  // set from parameters.yml:converge_is_live

	if ($this->live) {
	
		$guzzleOptions = ['defaults' => []];

		// I haven't tested this but Goooogling gives:
		// http://stackoverflow.com/questions/28066409/how-to-ignore-invalid-ssl-certificate-errors-in-guzzle-5
		$guzzleOptions['defaults']['verify'] = !$this->insecure;

		// Set request
		if ($this->live) {
		    $requestUrl = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
		} else {
		    $requestUrl = 'https://demo.myvirtualmerchant.com/VirtualMerchantDemo/process.do';
		}
		$requestUrl = 'https://api.convergepay.com/VirtualMerchant/process.do';

		$body = http_build_query($data);

		// Debugging output
		$this->debug = array();
		$this->debug['Request URL'] = $requestUrl;
		$this->debug['SSL Mode'] = $this->insecure ? 'WARNING: VERIFICATION DISABLED': 'Verification enabled';
		$this->debug['Posted Data'] = $data ? $body : null;
		echo "Sending: ".print_r($data,true);
		fwrite($apiLog,"Sending: ".print_r($data,true));

		try {
		    $client = new Client($guzzleOptions);
		    $response = $client->request('POST', $requestUrl, [
			'form_params' => $data,
		    ]);
		echo "API Call successful";
		fwrite($apiLog,"API Call successful\n");
		} catch (\Exception $e) {
		    $this->debug['Exception'] = $e;
		    echo "API Call not successful...Received (exception): ".print_r($e,true);
		    fwrite($apiLog,"API Call not successful...Received (exception): ".print_r($e,true));
		    return null;
		}

		$responseBody = (string) $response->getBody();
		echo "Response body was: ".print_r($responseBody,true);
		fwrite($apiLog,"Response body was: [$responseBody]");

		$this->debug['Response Status Code'] = $response->getStatusCode();
		$this->debug['Response Reason Phrase'] = $response->getReasonPhrase();
		$this->debug['Response Protocol Version'] = $response->getProtocolVersion();
		$this->debug['Response Headers'] = $response->getHeaders();
		$this->debug['Response Body'] = $responseBody;

	}

	else {
		$responseBody = '
ssl_result=0
ssl_token=0000000000000000
ssl_token_response=SUCCESS
ssl_txn_id="DUMMY_TXN_ID"';
		fwrite($apiLog,"(API Test Mode)".print_r($responseBody,true));
	}

	fclose($apiLog);

        return $responseBody;

    }

    /**
     * Parse an ASCII response
     * @param string $asciiString An ASCII (plaintext) Response
     * @param string $multiSplit The key in the response body to use to break multiple records on
     * @param string $multikey The key in the response array to put the multiple results
     * @return array
     *         If $multisplit is null, then response will be key value pairs like:
     *         array(
     *             'ssl_result' => '0',
     *             'ssl_result_message' => 'APPROVAL',
     *             'ssl_txn_id' => '1234'
     *         )
     *         from an input of:
     *         ssl_result=0
     *         ssl_result_message=APPROVAL
     *         ssl_txn_id=1234
     *
     *         If $multisplit is not null, then response will be key value pairs with an extra
     *         entry for $multikey, like when $multisplit="ssl_txn_id" and $multikey="transactions":
     *         array(
     *             'ssl_txn_count' => 2,
     *             "transactions" => array(
     *                 array("ssl_txn_id" => "1234", "ssl_amount" => "0.37"),
     *                 array("ssl_txn_id" => "5678", "ssl_amount" => "1.22")
     *             )
     *         )
     *         from an input of:
     *         ssl_txn_count=2
     *         ssl_txn_id=1234
     *         ssl_amount=0.37
     *         ssl_txn_id=5678
     *         ssl_amount=1.22
     **/
    private function parseAsciiResponse($asciiString, $multiSplit = null, $multikey = null)
    {
        $data = array();
        if ($multiSplit !== null) {
            $data[$multikey] = array();
        }
        $lines = explode("\n", $asciiString);
        $record = null;
        $isCapturingMulti = false;

        if (count($lines)) {
            foreach ($lines as $line) {
                if ($kvp = explode('=', $line, 2)) {
                    if (count($kvp) != 2) {
                        continue;
                    }
                    // if the key matches the $multisplit key to split on
                    // and we were already parsing a record, push onto
                    // the $multikey in data
                    if ($multiSplit !== null && $kvp[0] === $multiSplit) {
                        // once we start capturing records, we only have
                        // individual key value pairs that are transaction specific
                        $isCapturingMulti = true;
                        // if we were building a previous record, push it
                        if ($record !== null) {
                            $data[$multikey][] = $record;
                        }
                        // initialize record to empty
                        $record = array();
                    }

                    // if we are capturing multi, populate the record
                    // even with the key we split on
                    if ($isCapturingMulti) {
                        $record[$kvp[0]] = $kvp[1];
                    } else {
                    // if we are not capturing multiple records yet
                    // in the response, we have a response-wide key/value pair
                    // so store at the top level of the data
                        $data[$kvp[0]] = $kvp[1];
                    }
                }
            }
            // after we are done capturing, if we captured multiple records
            // the last one will not have been added on yet
            if ($isCapturingMulti && $record !== null) {
                $data[$multikey][] = $record;
                $record = null;
            }
        }

        return $data;
    }
}
