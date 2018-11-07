<?php
include 'init.php';


//
_prepare_run();
_log('run.php with $_REQUEST: ', $_REQUEST);

//
$_path = "extentions/". _folder() ."/" . _file();
_log("loading: $_path and function: " . _function());
include $_path;

if ( _function() ) {
    _function()();
}

