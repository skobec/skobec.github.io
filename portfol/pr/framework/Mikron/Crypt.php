<?php

class Mikron_Crypt {

    private static $secret_key = '011571f4979746b7b30a56281cebd035';

    static function encrypt($value, $secret_key = null) {   
        $secret_key = $secret_key ?: self::$secret_key;
        $value = json_encode($value);
        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $secret_key, $value, 
                    MCRYPT_MODE_ECB, 
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256, 
                            MCRYPT_MODE_ECB
                        ), 
                        MCRYPT_RAND)
                    )
                ), "\0"
            );
    }

    static function decrypt($value, $secret_key = null) {
        $secret_key = $secret_key ?: self::$secret_key;
        $out = rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256, 
                $secret_key, 
                base64_decode($value), 
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ), 
                    MCRYPT_RAND
                )
            ), "\0"
        );
        return json_decode($out);
    }

}