<?php
    include 'Formatter.php';

    class Nexus
    {
        public $files;
        private $config;
        private $dir;
        private $dirCache = 'cache';
        private static $errOpenDir = "[%s]: Unable to open directory '%s' (%s)";
        private static $errWriteDir = "[%s]: Unable to write in directory '%s' (%s)";
        private static $errCloseDir = "[%s]: Unable to close directory '%s' (%s)";
        private static $errCreateDir = "[%s]: Unable to create directory '%s' (%s)";
        private static $errParseConfig = "[%s]: Unable to parse config file (%s)";
        private static $errNoPerms = "[%s]: Not enough permissions! Current permissions: %s";
        private static $errDirNotFound = "[%s]: Unable to find directory '%s' (%s)";
        private static $errNoFiles = "[%s]: Unable to find files";

        private $formatter;

        public function __construct($_dir) {
            $formatter = new Formatter();

            print_r($formatter);

            if(!file_exists($_dir) && !is_dir($_dir)) {
                throw new Exception(sprintf($this::$errDirNotFound, __CLASS__, $_dir, error_get_last()['message']));
            }

            $perms = (int)decoct(fileperms($_dir) & 0777);
            if($perms < 755) {
                throw new Exception(sprintf($this::$errNoPerms, __CLASS__, $perms));
            }

            $this->dir = $_dir;
            $this->files = array();
        }

        public function setConfig($_config) {
            if(!($this->config = parse_ini_file($this->dir . DIRECTORY_SEPARATOR . $_config))) {
                throw new Exception(sprintf($this::$errParseConfig, __CLASS__, error_get_last()['message']));
            }
        }

        

        public function getFiles($_path = '.') {
            if(!file_exists($_path) && !is_dir($_path)) {
                throw new Exception(sprintf($this::$errDirNotFound, __CLASS__, $_path, error_get_last()['message']));
            }

            $result = array();
            $handler = opendir($_path);

            if(!$handler) {
                throw new Exception(sprintf($this::$errOpenDir, __CLASS__, $_path, error_get_last()['message']));
            }

            while(false !== ($file = readdir($handler))) {
                if(!in_array($file, $this->config['blacklist'])) {
                    if(is_dir($_path . DIRECTORY_SEPARATOR . $file)) {
                        $result[] = $this->getFiles($_path . DIRECTORY_SEPARATOR . $file);
                    }
                    else {
                        $result[] = array_merge(stat($_path . DIRECTORY_SEPARATOR . $file), pathinfo($_path . DIRECTORY_SEPARATOR . $file));
                    }
                }
            }

            return $result;
        }

        public function saveCache($_key) {
            $this->createDirectory($this->dirCache);
            $path = $this->dir . DIRECTORY_SEPARATOR . $this->dirCache;
            $handler = fopen($path . DIRECTORY_SEPARATOR . md5($_key), "w");

            if(!$handler) {
                throw new Exception(sprintf($this::$errOpenDir, __CLASS__, $path, error_get_last()['message']));
            }
            if(!fwrite($handler, serialize($this->files))) {
                throw new Exception(sprintf($this::$errWriteDir, __CLASS__, $path, error_get_last()['message']));
            }
            if(!fclose($handler)) {
                throw new Exception(sprintf($this::$errCloseDir, __CLASS__, $path, error_get_last()['message']));
            }
        }

        public function loadCache($_key) {
            $this->createDirectory($this->dirCache);
            $path = $this->dir . DIRECTORY_SEPARATOR . $this->dirCache . DIRECTORY_SEPARATOR . md5($_key);

            if(!file_exists($path)) {
                return false;
            }

            $handler = fopen($path, 'r');

            if(!$handler) {
                throw new Exception(sprintf($this::$errOpenDir, __CLASS__, $path, error_get_last()['message']));
            }
            if(time() < (filemtime($path) + $this->config['cache_expire'])) {
                return unserialize(fread($handler, filesize($path)));
            }

            fclose($handler);
        }
        
        public function createDirectory($_dir) {
            if(!file_exists($_dir) && !is_dir($_dir)) {
                if(!mkdir($_dir)) {
                    throw new Exception(sprintf($this::$errCreateDir, __CLASS__, $_dir, error_get_last()['message']));
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
                        echo "<tr><td><a href=",
                        $_array[$key]['dirname'],
                        DIRECTORY_SEPARATOR,
                        $_array[$key]['basename'],
                        ">",
                        $_array[$key]['basename'],
                        "</a></td><td sorttable_customkey=\"",
                        $_array[$key]['size'],
                        "\">",
                        $this->formatter::formatFileSize($_array[$key]['size']),
                        "</td><td>",
                        $this->formatter::formatFileExtension($_array[$key]['extension']),
                        "</td><td sorttable_customkey=\"",
                        $_array[$key]['mtime'],
                        "\">",
                        date($this->config['date_format'],
                        $_array[$key]['mtime']),
                        "</td><td sorttable_customkey=\"",
                        $_array[$key]['ctime'],
                        "\">",
                        date($this->config['date_format'],
                        $_array[$key]['ctime']),
                        "</td></tr>";
                    }
                }
            }
            else {
                throw new Exception(sprintf($this::$errNoFiles, __CLASS__));
            }
        }
    }
?>
