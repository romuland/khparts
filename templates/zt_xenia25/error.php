<<<<<<< HEAD
<?php

defined('JPATH_BASE') or die();
defined( '_JEXEC' ) or die( 'Restricted index access' );
$code = $this->error->get('code');
$status = getHTTPStatus(intval($code));
header("HTTP/1.x ".$status);
header("Status: ".$status);

if ($code=='404')
{
echo file_get_contents(JURI::root().'index.php?option=com_content&view=article&id=24');
	exit;
}
elseif ($code =='403')
{
echo file_get_contents(JURI::root().'index.php?option=com_content&view=article&id=25');
	exit;
}
/*else
{
echo file_get_contents(JURI::root().'index.php?option=com_content&view=article&id=26');
	exit;
}*/

function getHTTPStatus($code)
{
  $status = array
  (
    100 => '100 Continue',
    101 => '101 Switching Protocols',
    102 => '102 Processing',
    200 => '200 OK',
    201 => '201 Created',
    202 => '202 Accepted',
    203 => '203 Non-Authoritative Information',
    204 => '204 No Content',
    205 => '205 Reset Content',
    206 => '206 Partial Content',
    207 => '207 Multi Status',
    226 => '226 IM Used',
    300 => '300 Multiple Choices',
    301 => '301 Moved Permanently',
    302 => '302 Found',
    303 => '303 See Other',
    304 => '304 Not Modified',
    305 => '305 Use Proxy',
    306 => '306 (Unused)',
    307 => '307 Temporary Redirect',
    400 => '400 Bad Request',
    401 => '401 Unauthorized',
    402 => '402 Payment Required',
    403 => '403 Forbidden',
    404 => '404 Not Found',
    405 => '405 Method Not Allowed',
    406 => '406 Not Acceptable',
    407 => '407 Proxy Authentication Required',
    408 => '408 Request Timeout',
    409 => '409 Conflict',
    410 => '410 Gone',
    411 => '411 Length Required',
    412 => '412 Precondition Failed',
    413 => '413 Request Entity Too Large',
    414 => '414 Request-URI Too Long',
    415 => '415 Unsupported Media Type',
    416 => '416 Requested Range Not Satisfiable',
    417 => '417 Expectation Failed',
    420 => '420 Policy Not Fulfilled',
    421 => '421 Bad Mapping',
    422 => '422 Unprocessable Entity',
    423 => '423 Locked',
    424 => '424 Failed Dependency',
    426 => '426 Upgrade Required',
    449 => '449 Retry With',
    500 => '500 Internal Server Error',
    501 => '501 Not Implemented',
    502 => '502 Bad Gateway',
    503 => '503 Service Unavailable',
    504 => '504 Gateway Timeout',
    505 => '505 HTTP Version Not Supported',
    506 => '506 Variant Also Varies',
    507 => '507 Insufficient Storage',
    509 => '509 Bandwidth Limit Exceeded',
    510 => '510 Not Extended'
  );
 return $status[$code];
}

=======
<?php

defined('JPATH_BASE') or die();
defined( '_JEXEC' ) or die( 'Restricted index access' );
$code = $this->error->get('code');
$status = getHTTPStatus(intval($code));
header("HTTP/1.x ".$status);
header("Status: ".$status);

if ($code=='404')
{
echo file_get_contents(JURI::root().'index.php?option=com_content&view=article&id=24');
	exit;
}
elseif ($code =='403')
{
echo file_get_contents(JURI::root().'index.php?option=com_content&view=article&id=25');
	exit;
}
/*else
{
echo file_get_contents(JURI::root().'index.php?option=com_content&view=article&id=26');
	exit;
}*/

function getHTTPStatus($code)
{
  $status = array
  (
    100 => '100 Continue',
    101 => '101 Switching Protocols',
    102 => '102 Processing',
    200 => '200 OK',
    201 => '201 Created',
    202 => '202 Accepted',
    203 => '203 Non-Authoritative Information',
    204 => '204 No Content',
    205 => '205 Reset Content',
    206 => '206 Partial Content',
    207 => '207 Multi Status',
    226 => '226 IM Used',
    300 => '300 Multiple Choices',
    301 => '301 Moved Permanently',
    302 => '302 Found',
    303 => '303 See Other',
    304 => '304 Not Modified',
    305 => '305 Use Proxy',
    306 => '306 (Unused)',
    307 => '307 Temporary Redirect',
    400 => '400 Bad Request',
    401 => '401 Unauthorized',
    402 => '402 Payment Required',
    403 => '403 Forbidden',
    404 => '404 Not Found',
    405 => '405 Method Not Allowed',
    406 => '406 Not Acceptable',
    407 => '407 Proxy Authentication Required',
    408 => '408 Request Timeout',
    409 => '409 Conflict',
    410 => '410 Gone',
    411 => '411 Length Required',
    412 => '412 Precondition Failed',
    413 => '413 Request Entity Too Large',
    414 => '414 Request-URI Too Long',
    415 => '415 Unsupported Media Type',
    416 => '416 Requested Range Not Satisfiable',
    417 => '417 Expectation Failed',
    420 => '420 Policy Not Fulfilled',
    421 => '421 Bad Mapping',
    422 => '422 Unprocessable Entity',
    423 => '423 Locked',
    424 => '424 Failed Dependency',
    426 => '426 Upgrade Required',
    449 => '449 Retry With',
    500 => '500 Internal Server Error',
    501 => '501 Not Implemented',
    502 => '502 Bad Gateway',
    503 => '503 Service Unavailable',
    504 => '504 Gateway Timeout',
    505 => '505 HTTP Version Not Supported',
    506 => '506 Variant Also Varies',
    507 => '507 Insufficient Storage',
    509 => '509 Bandwidth Limit Exceeded',
    510 => '510 Not Extended'
  );
 return $status[$code];
}

>>>>>>> 1e1b309bbf9e27aa09f1eef63d7bfeed85150451
?>