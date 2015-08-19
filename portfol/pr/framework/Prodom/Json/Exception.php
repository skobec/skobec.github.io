<?php

    class Prodom_Json_Exception extends Exception {

        /**
         * @var null|Exception
         */
        private $_previous = null;

        /**
         * Construct the exception
         *
         * @param  string $msg
         * @param  int $code
         * @param  Exception $previous
         * @return void
         */
        public function __construct($msg = '', $code = 0, Exception $previous = null) {
            if($code == 0) {
                $code = -32767;
            }
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                parent::__construct($msg, (int) $code);
                $this->_previous = $previous;
            } else {
                parent::__construct($msg, (int) $code, $previous);
            }
        }

        /**
         * Overloading
         *
         * For PHP < 5.3.0, provides access to the getPrevious() method.
         *
         * @param  string $method
         * @param  array $args
         * 
         * @return mixed
         */
        public function __call($method, array $args) {
            if ('getprevious' == strtolower($method)) {
                return $this->_getPrevious();
            }
            return null;
        }

        /**
         * String representation of the exception
         *
         * @return string
         */
        public function __toString() {
            return json_encode(array('status' => 'error', 'message' => $this->getMessage()));
        }

        /**
         * Returns previous Exception
         *
         * @return Exception|null
         */
        protected function _getPrevious() {
            return $this->_previous;
        }

    }
