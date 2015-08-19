<?php

header("Content-Type: text/css");

$in = dirname(__FILE__).'/style.less';
$out = dirname(__FILE__).'/style.tmp';

if (true) {
    require dirname(__FILE__).'/../../../../framework/lessc.php';
    $less = new lessc;
    $less->checkedCompile($in, $out);
}

echo file_get_contents($out);
