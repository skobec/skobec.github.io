<?php


/**
* Криптография
*/
class Prodom_Cryptor {

    private $key = 'If you want to store the encrypted data in a database make...';
    private $td = null;
    private $iv = null;

    function __construct($key = null) {
        if(!is_null($key)) {
            $this->key = $key;
        }
        $this->td = mcrypt_module_open('des', '', 'ecb', '');
        $this->key = substr($this->key, 0, mcrypt_enc_get_key_size($this->td));
        $iv_size = mcrypt_enc_get_iv_size($this->td);
        $this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        if(mcrypt_generic_init($this->td, $this->key, $this->iv) == -1) {
            throw new Exception('Error init Encrypt-module.', 99);
        }
    }

    function Encrypt($plain_text, $key = null) {
        if(is_null($plain_text) || ($plain_text === '')) {
            return null;
        }
        $length = strlen($plain_text);
        /* Encrypt data */
        $c_t = mcrypt_generic($this->td, $plain_text);
        if($c_t === false) {
            return null;
        }
        return $length.'#'.base64_encode($c_t);
    }

    function Decrypt($crypted_buffer, $key = null) {
        $tp = explode('#', $crypted_buffer, 2);
        if(count($tp) < 2) {
            return false;
        }
        $length = (int)$tp[0];
        $crypted_buffer = base64_decode($tp[1]);
        $p_t = mdecrypt_generic($this->td, $crypted_buffer);
        if(strlen($p_t) != $length) {
            if(($length>0) && ($length<strlen($p_t))) {
                $p_t = substr($p_t, 0, $length);
            }
        }
        return $p_t;
    }

    function __destruct() {
        /* Clean up */
        mcrypt_generic_deinit($this->td);
        mcrypt_module_close($this->td);
    }

}
