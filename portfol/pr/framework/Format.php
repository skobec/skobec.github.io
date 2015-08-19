<?php

class Format {

	/**
	* Возвращает рзмер файла в человекочитаемом виде
	* @author sciner
	* @since 2015-01-20
	* 
	* @param int $bytes
	* 
	* @return string
	*/
	static function fileSizeUnits($bytes) {
		if ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ' ГБ';
		} elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ' МБ';
		} elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 0) . ' КБ';
		} elseif ($bytes > 1) {
			$bytes = $bytes . ' байт';
		} elseif ($bytes == 1) {
			$bytes = $bytes . ' байт';
		} else {
			$bytes = '0 байт';
		}
		return $bytes;
	}

	/**
	* @author sciner
	* @since 2014-07-11
	* 
	* @param string $text
	* 
	* @return string
	*/
    static function replaceTextHyperlinks($text) {
        $text = nl2br(htmlspecialchars($text));
        $text = str_replace('&amp;', '&', $text);
        $text = preg_replace('/((http(s)*:\/\/)+(([a-zA-Zа-яА-ЯёЁ0-9\-.]+\.[a-zA-Zа-яА-ЯёЁ0-9\-]+)([\/]([a-zA-Z0-9_\/\-.?&#;%=+])*)*))/u',
            '<noindex><a href="$1" rel="nofollow">$1</a></noindex>', $text);
        return $text;
    }

    /**
    * Возвращает ФИО
    * 
    * @param object $user
    */
    public static function fio($last_name, $first_name = null, $middle_name = null) {
        if(!isset($user->LName)) {
            return null;
        }
        $fio = array();
        if($last_name) {$fio[] = $last_name;}
        if($first_name) {$fio[] = $first_name;}
        if($middle_name) {$fio[] = $middle_name;}
        return count($fio) ? implode(' ', $fio) : null;
    }

    /**
    * Возвращает дату в читабельном виде
    * 
    * @param int $date
    * @param bool $showYear
    * @param bool $short
    * @param bool $showTime
    * @param bool $showSeconds
    * @param bool $smartYear
    * @param bool $showDate
    * 
    * @return string
    */
    public static function date($date, $showYear = true, $short = false, $showTime = false, $showSeconds = false, $smartYear = false, $showDate = true) {
        if(!$date) {
            return 'Нет данных';
        }
        $months = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
        $months2 = array('янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
        if($short){
            $months = $months2;
        }
        $day = date('j', $date);
        $month = date('n', $date);
        $mth = $months[$month - 1];
        $year = date('Y', $date);
        $ret = $mth;
		if($showDate) {
		    $ret = $day.' '.$ret;
		}
        if($showYear) {
            if($smartYear) {
                $currentYear = date('Y', time());
                if($year != $currentYear) {
                    $ret .= ' '.$year;
                }
            } else {
                $ret .= ' '.$year;
            }
        }
        if($showTime) {
            if($showSeconds) {
                $ret .= ', '.date('H:i:s', $date);
            } else {
                $ret .= ', '.date('H:i', $date);
            }
        }
        return $ret;
    }

    public static function phoneNumber($sPhone) {
        $sPhone = self::onlyDigits($sPhone);
        $sPhone = substr($sPhone, -10);
        if(strlen($sPhone) != 10) {
            return null;
        }
        $sArea = substr($sPhone, 0,3); 
        $sPrefix = substr($sPhone,3,3); 
        $sNumber1 = substr($sPhone,6,2); 
        $sNumber2 = substr($sPhone,8,2); 
        $sPhone = "+7(".$sArea.")".$sPrefix."-".$sNumber1."-".$sNumber2; 
        return $sPhone; 
    }

    public static function beautifyAddress($country = null, $region_prefix = null, $region_title = null, $locality_prefix = null, $locality_title = null,
        $street_prefix = null, $street_title = null, $house_number = null, $house_block = null) {
        $buffer = array();
        if($country) {$buffer[] = $country;}
        if($region_title) {$buffer[] = ($region_prefix ? $region_prefix : null).' '.$region_title;}
        if($locality_title) {$buffer[] = ($locality_prefix ? $locality_prefix : null).' '.$locality_title;}
        if($street_title) {$buffer[] = ($street_prefix ? $street_prefix : null).' '.$street_title;}
        if($house_number) {$buffer[] = 'дом '.$house_number;}
        if($house_block) {$buffer[] = 'корпус '.$house_block;}
        return join(', ', $buffer);
    }

    /**
    * День недели
    * 
    * @param mixed $date
    * @param mixed $short
    */
    public static function weekDayName($date, $short = true) {
        if($short) {
            $daysOfWeek = array("Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"); 
        }
        else {
            $daysOfWeek = array("Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"); 
        }
        $arr = getdate($date);
        return $daysOfWeek[$arr['wday']];
    }

    /**
    * Функция возвращает красиво обрезаную длинную строку, добавляя при необходимости в конце многоточие
    * 
    * @param mixed $string Исходная строка
    * @param mixed $length_limit Условие на длинну строки. Если строка короче, то функция вернет исходную строку
    * @param mixed $set_max_words Строка урезается по указанному в данном параметре количеству слов
    * @param mixed $add_dots Добавлять или нет многоточие в конце
    * 
    * @return string
    */
    public static function truncateStringDotted($string, $length_limit = 128, $set_max_words = 7, $add_dots = true) {
        if(is_null($string)) {
            return null;
        }
        $length_limit = (int)$length_limit;
        if(strlen($string) <= $length_limit) {
            return $string;
        }
        $tp = explode(' ', $string);
        $return_value = null;
        $words_count = 0;
        $words = Array();
        foreach($tp as $word) {
            $words[] = $tp[$words_count];
            $words_count++;
            if($words_count >= $set_max_words)
            {
                break;
            }
        }
        $return_value = implode(' ', $words);
        if($add_dots) {
            $return_value .= '...';
        }
        $pos = strpos($return_value, '{');
        if($pos !== false) {
            return $string;
        }
        return $return_value;
    }

    /**
    * Склонение чисел для красоты вывода информации
    * @author sciner
    * @since 2014-05-24
    * 
    * @example Format::numberOf($this->adverts_count, array('комментарий', 'комментария', 'комментариев'))
    * 
    * @tutorial Алгоритм:
    * Число заканчивающееся на 1 (1, 21, 31, 101, 1001, 1161 и т.д.), исключение 11, получает первое окончание: комментари(й)
    * Далее все числа в диапазоне от 2 до 4 (2-4, 22-24, 32-34, 102-104, 1122-1124), исключение 12-14, получает второе окончание: комментари(я)
    * 
    * @param number $number Склоняемое число
    * @param string[] $suffix массив возможных слов
    * 
    * @return string
    */
    public static function numberOf($number, $suffix) {
        // не будем склонять отрицательные числа
        $number = abs($number);
        $keys = array(2, 0, 1, 1, 1, 2);
        $mod = $number % 100;
        $suffix_key = $mod > 4 && $mod < 20 ? 2 : $keys[min($mod % 10, 5)];
        return $suffix[$suffix_key];
    }
}