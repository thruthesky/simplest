<?php
include '../init.php';


$backendUrl = "https://seo.sonub.com/simplest/run.php";



function rpc($in) {
    global $backendUrl;

    $url = $backendUrl . '?' . http_build_query($in);
    $re = file_get_contents($url);

    return json_decode( $re, true );
}



function testOk($msg) {
    echo "[ OK ] $msg\n";
}

function testBad($msg) {
    echo ">>>>>>>>>>>>>>> BAD : $msg\n";
    echo "^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n";
}

