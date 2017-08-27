<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>Nexus v0.4.0</title>
        <link rel="stylesheet" href="./core/css/stylesheet.css">
		<script type="text/javascript" src="./core/js/sorttable.js"></script>
	</head>
	<body>
		<table class="sortable">
			<tr><th>Filename</th><th>Size</th><th>Type</th><th>Modified at</th><th>Created at</th></tr>
			<?php
				include './core/libs/Nexus.php';

				try {
					$app = new Nexus(dirname(__FILE__));
					$app->setConfig('core' . DIRECTORY_SEPARATOR . 'config.ini');
					$app->files = $app->getFiles();

                    $cachedFiles = $app->loadCache(date('Y.m.d'));

					if(!$cachedFiles) {
						$app->saveCache(date('Y.m.d'));
					}
					else {
						$app->files = $cachedFiles;
					}

                    $app->getOutput($app->files);
				}
				catch (Exception $e) {
					echo '<tr><td colspan="5">', $e->getMessage(), '</td></tr>';
				}
			?>
		</table>
        <div class="footer"><a href="https://bikossor.de">&copy; André Lichtenthäler, 2017</a></div>
	</body>
</html>
