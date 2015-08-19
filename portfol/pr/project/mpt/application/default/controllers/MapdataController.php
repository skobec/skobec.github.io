<?php

class MapdataController extends Zend_Controller_Action {

    function init() {
        $this->_helper->Init->init();
        if (!$this->user->isLogged()) {
            // Functions::redirect('//'.Settings::get('root_domain').'/login/');
        }
    }

    /**
     * @ajax
     */
    public function indexAction() {
        $this->_helper->layout()->disableLayout();
        $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
        $mapkey = $_GET['mapkey'];
        // определение регионов входящих в тот же округ
        $hc_key = explode('-', $mapkey);
        $hc_key = 'ru-' . $hc_key[1];
        $region = db::get()->region()->where('hc_key', $hc_key)->fetch();
        $parent_id = $region['parent_id'];
        $region_list = db::get()->region()->where('parent_id', $parent_id);
        $hc_key_list = [];
        foreach ($region_list as $item) {
            $hc_key_list[] = $item['hc_key'];
        }
        $hc_key_list[] = $hc_key;
        $regions = json_decode(file_get_contents(dirname(__FILE__) . '/regions.json'));
        $field_hc_key = 'hc-key';
        foreach ($regions->features as $index => $feature) {
            $hc_key = $feature->properties->$field_hc_key;
            if (in_array($hc_key, $hc_key_list)) {
                if ($region = db::get()->region()->where('hc_key', $hc_key)->fetch()) {
                    $feature->properties->id = $region['id'];
                    $feature->properties->name = $region['title'];
                    $feature->properties->{'alt-name'} = $region['title'];
                    $feature->properties->{'postal-code'} = $region['title'];
                }
                $regions->features[] = $feature;
            }
            //Оригинальный ркгион тоже удаляем, иначе данные будут дублироваться.
            unset($regions->features[$index]);
        }
        $regions->features = array_values($regions->features);
        echo 'Highcharts.maps["' . $mapkey . '"] = ' . json_encode($regions, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @ajax
     * @help http://gis-lab.info/docs/geojson_ru.html#2.1.7
     */
    public function countriesAction() {
        // die(file_get_contents(dirname(__FILE__).'/../views/scripts/mapdata/countries.phtml'));
        $regions = json_decode(file_get_contents(dirname(__FILE__) . '/regions.json'));
        $features = [];
        $hc_key_list = ['ru-ky'];
        $field_hc_key = 'hc-key';
        $fiel_postal_code = 'postal-code';
        foreach ($regions->features as $index => $feature) {
            $hc_key = $feature->properties->$field_hc_key;
            $feature->properties->$fiel_postal_code = $feature->properties->name;
            $region = db::get()->region()->where('hc_key', $hc_key)->fetch();
            $parent_id = $region['parent_id'];
            if (!$region || !$parent_id) {
                continue;
            }
            if (!array_key_exists($parent_id, $features)) {
                $okrug = db::get()->region()->where('id', $parent_id)->fetch();
                $feature->properties->id = $okrug['id'];
                $feature->properties->name = $okrug['title'];
                $feature->properties->{'alt-name'} = $okrug['title'];
                $feature->properties->{'postal-code'} = $okrug['title'];
                if ($feature->geometry->type == 'Polygon') {
                    $feature->geometry->type = 'MultiPolygon';
                    $feature->geometry->coordinates = [$feature->geometry->coordinates];
                }
                $features[$parent_id] = $feature;
            } else {
                if ($feature->geometry->type == 'MultiPolygon') {
                    // Для объектов типа «MultiPolygon» свойство «coordinates» должно содержать массив массивов пар/триплетов координат «Polygon».
                    foreach ($feature->geometry->coordinates as $coord) {
                        $features[$parent_id]->geometry->coordinates[] = $coord;
                    }
                } elseif ($feature->geometry->type == 'Polygon') {
                    // Для объектов типа «Polygon» свойство «coordinates» должно содержать массив массивов
                    // пар/триплетов координат «LinearRing».
                    // Для полигонов с несколькими кольцами первым должно идти описание
                    // внешнего кольца и только затем внутренних, или дырок.
                    foreach ($feature->geometry->coordinates as $coord) {
                        $features[$parent_id]->geometry->coordinates[0][] = $coord;
                    }
                } else {
                    die($feature->geometry->type);
                }
            }
        }
        $regions->features = array_values($features);
        echo 'Highcharts.maps["countries/ru/ru-all"] = ' . json_encode($regions, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @ajax
     */
    public function originalAction() {
        $this->_helper->layout()->disableLayout();
        $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
        $regions = json_decode(file_get_contents(dirname(__FILE__) . '/regions.json'));
        echo 'Highcharts.maps["countries/ru/ru-all"] = ' . json_encode($regions, JSON_UNESCAPED_UNICODE);
    }

}
