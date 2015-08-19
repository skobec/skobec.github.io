<?php

    class Prodom_Api_Services {

        private static $services = array();
        private static $local_services = array();

        /**
         * Стандартный механизм получения и кеширования прокси
         * 
         * @param string $hostName
         * @param string $serviceName
         * @param string $siteApiKey
         * 
         * @return Prodom_Api_Client
         */
        protected static function getProxy($hostName, $serviceName, $siteApiKey) {
            if (!array_key_exists($serviceName, self::$services)) {
                self::$services[$serviceName] = new Prodom_Api_Client($hostName, $serviceName);
                self::$services[$serviceName]->setApiKey($siteApiKey);
            }
            return self::$services[$serviceName];
        }

        /**
         * Стандартный механизм получения и кеширования прокси
         * 
         * @param string $serviceDirectory
         * @param string $serviceName
         * 
         * @return Prodom_Api_Client
         */
        protected static function getLocalProxy($serviceDirectory, $serviceName) {
            if (!array_key_exists($serviceName, self::$local_services)) {
                self::$local_services[$serviceName] = new Prodom_Api_Client_Local($serviceDirectory, $serviceName);
            }
            return self::$local_services[$serviceName];
        }

    }
