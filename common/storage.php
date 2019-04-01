<?php

require_once "config.php";

class Storage {
	function manage_file($file, $location) {
		try {
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($file['error']) ||
				is_array($file['error'])
			) {
				throw new RuntimeException('Invalid parameters.');
			}

			// Check error value.
			switch ($file['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}

			// Check for maximum file size
			if ($file['size'] > $this->file_upload_max_size()) {
				throw new RuntimeException('Exceeded filesize limit.');
			}

			// Check MIME Type by yourself.
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
				$finfo->file($file['tmp_name']),
				array(
					'jpg' => 'image/jpeg',
					'png' => 'image/png',
					'gif' => 'image/gif',
					'pdf' => 'application/pdf',
					'doc' => 'application/msword',
					'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'ppt' => 'application/vnd.ms-powerpoint',
					'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				),
				true
			)) {
				throw new RuntimeException('Invalid file format.');
			}

			// Obtain safe unique name from its binary data.
			if (!move_uploaded_file(
				$file['tmp_name'],
				sprintf('%s/%s/%s', $_SERVER['DOCUMENT_ROOT'], $location, $file['name'])
			)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}

			//echo 'File is uploaded successfully.';
			return true;
		} catch (RuntimeException $e) {
			echo $e->getMessage();
			echo '<br/><br/>';
			return false;
		}
	}
	
	// The code following this line were taken from Drupal and
	// are subject to the terms of the GPL license version 2 or later.
	
	// Returns a file size limit in bytes based on the PHP upload_max_filesize
	// and post_max_size
	function file_upload_max_size() {
	  static $max_size = -1;

	  if ($max_size < 0) {
		// Start with post_max_size.
		$post_max_size = $this->parse_size(ini_get('post_max_size'));
		if ($post_max_size > 0) {
		  $max_size = $post_max_size;
		}

		// If upload_max_size is less, then reduce. Except if upload_max_size is
		// zero, which indicates no limit.
		$upload_max = $this->parse_size(ini_get('upload_max_filesize'));
		if ($upload_max > 0 && $upload_max < $max_size) {
		  $max_size = $upload_max;
		}
	  }
	  return $max_size;
	}

	function parse_size($size) {
	  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	  if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	  }
	  else {
		return round($size);
	  }
	}
}

?>