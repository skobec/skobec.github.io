<?php

class Mikron_Entity_Designer_Field_Link extends Mikron_Entity_Designer_Field {

    public $url;

    function __toString() {
        $title = 'link';
        echo <<<html
<a href="{$this->url}">{$title}</a>
html;
    }

}