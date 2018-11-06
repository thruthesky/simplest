<?php
/**
 * @file library.php
 */


$_log_count = 0;

/**
 * Returns the HTTP input value.
 * @param $key
 * @param null $default_value
 * @return null
 */
function _in($key, $default_value = null ) {
    if ( isset($_REQUEST[$key]) && $_REQUEST[$key] ) return $_REQUEST[$key];
    else return $default_value;
}


/**
 * Logs message to FILE_UPLOAD_DIR . 'debug.log'
 * @param mixed $msg1 msg
 * @param mixed $msg2 msg
 *
 * @use $ tail -f files/debug.log
 */
function _log( $msg1, $msg2 = '' ) {
    global $_log_count;
    $_log_count ++;
    $message = "[$_log_count]";
    if( is_array( $msg1 ) || is_object( $msg1 ) ){
        $msg1 = print_r( $msg1, true );
    }
    if( is_array( $msg2 ) || is_object( $msg2 ) ){
        $msg2 = print_r( $msg2, true );
    }
    $message = $message . " $msg1 $msg2\n";
    file_put_contents( LOG_FILE, $message, FILE_APPEND);
}

/**
 * Prepare for script running
 *
 * @reason Needs to sanitize the input for security.
 * @todo security check for the input.
 */
function _prepare_run() {
    global $_config;
    $run = _in('run') or _error('no_run', 'No run code provided');
    $actions = explode('.', $run, 3);
    $_config['folder'] = $actions[0];
    $_config['file'] = $actions[1];
    if ( isset($actions[2]) ) $_config['function'] = $actions[2];
    else $_config['function'] = null;
}


function _folder() {
    global $_config;
    return $_config['folder'];
}
function _file() {
    global $_config;
    return $_config['file'] . '.php';
}
function _function() {
    global $_config;
    return $_config['function'];
}





/**
 * Returns a safe file name to save on server disk from a user filename.
 * User filename may have characters that are not supported. like Korean character.
 *
 * @note This remains the extensions.
 *
 * @todo make it more readable file name instead of md5 string.
 *
 * @param $filename
 *
 * @return string
 */
function _safe_filename($filename) {
    $pi = pathinfo($filename);
    $sanitized = md5($pi['filename'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . time());
    if ( isset($pi['extension']) && $pi['extension'] ) return $sanitized . '.' . $pi['extension'];
    else return $sanitized;
}



/**
 * Print error json format string and exit.
 * @param $code
 * @param $message
 */
function _error( $code, $message ) {
    echo json_encode(['code' => $code, 'message' => $message]);
    exit;
}

/**
 * Print json data and exit.
 * @param mixed $data
 */
function _success( $data ) {
    echo json_encode($data);
    exit;
}
