<?php
/*Secret Key*/
define('SECURTY_KEY',md5('CsplCRM@#$%432'));
/*Access Key*/
//$num = bin2hex(random_bytes(32));
define('ACCESS_KEY','4ecebd9cd9bca77c98c4624019631415a660254ef08401bf3dd7392b563e124b');
///// API URL
define('ATTACHMENT_URL','https://crm.eaplworld.com/Serial_API/');
/*Data Type*/
define('BOOLEAN',1);
define('INTEGER',2);
define('STRING',3);
/*Error Codes*/
define('REQUEST_METHOD_NOT_VALID',405);
define('REQUEST_CONTENTTYPE_NOT_VALID',415);
define('REQUEST_NOT_VALID',400);
define('VALIDATE_PARAMETER_REQUIRED',400);
define('VALIDATE_PARAMETER_DATATYPE',400);
define('API_NAME_REQUIRED',411);
define('API_PARAM_REQUIRED',400);
define('API_DOES_NOT_EXIST',404);
define('INVALID_USER_PASSWORD',401);
define('USER_NOT_ACTIVE',403);
define('JWT_PROCESSING_ERROR',401);
define('USER_NOT_FOUND',401);
define('TOKEN_NOT_EXPIRE',401);
define('SUCCESS_RESPONSE',200);
define('FAILED_RESPONSE',400);
define('INVALID_TOKEN',498);

/*Server Error*/
define('AUTHORIZATION_HEADER_NOT_FOUND',403);
define('ACCESS_TOKEN_ERROR',401);
?>