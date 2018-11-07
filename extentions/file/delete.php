<?php
/**
 * @file delete.php
 */
/**
 * Deletes a file if user_no or security code matches.
 */

// prepare vars
$idx = _in('idx');
$user_no = _in('user_no');


/// logic


$file = _get_file( $idx );
if ( ! $file ) _error('file_not_found', 'File record not found');

// security check
if ( $user_no != $file['user_no'] ) _error('not_your_file', 'This is not your file');

// delete
if ( _delete_file( $idx ) ) _success(['idx' => $idx ]);
else _error('delete_file', 'Failed to delete file');

