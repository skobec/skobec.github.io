<?php

class App_Mpt
{

    /**
     * Возвращает сгенерированный массив месяцев с разбивкой с учетом лет
     * 
     * @param int[] $years - год или массив лет
     * 
     * @return array
     */
    static function generateMonthsArray($years)
    {
        if (is_array($years)) {
            for ($j = 0; $j < count($years); $j++) {
                for ($i = 0; $i < 12; $i++) {
                    if ($i == 0) {
                        $xAxis[] = $years[$j];
                    } else {
                        $xAxis[] = $i + 1;
                    }
                }
            }
        } else {
            for ($i = 0; $i < 12; $i++) {
                $xAxis[] = $i + 1;
            }
        }
        return $xAxis;
    }

    /**
     * Возвращает сгенерированный массив кварталов с разбивкой с учетом лет(не реализована)
     *
     * @param int[] $years - год или массив лет
     *
     * @return array
     */
    static function generateQuartersArray($years)
    {
        if (is_array($years)) {
            //.....
        } else {
            $xAxis[] = 'I квартал';
            $xAxis[] = 'II квартал';
            $xAxis[] = 'III квартал';
            $xAxis[] = 'IV квартал';
        }
        return $xAxis;
    }

    /**
     * Оборачивает строку в ссылку для создания подсказок.
     * @author tugmaks
     * @param string $string
     * 
     * @return string
     */
    public static function wrapHints($string)
    {
        $hints = Service::Thesaurus()->findAll();
        $patterns = array_map(function($item) {
            return "/$item->title/";
        }, $hints);
        $replacements = array_map(function($item) {
            return '<a href="#" data-hint-id="' . $item->id . '" class="cards-title-link js-title-link">'.$item->title.'</a>';
        }, $hints);
        return preg_replace($patterns, $replacements, $string);
    }
}
