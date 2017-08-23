<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Nexus v0.3.0</title>
        <link rel="stylesheet" href="./core/css/stylesheet.css">
		<script type="text/javascript" src="./core/js/sorttable.js"></script>
		<script type="text/javascript" src="./core/js/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="./core/js/nexus.js"></script>
	</head>
	<body>
		<?php
            define('DS', DIRECTORY_SEPARATOR);
            define('HOME_DIR', dirname(__FILE__));

			$config = parse_ini_file(HOME_DIR . DS . 'core' . DS . 'config.ini');
			//$disk_free = disk_free_space(HOME_DIR);
			//$disk_total = disk_total_space(HOME_DIR);
			$files = array();

			function formatFileSize($number, $base = 1024) {
				global $config;

				$class = min((int)log($number, $base) , count($config['si_prefix']) - 1);
				return sprintf('%1.2f %s', $number / pow($base, $class), $config['si_prefix'][$class]);
			}

			function getFiles($path = '.') {
				global $config, $files;
				$res = array();
				$dh = @opendir($path);

				while(false !== ($file = readdir($dh))) {
					if(!in_array($file, $config['blacklist'])) {
						if(is_dir($path . DS . $file)) {
			                $res[] = getFiles($path . DS . $file);
			            }
						else {
			                $res[] = array('file' => $path . DS . $file, stat($path . DS . $file), pathinfo($path . DS . $file));
			            }
					}
				}
				closedir($dh);
				return $res;
			}

			function saveCache($key, $data) {
                createCacheDir();

                $handle = fopen(HOME_DIR . DS . 'cache' . DS . md5($key), "w");
                fwrite($handle, serialize($data));
                fclose($handle);
			}

			function loadCache($key, $expire) {
                createCacheDir();

				$path = HOME_DIR . DS . 'cache' . DS . md5($key);

				if(!file_exists($path)) {
                    return false;
                }

                $handle = fopen($path, 'r');

				if(time() < (filemtime($path) + $expire)) {
                    return unserialize(fread($handle, filesize($path)));
                }

                fclose($handle);
                return false;
			}

            function createCacheDir() {
                if(!file_exists('cache') && !is_dir('cache')) {
                    mkdir('cache');
                }
            }

			$cachedFiles = loadCache(date('Y.m.d'), $config['cache_expire']);

            if(!$cachedFiles) {
                $files = getFiles();
				saveCache(date('Y.m.d'), $files);
			}
			else {
				$files = $cachedFiles;
			}
		?>
		<div class="modal">
			<img class="lb_image"/>
			<video id="video" class="lb_video" controls></video>
		</div>
		<!--
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
			<input type="text" name="q" required/>
			<input type="submit" value="Suchen"/>
		</form>
        -->
		<table class="sortable">
			<tr><th>Dateiname</th><th>Größe</th><th>Typ</th><th>Änderungsdatum</th><th>Erstelldatum</th></tr>
			<?php
				if(!empty($files)) {
					foreach ($files as $file) {
						if(is_array($file)) {
                            foreach ($file as $item) {
								echo '<tr><td><a href="', $item['file'], '" class="', strtolower($item[1]['extension']), '">', $item['file'], '</a></td><td sorttable_customkey="', $item[0]['size'], '">', formatFileSize($item[0]['size']), '</td><td>', strtoupper($item[1]['extension']), '</td><td>', date($config['date_format'], $item[0]['mtime']), '</td><td>', date($config['date_format'], $item[0]['ctime']), '</td></tr>';
							}
						}
					}
				}
				else {
					echo '<tr>', '<td rowspan="2">', 'No files found!', '</td>', '</tr>';
				}
			?>
		</table>
	</body>
</html>
