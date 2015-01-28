<?php

class fee extends SE_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$this->load->view( 'fee' );
    }
	
	function grid() {
		$grid_type = (isset($_POST['grid_type'])) ? $_POST['grid_type'] : 'not_paid';
		
		if ($grid_type == 'not_paid') {
			$_POST['column'] = array( 'p_father_name', 'p_mother_name', 'total' );
			$_POST['is_custom']  = '<span class="cursor-font-awesome icon-ok btn-paid" title="Paid"></span>';
			$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span>';
		} else if ($grid_type == 'already_paid') {
			$_POST['column'] = array( 'p_father_name', 'payment_method', 'payment_no', 'total' );
			$_POST['is_custom']  = '<span class="cursor-font-awesome icon-link btn-invoice" title="Invoice"></span>';
			$_POST['is_custom'] .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span>';
		}
		
		$array = $this->fee_model->get_array($_POST);
		$count = $this->fee_model->get_count();
		$grid = array( 'sEcho' => $_POST['sEcho'], 'aaData' => $array, 'iTotalRecords' => $count, 'iTotalDisplayRecords' => $count );
		
		echo json_encode($grid);
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		// result default
		$result = array( 'status' => false );
		
		if ($action == 'generate_fee') {
			if ($_POST['payment_type'] == 'register') {
				if ($_POST['payment_for'] == 'All Parent') {
					$array_parent = $this->parents_model->get_array_child(array( 'limit' => 2500 ));
				} else if ($_POST['payment_for'] == 'Spesific Parent') {
					$array_parent = $this->parents_model->get_array_child(array( 'parent_id' => $_POST['parent_id'], 'limit' => 2500 ));
				}
				
				// generate fee
				foreach ($array_parent as $key => $row) {
					// calculate subtotal
					$first_student = (empty($row['student_count'])) ? 0 : $_POST['first_student'];
					$additional_student = ($row['student_count'] <= 1) ? 0 : ($row['student_count'] - 1) * $_POST['additional_student'];
					
					// generate json meta
					$meta = array(
						'first_student' => $_POST['first_student'],
						'additional_student' => $_POST['additional_student']
					);
					
					// insert
					$param_fee = array(
						'parent_id' => $row['parent_id'],
						'discount' => $_POST['discount'],
						'subtotal' => $first_student + $additional_student,
						'is_paid' => 0,
						'json_meta' => json_encode($meta)
					);
					$result = $this->fee_model->update($param_fee);
				}
			}
		}
		else if ($action == 'update_fee') {
			ini_set("memory_limit", "256M");
			$this->load->library('mpdf');
			
			// update fee
			$param_update = array(
				'id' => $_POST['id'],
				'is_paid' => 1,
				'discount' => $_POST['discount'],
				'payment_method' => $_POST['payment_method'],
				'payment_no' => $_POST['last_digit'],
				'invoice_no' => $this->fee_model->get_invoice_no(),
				'invoice_date' => $this->config->item('current_date')
			);
			$result = $this->fee_model->update($param_update);
			$result['message'] = 'Transaction completed';
			
			// get lastest fee data
			$fee = $this->fee_model->get_by_id(array( 'id' => $_POST['id'] ));
			
			// generate invoice
			$filename = $this->config->item('base_path').'/static/temp/'.$param_update['invoice_no'].'.pdf';
			$template = $this->load->view( 'fee_template', array( ), true );
			$this->mpdf->WriteHTML($template);
			$this->mpdf->Output($filename, 'F');
			
			// collect mail to
			//$email_to  = 'her0satr@gmail.com';
			$email_to  = 'school@jafaria.org';
			$email_to .= (empty($fee['p_father_email'])) ? '' : ', '.$fee['p_father_email'];
			$email_to .= (empty($fee['p_mother_email'])) ? '' : ', '.$fee['p_mother_email'];
			
			// email subject
			$email_subject = 'Jafaria Sunday School Invoice For';
			$array_student = $this->student_model->get_array(array( 's_parent_id' => $fee['parent_id'] ));
			foreach ($array_student as $key => $row) {
				if (empty($key)) {
					$email_subject .= ' '.$row['s_name'];
				} else {
					$email_subject .= ', '.$row['s_name'];
				}
			}
			
			// sent mail
			$param_mail = array(
				'to' => $email_to,
				'subject' => $email_subject,
				'message' => '
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
    <style type="text/css">.ReadMsgBody{width:100%;}.ExternalClass{width:100%;}span.yshortcuts{color:#000;background-color:none;border:none;}span.yshortcuts:hover,span.yshortcuts:active,span.yshortcuts:focus{color:#000;background-color:none;border:none;}p{margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;}*{-webkit-text-size-adjust:none;}</style><table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;"><tr><td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;"><div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;"><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tr><td align="center"><table width="600" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#050505;"><tr><td style="padding-right:4px;padding-top:4px;padding-bottom:4px;padding-left:4px;"><table cellpadding="0" cellspacing="0" style="width:592px;border-collapse:collapse;table-layout:fixed;background:#ffffff;"><tr><td width="100%" style="vertical-align:top;"><div><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tr><td style="background:#fcfcfc;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;">
	<tr><td style="vertical-align:middle;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="text-align:center;font-size:11px;margin:0;">
	<em>In The Name of Allah, The Most Gracious, The Most Merciful</em></p>
</div></td></tr></table></td></tr></table></div><div>
<table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tr><td></td></tr></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></table></td></tr></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tr><td></td></tr></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tr><td style="vertical-align: top;" align="center"><div><img style="border:medium none;width:592px;height:186px;resize:none;position:relative;display:block;top:0px;left:0px;" width="592" height="186" src="http://static.sendgrid.com/uploads/UID_1327300_NL_2714447_dff7cd7f7d21aff2568903893d30c4df/c316cbc174fe8efcdd76a27c06696b79" /></div></td></tr></table></td></tr></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tr><td></td></tr></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></table></td></tr></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tr><td></td></tr></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tr><td style="vertical-align:top;"><div style="word-wrap:break-word;line-height:140%;text-align:left;">
<p style="line-height: normal; background-color: rgb(255, 255, 255);">
	<span style="font-family: book antiqua, palatino; font-size: 12pt;">
		Thank you for Registering your Child/Children for Jafaria Sunday School,<br />
		Please find the receipt of your payment attached to this email.<br /><br />

		Let any of our staff know if you have any questions/comments/concerns.<br /><br />
	</span>
</p>
</div></td></tr></table></td></tr></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tr><td></td></tr></table><table style="width:100%;border-collapse:separate;table-layout:fixed;" cellspacing="0" cellpadding="0"><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tr><td width="100%" style="width:100%;border-top-color:#000;border-top-style:solid;border-top-width:8px;" ></td></tr></table></td></tr></table></div><div><table style="border-collapse:separate;border-spacing:0px;table-layout:fixed;" cellpadding="5" cellspacing="5"><tr><td></td></tr></table><table style="width:100%;border-collapse:separate;table-layout:fixed;background:#ffffff;" cellspacing="15" cellpadding="0"><tr><td style="background:#ffffff;"><table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;"><tr><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">
</div></td><td style="vertical-align:middle;font-size:11px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;"><div style="word-wrap:break-word;line-height:140%;text-align:left;"><p style="font-size:11px;margin:0px;text-align:left;">Jafaria Education Center<br />1546 E La Palma Ave, Anaheim , CA, 92805</p></div></td></tr></table></td></tr></table></div></td></tr></table></td></tr></table></td></tr></table></div></td></tr></table>  </body>
</html>
				',
				'attachment' => array( $filename )
			);
			mailer($param_mail);
		} else if ($action == 'delete') {
			$result = $this->fee_model->delete($_POST);
		}
		
		echo json_encode($result);
	}
}