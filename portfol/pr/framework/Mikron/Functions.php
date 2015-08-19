<?php
  
class Mikron_Functions {
    
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
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function onlyDigits($string) {
        $p = preg_replace("/[^0-9]/", null, $string);
        return $p;
    }

	public static function cast($item, $typeName) {
        if(!is_array($item) && !is_object($item)) {
            switch($typeName) {
                case 'datetime':                                        
                    if(strlen($item) == 7) {
                       $item = '01-'.$item;
                    }
                    return date('Y-m-d H:i:s', strtotime($item));
                    break;
                case 'string': return (string)$item;
                case 'file': return (string)$item;
                case 'file[]': return (string)$item;
                case 'int': return (int)$item;
                case 'bool': return (trim($item) === '1') ? true : false;
                case 'float': return (float)str_replace(',', '.', $item);
                case 'text': return (string)$item;
                case 'html': return (string)$item;
                default: throw new Exception('Functions::cast incorrected input param $item: `'.$typeName.'`');
            }
        }
        if($typeName == 'file[]') {
            return null;
        }
        
        // @notfoolen
        if($typeName == 'json') {
            return json_encode($item, JSON_UNESCAPED_UNICODE);
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
        }
        $uri = $adduri;
        $path = $startUri;
        if(is_null($path)) {
            $path = $_SERVER['REQUEST_URI'];
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
            $a = Array();
            $a_values = Array();
            $attrs = explode('&', $query);
            if(count($attrs) > 0) {
                foreach($attrs as $attr) {
                    $v = explode('=', $attr, 2);
                    if(count($v) == 2) {
                        $a[$v[0]] = $v[1];
                    }
                    else {
                        $a[$v[0]] = null;
                    }
                }
                foreach($a as $key => $value) {
                    $a_values[] = $key.'='.$value;
                }
                $uri = str_replace($query, implode('&', $a_values), $uri);
            }
        }
        $uri = str_replace('?=&amp;', '?', $uri);
        $uri = str_replace('&=', null, $uri);
        $uri = str_replace('&amp;=', null, $uri);
        return $uri;
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
    public static function dateFormat($date, $showYear = true, $short = false, $showTime = false, $showSeconds = false, $smartYear = false) {
        if(!$date) {
            return '<span class="font-no-data" >Нет данных </span>';
        }
        $months = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
        $months2 = array('янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
        if($short){
            $months=$months2;
        }
        $day = date('j', $date);
        $month = date('n', $date);
        $mth = $months[$month-1];
        $year = date('Y', $date);
        $ret = $day.' '.$mth;
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
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    public static function num2str($num) {
    	$num = str_replace(',', '.', $num);
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',     1),
            array('американский доллар'   ,'американского доллара'   ,'американских долларов'    ,0),
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
        // $out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
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
}
