<?php

class Prodom_ToolBox {

    /**
    * Возвращает читабельный номер заявки
    * @author sciner
    * @since 02.11.2011 12:20
    * 
    * @param string $number
    * @param string $external_number
    * 
    * @return string
    */
    public static function formatIssueNumber($number, $external_number = null) {
        $external_number = trim($external_number);
        if(strlen($external_number))
        {
            return "{$number} ({$external_number})";
        }
        else
        {
            return $number;
        }
    }

    /**
    * Разбирает строку состоящую из email адресов в массив
    * @author sciner
    * @since 2012-03-27
    * 
    * @param string $emails_string
    * 
    * @return array
    */
    public static function parseEmails($emails_string) {
        $e = str_replace(' ', ';', $emails_string);
        $e = str_replace(',', ';', $e);
        $e = str_replace(';;', ';', $e);
        $e = explode(';', $e);
        $ret = array();
        foreach($e as $email) {
            $email = trim($email);
            if(Validator::validateEmail($email)) {
                $ret[] = $email;
            }
        }
        $ret = array_unique($ret);
        return $ret;
    }

    public static function var_dump($var) {
        echo '<pre>';
        if(is_object($var) || is_array($var)) {
            print_r($var);
        } else {
            var_dump($var);
        }
        exit;
    }

    public static function http_response($url) 
    {
         $ch = curl_init(); 
         curl_setopt($ch, CURLOPT_URL, $url); 
         curl_setopt($ch, CURLOPT_HEADER, TRUE); 
         // curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
         // echo $url,'<br />';
         try {
             $body = explode("\r\n\r\n", @curl_exec($ch), 2);
             if(count($body) != 2) {$body = array(null, null);}
             $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
             curl_close($ch);
         }
         catch(Exception $ex)
         {
             // do nothing
         }
         return (object)array(
            'status' => (int)$httpCode,
            'head' => $body[0],
            'content' => $body[1],
         );
    }

    /**
    * Функция парсит содержимое CSV в массив строк
    * 
    * @param string $body
    * @param array $options
    * 
    * @return array
    */
    public static function parse_csv($body, $options = null) {
        $dir = sys_get_temp_dir().'/region/';
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir.time().'.csv';
        $h = fopen($file, 'w');
        fwrite($h, $body);
        fclose($h);
        try {
            $csv = new SplFileObject($file, 'r');
            $csv->setFlags(SplFileObject::READ_CSV);
            $csv->setCsvControl(';');
        } catch (Exception $ex) {
            throw new Exception("Error openning csv: ". $ex->getMessage(), 950);
        }
        $data = array();
        try {
            foreach($csv as $row) {
                $data[] = $row;
            }
        } catch(Exception $ex) {
            throw new Exception("Error parse csv: ". $ex->getMessage(), 950);
        }
        $csv = null;
        @unlink($file);
        return $data;
    }

    public static function str_getcsv2($input, $delimiter=';', $enclosure='"', $escape=null, $eol=null) {
        return self::parse_csv($input);
    }

    public static function getMounthName($time)
    {
        $months = array("Января","Февраля","Марта","Апреля","Мая","Июня","Июля","Августа","Сентября","Октября","Ноября","Декабря");
        $monthIndex = (int)date('m', $time);
        return $months[$monthIndex - 1];
    }

    /**
    * Проверка выставленности указанного битового флага
    * 
    * @param int $intval
    * @param int $flag
    * 
    * return bool
    */
    public static function checkBitFlag($intval, $flag) {
        return ($intval & $flag) == $flag;
    }

