<?php

header("Content-Type: text/css");

$in = dirname(__FILE__).'/admin.less';
$out = dirname(__FILE__).'/admin.tmp';

if (true) {
    require dirname(__FILE__).'/../../../../framework/lessc.php';
    $less = new lessc;
    $less->checkedCompile($in, $out);
}

echo file_get_contents($out);
