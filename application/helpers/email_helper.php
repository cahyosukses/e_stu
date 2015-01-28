<?php

if (!function_exists('mime_content_type')) {
    function mime_content_type($filename) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
		
		preg_match('/\.([a-z0-9]+)$/i', $filename, $match);
		$ext = (!empty($match[1])) ? $match[1] : '';
		
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

if (!function_exists('mailer')) {
	/*
	mailer(array( 'to' => 'her0satr@gmail.com', 'subject' => 'Mail Without Attachment - '.time() ));
	mailer(array( 'to' => 'her0satr@gmail.com', 'subject' => 'Mail With Single Attachment - '.time(), 'attachment' => array( 'file-01.pdf' ) ));
	mailer(array( 'to' => 'her0satr@gmail.com', 'subject' => 'Mail With Double Attachment - '.time(), 'attachment' => array( 'file-01.pdf', 'file-02.pdf' ) ));
	/*	*/
	
	function mailer($param) {
		$result = array( 'status' => false, 'message' => '' );
		if (empty($param['to'])) {
			$result['message'] = 'Email destination cannot empty.';
			return $result;
		}
		if (empty($param['subject'])) {
			$result['subject'] = 'Subject cannot empty.';
			return $result;
		}
		
		// default data
		$mime_boundary = md5(time());
		$param['from'] = (empty($param['from'])) ? 'noreply@jafariaschool.org' : $param['from'];
		$param['message'] = (empty($param['message'])) ? '-' : $param['message'];
		$param['attachment'] = (isset($param['attachment'])) ? $param['attachment'] : array();
		
		// array attachment
		$attachemnt = array();
		foreach ($param['attachment'] as $row) {
			// get file name
			$array_temp = explode('/', $row);
			$filename = $array_temp[count($array_temp) - 1];
			
			// get mime
			$mime = mime_content_type($row);
			
			// get content
			$handle = fopen($row, 'rb');
			if ($handle) {
				$content = fread($handle, filesize($row));
				$content = chunk_split(base64_encode($content));
				fclose($handle);
			}
			
			// add to attachment
			$attachemnt[] = array(
				'name' => $filename,
				'mime' => $mime,
				'content' => $content
			);
		}
		
		// generate attachment
		$mail_attach = '';
		foreach ($attachemnt as $key => $row) {
			$content  = "--".$mime_boundary."\r\n";
			$content .= "Content-Type: ".$row['mime']."; name=\"".$row['name']."\"\r\n";
			$content .= "Content-Transfer-Encoding: base64\r\n";
			$content .= "Content-Disposition: attachment; filename=\"".$row['name']."\"\r\n\r\n";
			$content .= $row['content']."\r\n\r\n";
			$mail_attach .= $content;
		}
		
		$headers =
			"From: ".$param['from']."\r\n".
			"Reply-To: ".$param['from']."\r\n".
			"Message-ID: <".time()." ".$param['from'].">\r\n".
			'X-Mailer: PHP Mailer'."\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: multipart/mixed; boundary=\"".$mime_boundary."\"\r\n";
		
		$msg  = "This message is in MIME format. Since your mail reader does not understand this format, some or all of this message may not be legible.\r\n\r\n";
		$msg .= "--".$mime_boundary."\r\n";
		$msg .= "Content-Type: text/html; charset=iso-8859-1\r\n";
		$msg .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$msg .= $param['message']."\r\n\r\n";
		$msg .= $mail_attach;
		$msg .= "--".$mime_boundary."--\r\n\r\n";

		if (SENT_MAIL) {
			if (!empty($param['to'])) {
				mail($param['to'], $param['subject'], $msg, $headers);
			}
		}
	}
}