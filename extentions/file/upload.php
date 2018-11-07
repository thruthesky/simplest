<?php
_log('upload.php begins with $_FILES', $_FILES);


if ( empty( $_FILES ) ) _error('no_files', 'No files are uploaded.');
if ( ! isset( $_FILES['file'] ) ) _error('no_file', 'file data is empty.');

// prepare vars
$relation = _in('relation', '');
$code = _in('code', '');
$file = $_FILES['file'];
$file_name = _safe_filename( $file["name"] );
$path = FILE_UPLOAD_DIR . $file_name;



/// Delete old file if relation & code passed
/// code can be user_no.
if ( $code ) {
    if ( ! _delete_file( $relation, $code ) ) _error('failed_to_delete_old_file', 'Failed to delete existing file');
}



/// Insert DB record first
$record = [
    'user_no' => _in('user_no', 0),
    'relation' => $relation,
    'code' => $code,
    'name' => $file['name'],
    'type' => $file['type'],
    'size' => $file['size'],
    'complete' => _in('complete', 'y'),
    'path' => $file_name,
    'url' => FILE_UPLOAD_DIR_URL . $file_name,
    'stamp_created' => time(),
    'stamp_updated' => time()
];
$idx = db()->table(FILES)->record($record)->insert();


/// prepare variables
$file_name = "$idx-$file_name";
$path = FILE_UPLOAD_DIR . $file_name;
$url = FILE_UPLOAD_DIR_URL . $file_name;

/// copy uploaded file into storage including file.idx on filename
///
if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
    db()->table(FILES)->where("idx=$idx")->delete();
    _error('failed_move_uploaded_file', "Failed on moving uploaded file." );
}

$re = db()->table(FILES)->fields(['url' => $url, 'path' => $path])->where("idx=$idx")->update();
if ( ! $re ) _error( 'failed_update_file', "failed to update ");



$file = db()->row("SELECT * FROM " . _table('files') . " WHERE idx=$idx");
_log("uploaded record: ", $file);


_success($file);


