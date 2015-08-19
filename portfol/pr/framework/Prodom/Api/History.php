<?php

/**
* Запись истории вызова для API-шлюзов
*/
class Prodom_Api_History extends Prodom_Type {

    /**
    * Записи истории вызова для API-шлюзов
    * @author sciner
    * @since 16-07-2012 12:39
    * 
    * @var Prodom_Api_History
    */
    public static $history = array();

    /**
    * имя метода
    * 
    * @var string
    */
    public $method = null;
    
    /**
    * размер ответа
    * 
    * @var int
    */
    public $response_size = null;
    
    /**
    * затраченное время
    * 
    * @var float
    */
    public $elapsed = null;

    /**
    * код ответа
    * 
    * @var int
    */
    public $code = null;
    
    public static function dump() {
        ?>
        <!doctype html>
        <html>
        <head>
            <style>
                body {font-family: "Arial";}
                table {
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid #bbb;
                }
                th, td {
                    padding: .3em;
                }
                th {
                    background: #bbb;
                    color: #fff;
                }
                td {
                    font-family: "Courier new";
                }
            </style>
        </head>
        <body>
        <table border="1">
            <thead>
                <tr>
                    <th>Метод</th>
                    <th>Код ответа</th>
                    <th>Затраченное время</th>
                    <th>Вх. трафик</th>
                </tr>
            </thead>
            <tbody>
                <?$t=0;?>
                <?$s=0;?>
                <?$history = self::$history?>
                <?foreach($history as $record){?>
                    <tr>
                        <td><?=$record->method?></td>
                        <td><?=$record->code?></td>
                        <td><?=$record->elapsed?></td>
                        <td><?=round($record->response_size / 1024, 3)?> Kb</td>
                        <?$t+=$record->elapsed?>
                        <?$s+=$record->response_size?>
                    </tr>
                <?}?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?=round($t, 5)?></td>
                        <td><?=round($s / 1024, 3)?> Kb</td>
                    </tr>
            </tbody>
        </table>
        </body>
        </html>
        <?
        die();
    }

}