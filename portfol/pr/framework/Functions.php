<?php

class Functions {

	static function makeTag($name, $attr_list, $text) {
        $attr = null;
        foreach($attr_list as $key => $value) {
            $attr .= $key . '="' . $value.'" ';
        }
        return "<{$name} {$attr}>{$text}</{$name}>";
	}

	static function includeEx($file_path) {
		try {
			include $file_path;
		} catch(Exception $ex) {
			echo $ex->getMessage();
		}
	}
	
	/**
	* Находится ли система в режиме только для чтения
	* @author sciner
	* @since 2014-12-05
	*/
	static function isReadOnlyMode() {
		if(defined('READ_ONLY_MODE')) {
			return READ_ONLY_MODE;
		}
		return false;
	}

	static function colorBrightnes($color, $amount) {
		$color = str_replace('#', null, $color);
	    $rgb = hexdec($color); // convert color to decimal value
	    //extract color values:
	    $red = $rgb >> 16;
	    $green = ($rgb >> 8) & 0xFF;
	    $blue = $rgb & 0xFF;
	    //manipulate and convert back to hexadecimal
	    return '#'.dechex(($red + $amount) << 16 | ($green + $amount) << 8 | ($blue + $amount));
	}

	/**
	* Перенаправление на другую страницу
	* @author sciner
	* @since 2014-07-15
	* 
	* @param string $url
	* @param bool $rewrite
	* @param int $code
	* @param bool $exit
	* @param array $options
	* 
	* @return bool
	*/
	static function redirect($url, $rewrite = true, $code = 302, $exit = true, $options = null) {
        header("location: {$url}", (int)$rewrite, (int)$code);
        if($exit) {
        	exit;
		}
	}

    static function str_replace_dash($number) {
        $number = $number == 0 ? '-' : Functions::number_format($number, 2, ',', ' ') ;
        return $number;
    }

	static function parseFloat($var, $default = null) {
		$var = str_replace(',', '.', trim($var));
		$var = is_numeric($var) ? (float)$var : $default;
		return $var;
	}

