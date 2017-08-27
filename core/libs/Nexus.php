<?php
    class Nexus
    {
        public $files;
        private $config;
        private $dir;
        private $dirCache = 'cache';
        private $errOpenDir = "[%s]: Unable to open directory '%s' (%s)";
        private $errWriteDir = "[%s]: Unable to write in directory '%s' (%s)";
        private $errCloseDir = "[%s]: Unable to close directory '%s' (%s)";
        private $errCreateDir = "[%s]: Unable to create directory '%s' (%s)";
        private $errParseConfig = "[%s]: Unable to parse config file (%s)";
        private $errNoPerms = "[%s]: Not enough permissions! Current permissions: %s";
        private $errDirNotFound = "[%s]: Unable to find directory '%s' (%s)";
        private $errNoFiles = "[%s]: Unable to find files";

        public function __construct($_dir) {
            define('DS', DIRECTORY_SEPARATOR);
            define('CL', __CLASS__);

            if(!file_exists($_dir) && !is_dir($_dir)) {
                throw new Exception(sprintf($errDirNotFound, CL, $_dir, error_get_last()['message']));
            }

            $perms = (int)decoct(fileperms($_dir) & 0777);
            if($perms < 755) {
                throw new Exception(sprintf($errNoPerms, CL, $perms));
            }

            $this->dir = $_dir;
            $this->files = array();
        }

        public function setConfig($_config) {
            if(!($this->config = parse_ini_file($this->dir . DS . $_config))) {
                throw new Exception(sprintf($errParseConfig, CL, error_get_last()['message']));
            }
        }

        public function formatFileSize($_number, $_base = 1024) {
            $class = min((int)log($_number, $_base) , count($this->config['si_prefix']) - 1);
            return sprintf('%1.2f %s', $_number / pow($_base, $class), $this->config['si_prefix'][$class]);
        }

        public function getFiles($_path = '.') {
            if(!file_exists($_path) && !is_dir($_path)) {
                throw new Exception(sprintf($errDirNotFound, CL, $_path, error_get_last()['message']));
            }

            $result = array();
            $handler = opendir($_path);

            if(!$handler) {
                throw new Exception(sprintf($errOpenDir, CL, $_path, error_get_last()['message']));
            }

            while(false !== ($file = readdir($handler))) {
                if(!in_array($file, $this->config['blacklist'])) {
                    if(is_dir($_path . DS . $file)) {
                        $result[] = $this->getFiles($_path . DS . $file);
                    }
                    else {
                        $result[] = array_merge(stat($_path . DS . $file), pathinfo($_path . DS . $file));
                    }
                }
            }

            return $result;
        }

        public function saveCache($_key) {
            $this->createDirectory($this->dirCache);
            $path = $this->dir . DS . $this->dirCache;
            $handler = fopen($path . DS . md5($_key), "w");

            if(!$handler) {
                throw new Exception(sprintf($errOpenDir, CL, $path, error_get_last()['message']));
            }
            if(!fwrite($handler, serialize($this->files))) {
                throw new Exception(sprintf($errWriteDir, CL, $path, error_get_last()['message']));
            }
            if(!fclose($handler)) {
                throw new Exception(sprintf($errCloseDir, CL, $path, error_get_last()['message']));
            }
        }

        public function loadCache($_key) {
            $this->createDirectory($this->dirCache);
            $path = $this->dir . DS . $this->dirCache . DS . md5($_key);

            if(!file_exists($path)) {
                return false;
            }

            $handler = fopen($path, 'r');

            if(!$handler) {
                throw new Exception(sprintf($errOpenDir, CL, $path, error_get_last()['message']));
            }
            if(time() < (filemtime($path) + $this->config['cache_expire'])) {
                return unserialize(fread($handler, filesize($path)));
            }

            fclose($handler);
        }

        public function createDirectory($_dir) {
            if(!file_exists($_dir) && !is_dir($_dir)) {
                if(!mkdir($_dir)) {
                    throw new Exception(sprintf($errCreateDir, CL, $_dir, error_get_last()['message']));
                }
            }
        }

        public function getOutput($_array) {
            if(!empty($_array)) {
                foreach($_array as $key => $value) {
                    if(array_key_exists($key, $_array) && is_array($_array[$key][0])) {
                        $this->getOutput($_array[$key]);
                    }
                    else {
                        echo "<tr><td><a href=", $_array[$key]['dirname'], DS, $_array[$key]['basename'], ">", $_array[$key]['basename'], "</a></td><td sorttable_customkey=\"", $_array[$key]['size'], "\">", $this->formatFileSize($_array[$key]['size']), "</td><td>", strtoupper($_array[$key]['extension']), "</td><td sorttable_customkey=\"", $_array[$key]['mtime'], "\">", date($this->config['date_format'], $_array[$key]['mtime']), "</td><td sorttable_customkey=\"", $_array[$key]['ctime'], "\">", date($this->config['date_format'], $_array[$key]['ctime']), "</td></tr>";
                    }
                }
            }
            else {
                throw new Exception(sprintf($errNoFiles, CL));
            }
        }
    }
?>
