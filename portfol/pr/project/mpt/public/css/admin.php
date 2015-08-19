<?php

header("Content-Type: text/css");

$in = 'admin.less';
$all_in = array($in);
$out = 'admin.tmp';

$max_less_ftime = -9999999999999;
foreach($all_in as $a) {
    if(filemtime($a) > $max_less_ftime) {
        $max_less_ftime = filemtime($a);
    }
}

if (!is_file($out) || ($max_less_ftime > filemtime($out))) {
    require dirname(__FILE__).'/../../../../framework/lessc.php';
    $less = new lessc;
    try {
        // file_put_contents(dirname(__FILE__)."/{$out}", null);
        $less->checkedCompile($in, $out);
        // $less->compile($in, $out);
    } catch(Exception $ex) {
        die($ex->getMessage());
    }
}

echo file_get_contents(dirname(__FILE__)."/{$out}");
