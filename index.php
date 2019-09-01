<?php
try {
	require_once './novus.config.php';
	require_once './core/Bootstrapper.php';

	$app = new Application();
	$app->run();
} catch (Exception $e) {
	echo $e;
}
