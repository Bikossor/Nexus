<?php
spl_autoload_register(function ($name) {
	$path_core = sprintf('./core/libraries/%s.php', $name);

	if (file_exists($path_core)) {
		include $path_core;
	} else {
		throw new Exception(sprintf("Files for class '%s' not found!", $name));
	}
});