	/**
	* @author sciner
	* 
	* @param float $number
	* @param int $decimals
	* @param string $dec_point
	* @param string $thousands_sep
	* 
	* @return string
	*/
    static function number_format($number, $decimals, $dec_point, $thousands_sep) {
		$number = (float)str_replace(',', '.', $number);
		return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

	/**
	* @author sciner
	* @since 2014-07-11
	* 
	* @param int $int_version
	* @param int $max_length
	* 
	* @return string
	*/
	public static function makeVersionStringFromInt($int_version, $max_length = 5) {
		$vs = str_pad($int_version, $max_length, '0', STR_PAD_LEFT);
		if($vs[0] == '0') {
			$vs[0] = '1';
		}
		$vs = array($vs[0], substr($vs, 1, 1), substr($vs, -$max_length + 2));
		$vs = implode('.', $vs);
		return $vs;
	}

    /**
    * Получить геокоординаты
    * 
    * @param string $addressString
    */
    public static function getAddressPosition($addressString) {
        $addressString = urlencode(trim($addressString));
        if(!$addressString) {
            return null;
        }
        $apikey = 'ADmQ8E4BAAAAUsv_fgIASzd_9CcFKeAPUXP_sojgQwkXw-gAAAAAAAAAAACQP-lbXi7DaS0GEfehVS6uwwb-4Q==~AAEc704BAAAA16a1WgIAnTt9t6OtJv7HA_UPACOT8T1FmfUAAAAAAAAAAAAbC43YS4hiLRmraV2LxXDeSAZWwg==~ABQc704BAAAAGh9pQgIAJ_DcItzYRukovWkmIVs7bdv1PqsAAAAAAAAAAAB_Ce3TMgl8F9-KPaDocpSS1Y5zUQ==';
        try {
            $url = "http://geocode-maps.yandex.ru/1.x/?format=json&geocode={$addressString}&key={$apikey}";
                 // http://api-maps.yandex.ru/1.1.21/xml/Geocoder/Geocoder.xml?key={$apikey}&geocode={$addressString}&ll=49.149381%2C55.793905&spn=0.007403%2C0.00272&results=9&skip=0&callback=jsonp1365671100340
            $o = json_decode(file_get_contents($url));
            if(!count($o->response->GeoObjectCollection->featureMember)) {
                return null;
            }
            $points = explode(' ', $o->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
            if(count($points) == 2) {
                return $points;
            }
        } catch(Exception $ex) {
            // do nothing ...
        }
        return null;
    }

    /**
    * Генерация случайного пароля
    * @author sciner
    * 
    * @param int $PassLength длина пароля
    * 
    * @return string
    */
    public static function generatePassword($PassLength = 8) {
        $PassCase = 'zyxwvutskjhfedcbaABCDEFGHKLMNPQRSTUVWXYZ2345678';
        $pwd = ''; //сам пароль
        for ($i = 0; $i < $PassLength; $i++) {
            $pwd .= $PassCase[rand(0, 46)];
        }
        return $pwd;
    }

    /**
    * Возвращает true если это Ajax-запрос
    */
    public static function isAjaxRequest() {
        $ret = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        	&& (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        // @sciner
		if(!$ret) {
        	if(isset($_SERVER['HTTP_ACCEPT'])) {
        		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$ret = strpos($_SERVER['HTTP_ACCEPT'], 'application/x-ms-application') !== false;
				}
			}
		}
		return $ret;
    }

    public static function onlyDigits($string) {
        $p = preg_replace("/[^0-9]/", null, $string);
        return $p;
    }

	public static function cast($item, $typeName) {
        if(!is_array($item) && !is_object($item)) {
            throw new Exception('Function::cast incorrected input param $item');
        }
        $resp = null;
        $item = json_decode(json_encode($item));
		$docComment = Prodom_Reflection::getDataTypeDoc($typeName);
        foreach($docComment as $name => $properties) {
            $resp = new $name();
            foreach($properties as $propertyName => $property) {
                $propertyType = $property['type'];
                if(is_array($propertyType)) {
                    $ac = array_keys($propertyType);
                    $propertyType = $ac[0];
                    if(isset($item->$propertyName)) {
                        if(strpos($propertyType, '[]') === false) {
					        $resp->$propertyName = self::cast($item->$propertyName, $propertyType);
                        } else {
                            $resp->$propertyName = self::castAll($item->$propertyName, str_replace('[]', null, $propertyType));
                        }
                    }
                } elseif(isset($item->$propertyName)) {
                    $resp->$propertyName = $item->$propertyName;
                }
            }
        }
        return $resp;
	}

	public static function castAll($items, $typeName) {
		if(!is_array($items)) {
			throw new Exception('Error in '.__CLASS__.'::'.__FUNCTION__);
		}
		foreach($items as $key => $item) {
			$items[$key] = self::cast($item, $typeName);
		}
		return $items;
	}

    public static function arrayPathValue($array, $path, $default = null) {
        $value = $array;
        $p = explode('/', $path);
        foreach($p as $key) {
            if(!array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }
        return $value;
    }

    /**
    * Создание корректной ссылки с учетом текущего адреса
    * 
    * @param string $adduri
    * @param boolean $finnally
    * @param string $startUri
    * 
    * @return string
    */
    public static function makePath($adduri, $finnally = false, $startUri = null) {        
        if(!$finnally) {
            $adduri = str_replace('&amp;', chr(2), $adduri);
            $adduri = str_replace('&', chr(2), $adduri);
            $adduri = str_replace(chr(2), '&amp;', $adduri);
            $adduri = str_replace('[]', '%5B%5D', $adduri);
        }
        $uri = $adduri;
        $path = $startUri;
        if(!$path) {
            $path = $_SERVER['REQUEST_URI'];
            $path = str_replace('&amp;', chr(2), $path);
            $path = str_replace('&', chr(2), $path);
            $path = str_replace(chr(2), '&amp;', $path);
        }
        $added_params = self::getUriParams($adduri);
        $start_params = self::getUriParams($path);
        foreach($start_params as $key => $s) {
            if(array_key_exists($key, $added_params)) {
                $path = str_replace("{$key}={$s}", null, $path);
            }
        }
        $tp = explode('#', $adduri, 2);
        if(count($tp) == 2) {
            $path = str_replace($tp[0], null, $path);
        }
        $pos = strpos($path, '?');
        if($pos === false) {
            switch(substr($uri, 0, 1)) {
                case '?':
                    $uri = $path.$uri;
                    break;
                case '&':
                    $uri = $path.'?'.$uri;
                    break;
            }
        }
        else {
            switch(substr($uri, 0, 1)) {
                case '?':
                    $uri = substr($path, 0, $pos).$uri;
                    break;
                case '&':
                    $uri = $path.$uri;
                    break;
            }
        }
        /*** Здесь нужно искать и удалять задвоенные параметры в $uri ***/
        $uri_info  = parse_url($uri);
        $query = $uri_info['query'];
        if(strlen($query) > 0) {
            $a = array();
            $a_values = array();
            $attrs = explode('&amp;', $query);
            if(count($attrs) > 0) {
                $array = array();
                foreach($attrs as $attr) {
                    $v = explode('=', $attr, 2);
                    $key = $v[0];
                    $value = (count($v) == 2) ? $v[1] : null;
                    if(strpos($key, '%5B%5D') == false) {
                        $a[$key] = $value;
                    } else {
                        if(!array_key_exists($key, $array)) {
                            $array[$key] = array();
                        }
                        $array[$key][] = $value;
                    }
                }
                foreach($a as $key => $value) {
                    $a_values[] = $key.'='.$value;
                }
                foreach($array as $key => $values) {
                    foreach($values as $value) {
                        $a_values[] = $key.'='.$value;
                    }
                }
                $uri = str_replace($query, implode('&', $a_values), $uri);
            }
        }
        $uri = str_replace('?=&amp;', '?', $uri);
        $uri = str_replace('&=', null, $uri);
        $uri = str_replace('&amp;=', null, $uri);
        return $uri;
    }
    
    public static function getUriParams($uri) {
        $ret = Array();
        $uri = str_replace('&amp;', '&', $uri);
        $p = explode('?', $uri, 2);
        if(count($p) > 1) {
            $uri = $p[1];
        }
        $p = explode('#', $uri, 2);
        if(count($p) > 1) {
            $uri = $p[0];
        }
        $ue = explode('&', $uri);
        foreach($ue as $u) {
            $u3 = explode('=', $u, 2);
            if(count($u3) > 1) {
                $key = $u3[0];
                $ret[$key] = $u3[1];
            }
        }
        return $ret;
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
    public static function dateFormat($date, $showYear = true, $short = false, $showTime = false, $showSeconds = false, $smartYear = false, $showDay = true) {
        if(!$date) {
            return '<span class="font-no-data" >Нет данных </span>';
        }
        $months = array("Января","Февраля","Марта","Апреля","Мая","Июня","Июля","Августа","Сентября","Октября","Ноября","Декабря");
        $months2 = array("янв","фев","мар","апр","мая","июн","июл","авг","сен","окт","ноя","дек");
        $months3 = array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
        if($short){
            $months=$months2;
        }
        $day = date('j', $date);
        $month = date('n', $date);
        $mth = $months[$month-1];
        $year = date('Y', $date);
		if($showDay) {
			$ret = $day.' '.$mth;	
		} else {
			$mth2 = $months3[$month-1];
			$ret = $mth2;
		}
        if($showYear) {
            if($smartYear) {
                $currentYear = date('Y', time());
                if($year != $currentYear) {
                    $ret .= ' '.$year;
                }
            }
            else {
                $ret .= ' '.$year;
            }
        }
        if($showTime) {
            if($showSeconds) {
                $ret .= ', '.date('H:i:s', $date);
            }
            else {
                $ret .= ', '.date('H:i', $date);
            }
        }
        return $ret;
    }

    /**
     * Возвращает название месяца по его порядковому номеру
     *
     * @param $id
     * @param bool $nominative если выставить в false, то метод вернет месяц в другом падеже.
     * @return string
     */
    public static function getMonthName($id, $nominative = true)
    {
        $months = array(
            1  => ['Январь', 'Январе'],
            2  => ['Февраль', 'Феврале'],
            3  => ['Март', 'Марте'],
            4  => ['Апрель', 'Апреле'],
            5  => ['Май', 'Мае'],
            6  => ['Июнь', 'Июне'],
            7  => ['Июль', 'Июле'],
            8  => ['Август', 'Августе'],
            9  => ['Сентябрь', 'Сентябре'],
            10  => ['Октябрь', 'Октябре'],
            11  => ['Ноябрь', 'Ноябре'],
            12  => ['Декабрь', 'Декабре'],
        );

        if (isset($months[$id])) {
            return $nominative ? $months[$id][0] : $months[$id][1];
        }
        throw new \Exception("Незвестный идентификатор месяца " . $id, 950);
    }

    /**
    * Создание превью картинки
    * @author Notfoolen
    * @since 14.08.2013
    * 
    * @param string $filename Название картинки
    * @param string $file_output Куда и с каким названием сохранить
    * @param string $o_width Новая ширина картинки
    * @param string $o_height Новая высота картинки
    */
    public static function imageCut($filename, $file_output, $o_width, $o_height) {
        list($width, $height, $type) = getimagesize($filename);
        $new_width  = $o_width;
        $new_height = $o_height;
        $ratio = $new_width / $new_height;
        $types = array('','gif','jpeg','png');
        $ext = $types[$type];
        
        if(strstr($filename, '.png') || strstr($filename, '.PNG')) {
            $image = imagecreatefrompng($filename);
        } elseif(strstr($filename, '.jpg') || strstr($filename, '.JPG') || strstr($filename, '.jpeg') || strstr($filename, '.JPEG')) {
            $image = imagecreatefromjpeg($filename);
        } elseif(strstr($filename, '.gif') || strstr($filename, '.GIF')) {
            $image = imagecreatefromgif($filename);
        }
        $image_p = imagecreatetruecolor($new_width, $new_height) or die('Cannot initialize new GD image stream.');
        if ($width / $height > $ratio) {
            $tmp_width = $height * $ratio;
            $x = ($width - $tmp_width) / 2;
            imagecopyresampled($image_p, $image, 0, 0, $x, 0, $new_width, $new_height, $tmp_width, $height);
        } else {
            $tmp_height = $width * 1 / $ratio;
            $y = ($height - $tmp_height) / 2;
            imagecopyresampled($image_p, $image, 0, 0, 0, $y, $new_width, $new_height, $width, $tmp_height);
        }
        return imagejpeg($image_p, $file_output, 70);
    }
    
    /**
     * Склонение числительныхфывфывфывф
     * @param int $numberof — склоняемое число
     * @param string $value — первая часть слова (можно назвать корнем)
     * @param array $suffix — массив возможных окончаний слов
     * @return string
     *
     */
    static function numberof($numberof, $value, $suffix) {
        // не будем склонять отрицательные числа
        $numberof = abs($numberof);
        $keys = array(2, 0, 1, 1, 1, 2);
        $mod = $numberof % 100;
        $suffix_key = $mod > 4 && $mod < 20 ? 2 : $keys[min($mod%10, 5)];
        
        return $value . $suffix[$suffix_key];
    }

    public static function translit($str) {
        $tr = array(
            "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
            "Д"=>"d","Е"=>"e", "Ё" => "e","Ж"=>"j","З"=>"z","И"=>"i",
            "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
            "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
            "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
            "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
            "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
        );
        return strtr($str, $tr);
    }

    public static function translitUrl($str) {
        $tr = array(
            "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
            "Д"=>"d","Е"=>"e", "Ё" => "e","Ж"=>"j","З"=>"z","И"=>"i",
            "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
            "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
            "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
            "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
            "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
            " "=> "-", "."=> ".", "/"=> "_", ";" => ":"
        );
        return strtr($str, $tr);
    }

	/**
	* Возвращает сумму прописью
	* @author runcore
	* @uses morph(...)
	*/
       static function num2str($num) {
       $num = str_replace(',', '.', $num);
       $num = str_replace(' ', '', $num);
	   $nul='ноль';
	   $ten=array(
	       array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
	       array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	   );
	   $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	   $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	   $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	   $unit=array( // Units
	       array('копейка' ,'копейки' ,'копеек',	 1),
	       array('рубль'   ,'рубля'   ,'рублей'    ,0),
	       array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
	       array('миллион' ,'миллиона','миллионов' ,0),
	       array('миллиард','милиарда','миллиардов',0),
	   );
	   //
	   list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	   $out = array();
	   if (intval($rub)>0) {
	       foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
		   if (!intval($v)) continue;
		   $uk = sizeof($unit)-$uk-1; // unit key
		   $gender = $unit[$uk][3];
		   list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
		   // mega-logic
		   $out[] = $hundred[$i1]; # 1xx-9xx
		   if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
		   else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
		   // units without rub & kop
		   if ($uk>1) $out[]= self::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
	       } //foreach
	   }
	   else $out[] = $nul;
	   $out[] = self::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	   $out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	   return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

	/**
	* Склоняем словоформу
	* @ author runcore
	*/
	static function morph($n, $f1, $f2, $f5) {
		$n = abs(intval($n)) % 100;
		if ($n>10 && $n<20) return $f5;
		$n = $n % 10;
		if ($n>1 && $n<5) return $f2;
		if ($n==1) return $f1;
		return $f5;
	}

    public static function is_utf8($str) {
        $c=0; $b=0;
        $bits=0;
        $len=strlen($str);
        for($i=0; $i<$len; $i++){
            $c=ord($str[$i]);
            if($c > 128){
                if(($c >= 254)) return false;
                elseif($c >= 252) $bits=6;
                elseif($c >= 248) $bits=5;
                elseif($c >= 240) $bits=4;
                elseif($c >= 224) $bits=3;
                elseif($c >= 192) $bits=2;
                else return false;
                if(($i+$bits) > $len) return false;
                while($bits > 1){
                    $i++;
                    $b=ord($str[$i]);
                    if($b < 128 || $b > 191) return false;
                    $bits--;
                }
            }
        }
        return true;
    }
    
    public static function detect_cyr_charset($str) { // функция определения кодировки
        $lowercase = 3;
        $uppercase = 1;
        $charsets = Array('k' => 0, 'w' => 0, 'd' => 0, 'i' => 0, 'm' => 0);
        for ( $i = 0, $length = strlen($str); $i < $length; $i++ ) {
            $char = ord($str[$i]);
            //non-russian characters
            if ($char < 128 || $char > 256) continue;
            //CP866
            if (($char > 159 && $char < 176) || ($char > 223 && $char < 242))
            $charsets['d']+=$lowercase;
            if (($char > 127 && $char < 160)) $charsets['d']+=$uppercase;
            //KOI8-R
             if (($char > 191 && $char < 223)) $charsets['k']+=$lowercase;
            if (($char > 222 && $char < 256)) $charsets['k']+=$uppercase;
            //WIN-1251
            if ($char > 223 && $char < 256) $charsets['w']+=$lowercase;
            if ($char > 191 && $char < 224) $charsets['w']+=$uppercase;
            //MAC
            if ($char > 221 && $char < 255) $charsets['m']+=$lowercase;
            if ($char > 127 && $char < 160) $charsets['m']+=$uppercase;
            //ISO-8859-5
            if ($char > 207 && $char < 240) $charsets['i']+=$lowercase;
            if ($char > 175 && $char < 208) $charsets['i']+=$uppercase;
        }
        arsort($charsets);
        return key($charsets);
    }

    /**
     * преобразует строку, первая буква заглавная, остальные в нижнем регистре
     * @author Rinat_M
     */        
    public static function mb_ucfirst($str) {
        return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8') .
        	mb_substr(mb_convert_case($str, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($str), 'UTF-8');
    }

	/**
	* добавление заданного количества пробелов
	* @author Rinat_M
	*/ 
	public static function add_nbsp($nbsp_count) {
		return str_repeat('&nbsp;', $nbsp_count);
	}
        
    /**
     * Перевод строки в UTF-8, защита от квакозябр в excel
     * @param string $text
     * @return string
     */
    public static function utf8to1251(&$text) {
        $text = mb_convert_encoding($text, 'Windows-1251', 'UTF-8');
        return $text;
    }
    
    /**
     * выгрузка данных в csv файл
     * @author Rinat_M
     * @param string $file_path путь файла
     * @param array $header заголовок документа
     * @param array $body тело документа
     * return bool
     */
    public static function arrayToCsvFile($file_path, $header, $body) {
        if($header) {
            if(!is_array($header)) {
                $header = array();
                foreach ($body as $body_item_index => $body_item) {
                    if(is_array($body_item)) {
                        foreach ($body_item as $index => $item) {
                            $header[] = $index;
                        }
                        break;
                    } else {
                        $header[] = $body_item_index;
                    }                    
                }
                file_put_contents($file_path, implode((array)$header, ';')."\n", FILE_APPEND);
            } else {                
                array_walk($header, array("self", "utf8to1251"));
                file_put_contents($file_path, implode((array)$header, ';')."\n", FILE_APPEND);
            }
        }
        foreach ($body as $body_item) {
            if (is_array($body_item)) {
                array_walk($body_item, array("self", "utf8to1251"));                    
                array_walk($body_item, array("self", "deletelfchar"));
                file_put_contents($file_path, implode((array)$body_item, ';')."\n", FILE_APPEND);
            } else {
                array_walk($body, array("self", "utf8to1251"));
                array_walk($body, array("self", "deletelfchar"));
                file_put_contents($file_path, implode((array)$body, ';')."\n", FILE_APPEND);
                break;
            }                
        }           
        return true;
    }
    
    /**
     * Удаление всех переводов строки
     * @param string $text
     * @return string
     */
    public static function deletelfchar(&$text) {
        $text = str_replace("\n", "", $text);
        return $text;
    }

	/**Конвертируем строку в 1251.
	* Для корректного отображения в Excel выгрузки CSV
	*
	* @param string $text
	*/
	public static function utf8to1251new($text) {
		//$text = iconv("utf-8", "windows-1251", chr(32).$text);
		$text = mb_convert_encoding(chr(32).$text,'Windows-1251','auto');
		return $text;
	}

	/**
     * группировка строк по суммам, если строка не numeric, то подставляется спец.символ
     * @author Rinat_M
     * @param array $input_arr входной массив
	 * @param array $input_arr массив для группировки
	 * @param array $exclude_arr массив исключающих ключей от группировки
     * return $output_arr выходной массив
     */
	public static function reportLineGroup($input_arr, $input_group_arr, $exclude_arr) {
		$output_arr = array();
		foreach ($input_arr as $input_arr_index => $input_arr_value) {
			if(is_numeric($input_arr_value) && !in_array($input_arr_index, $exclude_arr)) {
				$output_arr[$input_arr_index] = isset($input_group_arr[$input_arr_index]) ? $input_group_arr[$input_arr_index] + $input_arr_value : $input_arr_value;
			} else {
				$output_arr[$input_arr_index] = 'x';
			}
		}
		return $output_arr;
	}

	/**
	 * Проверяет, валидный ли json
	 *
	 * @param type $json
	 *
	 * @return bool
	 */
	public static function isValideJson($json){
		json_decode($json);
		return (json_last_error()===JSON_ERROR_NONE);
	}
}
