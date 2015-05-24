<?php
require_once(dirname(__FILE__) . '/../config/config.php');

global $MLOG;

include_once('error_handler.php');

use RestService\Server;


/**
 * PHP doesn't handle put requests well.  The header "X-HTTP-Method-Override: PUT"
 * seemed to fix requests sent by cURL, but it's not doing so well for
 * google's Postman extension.  Therefore, to get PUT requests working
 * properly, there's a 'Script' directive in the vhost that sends us the
 * raw input, and we then parse that (if appropriate) and add it to the
 * $_REQUEST
**/

$rawinput=file_get_contents("php://input");
if (!count($_REQUEST) && strlen($rawinput)) {
	// Can we parse this?
	parse_str($rawinput, $arr);
	$_REQUEST['putData'] = $arr;
	$_POST['putData'] = $arr;
} else {
	$_REQUEST['putData'] = '';
}


////////////////////////////////////////////////////////////////////////
//   DEBUG STUFF
////////////////////////////////////////////////////////////////////////

$str = array();
$MLOG->addDebug("\n\n\n{$config->ansi->red}New API request{$config->ansi->clear}");
if (isset($_SERVER['REQUEST_METHOD'])) {
	$str[] = 'REQUEST_METHOD: '. $_SERVER['REQUEST_METHOD'];
} else {
	$_SERVER['REQUEST_METHOD'] = '<unknown>';
}
if (isset($_SERVER['REQUEST_URI']))
	$str[] = sprintf('Requested URL: %s%s%s %s%s%s', $config->ansi->blue, $_SERVER['REQUEST_METHOD'], $config->ansi->clear, $config->ansi->green, $_SERVER['REQUEST_URI'], $config->ansi->clear);
if (function_exists('apache_request_headers')) {
	$headers = apache_request_headers();
	$newheaders=array();
	foreach ($headers as $key=>$val) {
		$newheaders[]="$key: $val";
	}
	$str[] = "Headers\n" . join("\n", $newheaders) . "\n";
}
if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
	$str[] = "HTTP_X_HTTP_METHOD_OVERRIDE: " . $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];

if (isset($_SERVER['QUERY_STRING']))
	$str[] = "Query string: " . $_SERVER['QUERY_STRING'];
$str[] = "\$_GET: " . print_r($_GET, true);
//$str[] = "\$_POST: " . print_r($_POST, true);
$str[] = "\$_REQUEST: " . print_r($_REQUEST, true);
$str[] = "Raw input: " . print_r($rawinput , true);

//$str[] = "_SERVER: " . print_r($_SERVER, true);
$MLOG->addDebug(join("\n", $str));

////////////////////////////////////////////////////////////////////////
//  /DEBUG STUFF
////////////////////////////////////////////////////////////////////////

try {

	$RestGameRoutes = Server::create('/rest/games', new \DiplomacyEngineRestApi\v1\Game)
		->addGetRoute('', 'doGetGames')
		;

	$RestMatchRoutes = Server::create('/rest/matches/', new \DiplomacyEngineRestApi\v1\Match)
		->addPostRoute('', 'doCreateMatch')
		->addGetRoute( '', 'doGetMatches')
		->addGetRoute( '([0-9]+)', 'doGetEmpires')
		->addPostRoute('([0-9]+)/empires/([0-9]+)/orders', 'doAddOrder')
		->addPostRoute( '([0-9]+)/empires/([0-9]+)/orders/validate', 'doValidate')
		->addGetRoute( '([0-9]+)/empires/([0-9]+)/territories', 'doGetEmpireTerritoryMap')
		;

		;

	// RPC routes

	$RpcMatchRoutes = Server::create('/rpc/matches/', new \DiplomacyEngineRestApi\v1\Match)
		->addGetRoute('([0-9]+)/resolve', 'doResolve')
		;



	// Not 100% sure I need this..
	$miscRoutes = Server::create('/', new \DiplomacyEngineRestApi\v1\RouteHandler)
		->addOptionsRoute('ping', function () { return; })
		->addOptionsRoute('(.*)', function ($p1=null) { return; })
		;

	$routes = array($RestGameRoutes, $RestMatchRoutes, $RpcMatchRoutes, $miscRoutes);
	// $routes = array($RestGameRoutes);
	foreach ($routes as $r) {
		$r->setExceptionHandler($exHandlerFactory($config, $r));
		$r->setDebugMode($config->host->mode==='dev');
		$r->setFallbackMethod('defaultRoute');
		if ($config->host->INTERFACE_TYPE === 'web') {
			//$MLOG->debug("Running ". get_class($r) ."");
			$r->run(); // If it's CLI, we're simulating and don't want this
		}
	}

//} catch (\Propel\Runtime\Exception\PropelException $e) {
//	// Do not send propel exceptions to client
//	$MLOG->err("Propel exception! $e\n". $e->getTraceAsString());
//	throw new \Exception('An error has occured.');
} catch (\Exception $e) {
	$req = '';
	if (isset($_SERVER['REQUEST_URI']))
		$req = $_SERVER['REQUEST_URI'];

	$MLOG->debug("Exception thrown for request: $req.  $e");
	throw $e;
}


//print_r($RestGameRoutes->describe());
//print $RestGameRoutes->simulateCall('/rest/games', 'GET');
//print $RestMatchRoutes->simulateCall('/rest/matches', 'GET');
//$RestGameRoutes->simulateCall(' /rest/games/', 'OPTIONS');
//$uRoutes->simulateCall('/user/1/order/createNewMembershipOrder?memtype=attendant', 'get');
//$uRoutes->simulateCall('/user/lookUp', 'get');
//$rpcRoutes->simulateCall('/rpc/user/addCredentials', 'post');
//$rpcRoutes->simulateCall('/rpc/user/me', 'get');
//$uRoutes->simulateCall('/user/1/order/226/mo?merch=Donation&opts=%7B%22opts%22:%7B%22price%22:5%7D%7D&quantity=1', 'post');
//print "Description:\n"; print_r($RpcAdminRoutes->describe());
//$RpcAdminRoutes->simulateCall('/rpc/admin/members/2014', 'get');
//$RpcAdminRoutes->simulateCall('/rpc/admin/itemStats/2014', 'get');
//$RpcAdminRoutes->simulateCall('/rpc/admin/paymentMethodStats/2014', 'get');
//$RpcAdminRoutes->simulateCall('/rpc/admin/usedCoupons/2', 'get');
//$RpcAdminRoutes->simulateCall('/rpc/admin/dataMembership', 'get');

// vim: sw=3 sts=3 ts=3 noet :
