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
    _merge_raw_data_into_request();
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


function _merge_raw_data_into_request() {
    $json_params = file_get_contents( "php://input" );
    if ( strlen($json_params) > 0 ) {
        $arr = json_decode( $json_params, true );
        if ( json_last_error() == JSON_ERROR_NONE ) {
            return $_REQUEST = array_merge( $_REQUEST, $arr );
        }
    }
    return null;
}


/**
 *
 * @param string $idx_or_relation idx or relation
 * @param string $code
 *
 * @return mixed - same as db()->row
 */
function _get_file($idx_or_relation, $code = null) {
    if ( $code ) {
        // relation & code
        $row = db()->row("SELECT * FROM " . _table('files') . " WHERE relation='$idx_or_relation' AND code='$code'");
    } else {
        // idx
        $row = db()->row("SELECT * FROM " . _table('files') . " WHERE idx=$idx_or_relation");
    }
    return $row;
}
/**
 *
 * @desc to debug ` $ tail -f debug.log | grep _delete_file `
 * @param $idx_or_relation
 * @param null $code
 * @return bool
 *      true on success to delete a file
 *      false on failure of deleting a file
 *      2 - if there is not file to delete.
 *
 */
function _delete_file($idx_or_relation, $code = null) {
    _log("_delete_file: idx_or_relation: $idx_or_relation, code: $code");
    $row = _get_file( $idx_or_relation, $code );
    if ( $row ) {
        /**
         * If file exists, delete the file.
         */
        $file = FILE_UPLOAD_DIR . $row['path'];
        if ( file_exists($file) ) {
            $re = @unlink( $file );
            if ( ! $re ) {
                _log('_delete_file: Failed to delete file: ', $file);
                return false;
            }
        }
        $re = db()->table('files')->where(" idx=$row[idx] ")->delete();
        if ( ! $re ) {
            _log("_delete_file: Failed to delete database record: idx: $row[idx]");
            return false;
        }
        return true;
    } else {
        _log("_delete_file: File record does not exists on DB. idx_or_relation: $idx_or_relation, code: $code");
        return 2;
    }
}