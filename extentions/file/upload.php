<?php
_log('upload.php begins with $_FILES', $_FILES);


if ( empty( $_FILES ) ) _error('no_files', 'No files are uploaded.');
if ( ! isset( $_FILES['file'] ) ) _error('no_file', 'file data is empty.');

$file = $_FILES['file'];
$file_name = _safe_filename( $file["name"] );
$path = FILE_UPLOAD_DIR . $file_name;
if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) _error('failed_move_uploaded_file', "Failed on moving uploaded file." );


/// If old relation & code exists, then it deletes.
/// code can be user_no.
$relation = _in('relation', '');
$code = _in('code', '');
$row = db()->row("SELECT idx, path FROM " . _table('files') . " WHERE relation='$relation' AND code='$code'");
if ( $row ) {
    $re = db()->table('files')->where(" idx=$row[idx] ")->delete();
    if ( ! $re ) _error('failed_to_delete_old_file', 'Failed to delete existing file');
    @unlink( FILE_UPLOAD_DIR . $row['path']);
}
$record = [
    'user_no' => _in('user_no', 0),
    'relation' => $relation,
    'code' => $code,
    'name' => $file['name'],
    'type' => $file['type'],
    'size' => $file['size'],
    'complete' => _in('complete', 'y'),
    'path' => $file_name,
    'stamp_created' => time(),
    'stamp_updated' => time()
];

$idx = db()->table('files')->record($record)->insert();

$file = db()->row("SELECT * FROM " . _table('files') . " WHERE idx=$idx");
_log("uploaded record: ", $file);
$file['url'] = FILE_DIR_URL . $file_name;

_success($file);


