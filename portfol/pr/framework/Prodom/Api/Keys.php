<?php

    class Prodom_Api_Keys {

        private $keys = null;

        public function getKeyName($key) {
            if (!is_array($this->keys)) {
                return null;
            }
            $keys = $this->keys['keys'];
            if (!array_key_exists($key, $keys)) {
                return null;
            }
            return $keys[$key];
        }

        public function readFromIniFile($filePath) {
            // чтение файла с ключами
            if (!file_exists($filePath)) {
                throw new Exception('Не найден файл с API-ключами', Prodom_Api_Server::ERR_KEY_MANAGER_INTERNAL_ERROR);
            }
            $keys = parse_ini_file($filePath, true);
            if (!is_array($keys)) {
                throw new Exception('Не удалось прочитать файл с API-ключами', Prodom_Api_Server::ERR_KEY_MANAGER_INTERNAL_ERROR);
            }
            if (!array_key_exists('keys', $keys)) {
                throw new Exception('Не найдена секция [keys] в файле с API-ключами', Prodom_Api_Server::ERR_KEY_MANAGER_INTERNAL_ERROR);
            }
            $this->keys = $keys;
        }

        /**
         * Метод проверки наличия прав на указанный метод
         * 
         * @param string $key
         * @param string $gateway
         * @param string $method
         * 
         * @return string
         */
        public function check($key, $gateway, $method) {
            if ($method == '__getFunctions') {return true;}
            if (!is_array($this->keys)) {throw new Exception('ERR_KEY_MANAGER_NOT_LOADED', Prodom_Api_Server::ERR_KEY_MANAGER_NOT_LOADED);}
            $keys = $this->keys['keys'];
            if (!array_key_exists($key, $keys)) {throw new Exception('ERR_INVALID_KEY', Prodom_Api_Server::ERR_INVALID_KEY);}
            $projectCode = $keys[$key];
            if (!array_key_exists($projectCode, $this->keys)) {throw new Exception('ERR_KEY_NOT_PERMITTED', Prodom_Api_Server::ERR_KEY_NOT_PERMITTED);}
            $gateways = $this->keys[$projectCode];
            if (!is_array($gateways)) {throw new Exception('ERR_KEY_RESTICTS_NOT_FOUND', Prodom_Api_Server::ERR_KEY_RESTICTS_NOT_FOUND);}
            if (!array_key_exists($gateway, $gateways)) {throw new Exception('ERR_SERVICE_NOT_PERMITTED', Prodom_Api_Server::ERR_SERVICE_NOT_PERMITTED);}
            $methods = trim(str_replace(';', ',', $gateways[$gateway]));
            $methods = str_replace(' ', null, $gateways[$gateway]);
            if ($methods == '*') {return true;}
            $methods = explode(',', $methods);
            if (in_array($method, $methods)) {return true;}
            throw new Exception('ERR_METHOD_NOT_PERMITTED', Prodom_Api_Server::ERR_METHOD_NOT_PERMITTED);
        }

    }