    /**
    * Post request
    * 
    * @param string $host
    * @param int $port
    * @param string $url
    * @param string|array $content_string
    * 
    * @return string
    */
    public static function __post_request($host, $port, $url, $post_data)  {
        if(!$port) {
			$port = 80;
		}
        if(is_array($post_data)) {
            $post_data = http_build_query($post_data);
        }
		$timeout = 30;
        $content_length = strlen($post_data);
        $request_body = "POST {$url} HTTP/1.0
Host: {$host}:{$port}
Content-Type: Application/Json-Rpc
Content-length: {$content_length}

{$post_data}";
$errno = 0; $errstr = null;
        $sh = @fsockopen($host, $port, $errno, $errstr, $timeout); // or die("can't open socket to {$host}: {$errno} {$errstr}");
        if($sh === false) {
            throw new Exception("Couldn't connect to server {$host}:{$port}");
        }
        fputs($sh, $request_body);
        $response = '';
        while(!feof($sh)) {
            $response .= fread($sh, 16384);
        }
        fclose($sh) or die("Can't close socket handle: {$php_errormsg}");
        $response = explode("\r\n\r\n", $response, 2);
        if(count($response) == 2) {
            return $response[1];
        }
        return null;
    }

    public static function object_to_array($object) {
        $arr = array();
        $arrObj = array();
        if($object) {
            $arrObj = is_object($object) ? get_object_vars($object) : $object;   
        }
        foreach ($arrObj as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? self::object_to_array($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    /**
     * Converts an associative array of arbitrary depth and dimension into JSON representation.
     *
     * NOTE: If you pass in a mixed associative and vector array, it will prefix each numerical
     * key with "key_". For example array("foo", "bar" => "baz") will be translated into
     * {'key_0': 'foo', 'bar': 'baz'} but array("foo", "bar") would be translated into [ 'foo', 'bar' ].
     *
     * @param $array The array to convert.
     * @return mixed The resulting JSON string, or false if the argument was not an array.
     * @author Andy Rusterholz
     */
    public static function array_to_json($array, $quote = "'") {
        if(!is_array($array)) {
            return false;
        }
        $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
        if($associative) {
            $construct = array();
            foreach($array as $key => $value)
            {
                // We first copy each key/value pair into a staging array,
                // formatting each key and value properly as we go.
                // Format the key:
                if(is_numeric($key)) {
                    $key = $key;
                }
                $key = $quote.addslashes($key).$quote;
                // Format the value:
                if(is_array($value)) {
                    $value = self::array_to_json( $value );
                } else if(!is_numeric($value) || is_string($value)) {
                    $value = $quote.addslashes($value).$quote;
                }
                // Add to staging array:
                $construct[] = "{$key}: {$value}";
            }
            // Then we collapse the staging array into the JSON form:
            $result = '{ ' . implode( ', ', $construct ) . ' }';

        } else { // If the array is a vector (not associative):
            $construct = array();
            foreach( $array as $value ) {
                // Format the value:
                if( is_array( $value )) {
                    $value = self::array_to_json( $value );
                } else if( !is_numeric( $value ) || is_string( $value ) ) {
                    $value = $quote.addslashes($value).$quote;
                }
                // Add to staging array:
                $construct[] = $value;
            }
            // Then we collapse the staging array into the JSON form:
            $result = '[ ' . implode( ', ', $construct ) . ' ]';
        }
        return $result;
    }

    /**
    * Возвращает морфологически корректное значение. Например morph(25, 'день', 'дня', 'дней')
    * @author sciner
    * @since 23-11-2011
    * 
    * @param int $n
    * @param string $f1
    * @param string $f2
    * @param string $f5
    * 
    * @return string
    */
    public static function morph($n, $f1, $f2, $f5) {
        $n = abs($n) % 100;
        $n1= $n % 10;
        if (($n>10 && $n<20) || $n==0) return $f5;
        if ($n1>1 && $n1<5) return $f2;
        if ($n1==1) return $f1;
        return $f5;
    }

    // Функция выяснения лежит ли точка в полигоне
    public static function inPolygon($poly_x, $poly_y, $x, $y) {
        // В основе алгоритма лежит идея подсчёта количества пересечений луча,
        // исходящего из данной точки в направлении горизонтальной оси,
        // со сторонами многоугольника.
        // Если оно чётное, точка не принадлежит многоугольнику. (c) Wikipedia
        $count = count($poly_x);
        $j = $count - 1;
        $c = 0;
        for ($i = 0; $i < $count; $i++) {
            if (((($poly_y[$i] <= $y) && ($y < $poly_y[$j])) || (($poly_y[$j] <= $y) && ($y < $poly_y[$i]))) &&
                    ($x > ($poly_x[$j] - $poly_x[$i]) * ($y - $poly_y[$i]) / ($poly_y[$j] - $poly_y[$i]) + $poly_x[$i])) {
                $c = !$c;
            }
            $j = $i;
        }
        return $c;
    }

    public static function GUID()
    {
         if(function_exists('com_create_guid') === true)
         {
             return trim(com_create_guid(), '{}');
         }
         return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public static function convertToExcellLetter($iCol)
    {
       $f = ($iCol < 26) ? null : chr(64+$iCol/26);
       return $f.chr(65+($iCol%26));
    }

    /**
     * Determine if supplied string is a valid GUID
     *
     * @param string $guid String to validate
     * @return boolean
     */
    public static function isValidGuid($guid)
    {
        return !empty($guid) && preg_match('/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i', $guid);
    }
    
    /**
    * Универсальный метод проверки наличия в массиве значений с указанными ключами(индексами)
    * 
    * @param array $array
    * @param array $keys
    */
    public static function arrayCheckKeys($array, $keys) 
    {
        if(!is_array($array) || !is_array($keys))
        {
            return false;
        }
        foreach($keys as $key)
        {
            if(!array_key_exists($key, $array))
            {
                return false;
            }
        }
        return true;
    }

    public static function punycode_to_unicode($input) {
        $prefix = 'xn--'; $safe_char = 0xFFFC;  $base = 36; $tmin = 1; $tmax = 26; $skew = 38; $damp = 700; $output_parts=array();
        $enco_parts=(array)explode('.',$input);  
        foreach ($enco_parts as $encoded) {    // loop through each part of a host domain,  ie. subdomain.subdomain.domain.tld
        if (strpos($encoded,$prefix)!==0 || strlen(trim(str_replace($prefix,'',$encoded)))==0) { $output_parts[]=$encoded; continue; }
        $is_first = true; $bias = 72; $idx = 0;  $char = 0x80; $decoded = array();    $output='';
        $delim_pos = strrpos($encoded, '-');
        if ($delim_pos > strlen($prefix)) { for ($k = strlen($prefix); $k < $delim_pos; ++$k) { $decoded[] = ord($encoded{$k}); } }
        $deco_len = count($decoded);
        $enco_len = strlen($encoded);
        for ($enco_idx = $delim_pos ? ($delim_pos + 1) : 0; $enco_idx < $enco_len; ++$deco_len) {        
            for ($old_idx = $idx, $w = 1, $k = $base; 1 ; $k += $base) {
                $cp = ord($encoded{$enco_idx++});
                        $digit = ($cp - 48 < 10) ? $cp - 22 : (($cp - 65 < 26) ? $cp - 65 : (($cp - 97 < 26) ? $cp - 97 : $base));
                            $idx += $digit * $w;
                            $t = ($k <= $bias) ? $tmin : (($k >= $bias + $tmax) ? $tmax : ($k - $bias));
                            if ($digit < $t) { break; }
                            $w = (int) ($w * ($base - $t));
                        }
            $delta = $idx - $old_idx;        
            $delta = intval($is_first ? ($delta / $damp) : ($delta / 2));
            $delta += intval($delta / ($deco_len + 1));
            for ($k = 0; $delta > (($base - $tmin) * $tmax) / 2; $k += $base) { $delta = intval($delta / ($base - $tmin)); }
            $bias = intval($k + ($base - $tmin + 1) * $delta / ($delta + $skew));
            $is_first = false;
            $char += (int) ($idx / ($deco_len + 1));
            $idx %= ($deco_len + 1);
            if ($deco_len > 0) { for ($i = $deco_len; $i > $idx; $i--) { $decoded[$i] = $decoded[($i - 1)]; } }
                        $decoded[$idx++] = $char;
                }
                foreach ($decoded as $k => $v) {
                        if ($v < 128) { $output .= chr($v); } // 7bit are transferred literally
            elseif ($v < (1 << 11)) { $output .= chr(192+($v >> 6)).chr(128+($v & 63)); } // 2 bytes
            elseif ($v < (1 << 16)) { $output .= chr(224+($v >> 12)).chr(128+(($v >> 6) & 63)).chr(128+($v & 63)); } // 3 bytes
            elseif ($v < (1 << 21)) { $output .= chr(240+($v >> 18)).chr(128+(($v >> 12) & 63)).chr(128+(($v >> 6) & 63)).chr(128+($v & 63)); } // 4 bytes
            else { $output .= $safe_char; } //  'Conversion from UCS-4 to UTF-8 failed: malformed input at byte '.$k
        }
        $output_parts[]=$output;        
        }  // $enco_parts loop
        return implode('.',$output_parts);
    }

        /**
        * возвращает человекопонятную строку прошедшего времени между двумя временными метками
        * 
        * @param int $startTime
        * @param int $stopTime
        * 
        * @author sciner
        * @since 22.11.2011
        * 
        * @return string
        */
        static public function whatTimeElapse($startTime, $stopTime)
        {
            $elapsed = $stopTime - $startTime;
            if($elapsed > 86400) { // больше суток
                $days = ceil($elapsed / 86400);
                return "{$days} ".self::morph($days, 'день', 'дня', 'дней');
            } elseif($elapsed < 60) { // меньше минуты
                return 'меньше минуты';
            } elseif($elapsed < 3600) { // меньше часа
                $minutes = ceil($elapsed / 60);
                return "{$minutes} ".self::morph($minutes, 'минута', 'минуты', 'минут');
            } else {
                $hours = ceil($elapsed / 3600);
                return "{$hours} ".self::morph($hours, 'час', 'часа', 'часов');                
            }
            return '3 часа';
        }


        /**
        * Возвращает дату в читабельном виде
        * 
        * @param int $date
        * @param boolean $showYear
        * @param boolean $short
        * @param boolean $showTime
        * @param boolean $showSeconds
        * @param boolean $smartYear
        */
        public static function dateFormat($date, $showYear = true, $short = false, $showTime = false, $showSeconds = false, $smartYear = false)
        {
            $months = array("Января","Февраля","Марта","Апреля","Мая","Июня","Июля","Августа","Сентября","Октября","Ноября","Декабря");
            $months2 = array("янв","фев","мар","апр","мая","июн","июл","авг","сен","окт","ноя","дек");
            if($short){$months=$months2;}
            $day = date('j', $date);
            $month = date('n', $date);
            $mth = $months[$month-1];
            $year = date('Y', $date);
            $ret = $day.' '.$mth;
            if($showYear)
            {
                if($smartYear)
                {
                    $currentYear = date('Y', time());
                    if($year != $currentYear)
                    {
                        $ret .= ' '.$year;
                    }
                }
                else
                {
                    $ret .= ' '.$year;
                }
            }
            if($showTime)
            {
                if($showSeconds)
                {
                    $ret .= ', '.date('H:i:s', $date);
                }
                else
                {
                    $ret .= ', '.date('H:i', $date);
                }
            }
            return $ret;
        }

    static function generateCode($length) {
       $chars = 'abcdefhknrstvwxyz23456789ABCDEFHKNRSTVWXYZ';
       $numChars = strlen($chars);
       $string = '';
       for ($i = 0; $i < $length; $i++) {
          $string .= substr($chars, rand(1, $numChars) - 1, 1);
       }
       return $string;
    }

}
