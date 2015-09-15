<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\ReCaptcha;

use SilverWp\Debug;
use Zend\Http\Response as HTTPResponse;

/**
 * Zend_Service_ReCaptcha_Response
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage ReCaptcha
 */
class Response
{
	const MISSING_SECRET            = 'missing-input-secret';
	const INVALID_SECRET     = 'invalid-input-secret';
	const MISSING_RESPONSE   = 'missing-input-response';
	const INVALID_MX_RECORD  = 'invalid-input-response';

	/**
     * Status
     *
     * true if the response is valid or false otherwise
     *
     * @var boolean
     */
    protected $status = null;

    /**
     * Error code
     *
     * The error code if the status is false. The different error codes can be found in the
     * recaptcha API docs.
     *
     * @var string
     */
    protected $errorCode = null;

	protected $messageTemplates = [
//		self::MISSING_SECRET            => "Invalid type given. String expected",
//		self::INVALID_FORMAT     => "The input is not a valid email address. Use the basic format local-part@hostname",
//		self::INVALID_HOSTNAME   => "'%hostname%' is not a valid hostname for the email address",
//		self::INVALID_MX_RECORD  => "'%hostname%' does not appear to have any valid MX or A records for the email address",
//		self::INVALID_SEGMENT    => "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network",
//		self::DOT_ATOM           => "'%localPart%' can not be matched against dot-atom format",
//		self::QUOTED_STRING      => "'%localPart%' can not be matched against quoted-string format",
//		self::INVALID_LOCAL_PART => "'%localPart%' is not a valid local part for the email address",
//		self::LENGTH_EXCEEDED    => "The input exceeds the allowed length",
	];

    /**
     * Class constructor used to construct a response
     *
     * @param string $status
     * @param string $errorCode
     * @param \Zend\Http\Response $httpResponse If this is set the content will override $status and $errorCode
     */
    public function __construct($status = null, $errorCode = null, HTTPResponse $httpResponse = null)
    {
        if ($status !== null) {
            $this->setStatus($status);
        }

        if ($errorCode !== null) {
            $this->setErrorCode($errorCode);
        }

        if ($httpResponse !== null) {
            $this->setFromHttpResponse($httpResponse);
        }
    }

    /**
     * Set the status
     *
     * @param string $status
     * @return \ZendService\ReCaptcha\Response
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Alias for getStatus()
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->getStatus();
    }

    /**
     * Set the error code
     *
     * @param string $errorCode
     * @return \ZendService\ReCaptcha\Response
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get the error code
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Populate this instance based on a Zend_Http_Response object
     *
     * @param \Zend\Http\Response $response
     * @return \ZendService\ReCaptcha\Response
     */
    public function setFromHttpResponse(HTTPResponse $response)
    {
        $body = $response->getBody();

        $parse = \Zend\Json\Json::decode($response->getBody());
	    $this->setStatus($parse->success);
	    if (!$parse->success) {
	        if (isset($parse->{'error-codes'})) {
		        $this->setErrorCode($parse->{'error-codes'}[0]);
	        }
        }

        return $this;
    }
}
