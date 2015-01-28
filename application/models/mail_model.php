<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mail_model extends CI_Model {
    function __construct() {
        parent::__construct();
		
        $this->field = array( 'id', 'user_id', 'user_type_id', 'from_title', 'from_email', 'to_email', 'subject', 'content', 'mail_info', 'due_date', 'is_read', 'attachment' );
    }

    function update($param) {
        $result = array();
       
        if (empty($param['id'])) {
            $insert_query  = GenerateInsertQuery($this->field, $param, MAIL);
            $insert_result = mysql_query($insert_query) or die(mysql_error());
           
            $result['id'] = mysql_insert_id();
            $result['status'] = '1';
            $result['message'] = 'Data successfully saved.';
        } else {
            $update_query  = GenerateUpdateQuery($this->field, $param, MAIL);
            $update_result = mysql_query($update_query) or die(mysql_error());
           
            $result['id'] = $param['id'];
            $result['status'] = '1';
            $result['message'] = 'Data successfully updated.';
        }
       
        return $result;
    }

    function get_by_id($param) {
        $array = array();
		
        if (isset($param['id'])) {
            $select_query  = "SELECT * FROM ".MAIL." WHERE id = '".$param['id']."' LIMIT 1";
        } 
       
        $select_result = mysql_query($select_query) or die(mysql_error());
        if (false !== $row = mysql_fetch_assoc($select_result)) {
            $array = $this->sync($row);
        }
		
        return $array;
    }
	
    function get_array($param = array()) {
        $array = array();
		
		$param['field_replace']['due_date_title'] = 'due_date';
		
		$string_user = (!empty($param['user_id'])) ? "AND user_id = '".$param['user_id']."'" : '';
		$string_user_type = (!empty($param['user_type_id'])) ? "AND user_type_id = '".$param['user_type_id']."'" : '';
		$string_namelike = (!empty($param['namelike'])) ? "AND subject LIKE '%".$param['namelike']."%'" : '';
		$string_filter = GetStringFilter($param, @$param['column'], array( 'quote' => true ));
		$string_sorting = GetStringSorting($param, @$param['column'], 'subject ASC');
		$string_limit = GetStringLimit($param);
		
		$select_query = "
			SELECT SQL_CALC_FOUND_ROWS *
			FROM ".MAIL."
			WHERE 1 $string_namelike $string_user $string_user_type $string_filter
			ORDER BY $string_sorting
			LIMIT $string_limit
		";
		
        $select_result = mysql_query($select_query) or die(mysql_error());
		while ( $row = mysql_fetch_assoc( $select_result ) ) {
			$array[] = $this->sync($row, $param);
		}
		
        return $array;
    }

    function get_count($param = array()) {
		$count_type = (isset($param['count_type'])) ? $param['count_type'] : '';
		
		// get query
		if ($count_type == 'unread_count' && isset($param['user_id']) && isset($param['user_type_id'])) {
			$select_query = "
				SELECT COUNT(*) total
				FROM ".MAIL."
				WHERE
					is_read = 0
					AND user_id = '".$param['user_id']."'
					AND user_type_id = '".$param['user_type_id']."'";
		} else {
			$select_query = "SELECT FOUND_ROWS() total";
		}
		
		$select_result = mysql_query($select_query) or die(mysql_error());
		$row = mysql_fetch_assoc($select_result);
		$total = $row['total'];
		
		return $total;
    }
	
    function delete($param) {
		$delete_query  = "DELETE FROM ".MAIL." WHERE id = '".$param['id']."' LIMIT 1";
		$delete_result = mysql_query($delete_query) or die(mysql_error());
		
		$result['status'] = '1';
		$result['message'] = 'Data successfully removed.';

        return $result;
    }
	
	function sync($row, $param = array()) {
		$row = StripArray($row);
		
		// time diff
		$row['time_diff'] = show_time_diff($row['due_date']);
		
		// set to bold
		$row['due_date_title'] = ExchangeFormatDate($row['due_date']);
		if ($row['is_read'] == 0) {
			$row['due_date_title'] .= '<span class="hide font-weight"></span>';
		}
		
		if (count(@$param['column']) > 0) {
			$row = dt_view_set($row, $param);
		}
		
		return $row;
	}
	
	function sent_grid($param = array()) {
		$string_title = (!empty($param['title'])) ? ''.$param['title'].',&nbsp;' : '';
		$param['full_body_message'] = (isset($param['full_body_message'])) ? $param['full_body_message'] : false;
		
		// email body
		if ($param['full_body_message']) {
			$email_body = '
	<p style="line-height: normal; background-color: rgb(255, 255, 255); font-family: book antiqua, palatino; font-size: 12pt;">
		'.nl2br($param['content']).'<br /><br />
	</p>
			';
		} else {
			$email_body = '
	<p style="line-height: normal; background-color: rgb(255, 255, 255);">
		<span style="font-family: book antiqua, palatino; font-size: 12pt;">
			'.nl2br($param['content']).'<br /><br />
			JazakAllah,
		</span>
	</p>
	<p style="font-family: arial, sans-serif; font-size: 13px; line-height: normal; background-color: rgb(255, 255, 255);">
		<span style="font-family: book antiqua, palatino; font-size: 12pt;">'.$param['user_display'].'</span><br />
		<span style="font-family: book antiqua, palatino; font-size: 12pt;">'.$string_title.'Jafaria Education Center</span><br />
		<span style="font-family: book antiqua, palatino; font-size: 12pt;">Email.&nbsp;</span><a href="mailto:'.$param['user_email'].'" style="font-family: book antiqua, palatino; font-size: 12pt; color: rgb(17, 85, 204);" target="_blank">'.$param['user_email'].'</a><br />
		<span style="font-family: book antiqua, palatino; font-size: 12pt;">Web. &nbsp;&nbsp;</span><a href="http://www.jafariaschool.org/" style="font-family: book antiqua, palatino; font-size: 12pt; color: rgb(17, 85, 204);" target="_blank">www.jafariaschool.org</a>
	</p>
			';
		}
		
		$param_sent_grid = array(
			'from' => $param['user_email'],
			'fromname' => $param['user_display'],
			'to' => $param['array_to'],
			'subject' => $param['subject'],
			'text' => $param['content'],
			'html' => '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style type="text/css">
      body,td{font-size:14px;font-family:Arial, sans-serif; word-wrap: break-word;}
      p{margin: 0; padding: 0}
    </style>
  </head>
  <body style="width: 100%; margin: 0 auto; text-align: left; font-family: Arial;">
    <style type="text/css">.ReadMsgBody{width:100%;}.ExternalClass{width:100%;}span.yshortcuts{color:#000;background-color:none;border:none;}span.yshortcuts:hover,span.yshortcuts:active,span.yshortcuts:focus{color:#000;background-color:none;border:none;}p{margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;}*{-webkit-text-size-adjust:none;}</style><table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;"><tbody><tr><td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;"><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td align="center"><table width="600" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#050505;"><tbody><tr><td style="padding-right:4px;padding-top:4px;padding-bottom:4px;padding-left:4px;"><table cellpadding="0" cellspacing="0" style="width:592px;border-collapse:collapse;table-layout:fixed;background:#ffffff;"><tbody><tr><td width="100%" style="vertical-align:top;"><div><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#fcfcfc;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;">
	<tbody><tr><td style="vertical-align:middle;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="text-align:center;font-size:11px;margin:0;">
	<em>In The Name of Allah, The Most Gracious, The Most Merciful</em></p>
</div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align: top;" align="center"><div><img style="border:medium none;width:592px;height:186px;resize:none;position:relative;display:block;top:0px;left:0px;" width="592" height="186" src="http://static.sendgrid.com/uploads/UID_1327300_NL_3295225_5d9952d06b97e8ba974d930c7814ea3e/80723c022f02659ad611359be79867ec" /></div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align:top;"><div style="word-wrap:break-word;line-height:140%;text-align:left;">
	
	'.$email_body.'
	
</div></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></tbody></table></td></tr></tbody></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tbody><tr><td></td></tr></tbody></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tbody><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tbody><tr><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">
</div></td><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">Jafaria Education Center<br />1546 E La Palma Ave, Anaheim , CA, 92805</p></div></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table>  </body>
</html>
				'
		);
		
		// add category
		if (isset($param['category'])) {
			$param_sent_grid['category'] = $param['category'];
		}
		
		// add sub
		if (isset($param['array_sub'])) {
			$param_sent_grid['sub'] = $param['array_sub'];
		}
		
		sent_grid($param_sent_grid);
	}
}