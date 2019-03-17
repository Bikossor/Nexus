<?php
    class Formatter {
        private $config;

        public function __constructor($config) {
            $this->config = $config;
        }

        public static function formatFileSize($_number, $_base = 1024) {
            $class = min((int)log($_number, $_base) , count($this->config['si_prefix']) - 1);
            return sprintf('%1.2f %s', $_number / pow($_base, $class), $this->config['si_prefix'][$class]);
        }

        public static function formatFileExtension($fileExtension) {
            return strtoupper($fileExtension);
        }
    }
?>