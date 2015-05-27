<?php

/**
 * Error handler factory
 * Done like this so I can get the $server (when it exists) into the handler,
 * otherwise I can't really send a response or anything.
 * Essentially this allows me to get $this (as $server) into the handler, and
 * replicate it's own exception handler, but with a few tweaks.
**/
$exHandlerFactory = function ($config, $server) use ($MLOG) {
	return function ($pException) use ($config, $server, $MLOG) {
		$message = $pException->getMessage();
		if (is_object($message) && $message->xdebug_message) $message = $message->xdebug_message;

		$msg = array('error' => get_class($pException), 'message' => $message);

		if ($server->getDebugMode()) {
			$msg['file'] = $pException->getFile();
			$msg['line'] = $pException->getLine();
			$msg['trace'] = $pException->getTraceAsString();
		}

		if (!$server->getClient()) throw new \Exception('Client not found in ServerController');

		$MLOG->warn("REST Server threw a ". get_class($pException) . " exception: ". $pException->getMessage() . "\n". $pException->getTraceAsString());

		if ($pException instanceof \Propel\Runtime\Exception\PropelException && $config->host->mode === 'production') {
			// Clear the message
			$pException->setMessage('Database error');
		}

		if ($pException instanceof \MayoFoundation\AuthException)
			return $server->getClient()->sendResponse('401', $msg);
		elseif ($pException instanceof \MayoFoundation\InvalidIdentifyerException)
			return $server->getClient()->sendResponse('404', $msg);
		else
			return $server->getClient()->sendResponse('500', $msg);
	};
};

// vim: sw=3 sts=3 ts=3 noet :
