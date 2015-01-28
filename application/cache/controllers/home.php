<?php

class home extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
		$is_login = $this->user_model->is_login();
		
		if ($is_login) {
			$this->load->view( 'home' );
		} else {
			$this->load->view( 'signin' );
		}
    }
	
	function report() {
		$this->load->view( 'home' );
	}
	
	function action() {
		$action = (isset($_POST['action'])) ? $_POST['action'] : '';
		unset($_POST['action']);
		
		$result = array( 'status' => false );
		if ($action == 'signin') {
			$user = $this->user_model->get_by_id(array( 'user_uname' => $_POST['user_uname'] ));
			if (count($user) == 0) {
				$user = $this->parents_model->get_by_id(array( 'p_father_email' => $_POST['user_uname'] ));
			}
			
			$result = array( 'message' => '' );
			if (count($user) == 0) {
				$result['message'] = 'Sorry, username not found.';
			} else if ($user['user_pword'] == $_POST['user_pword']) {
				// additional data for parent
				if ($user['user_type_id'] == USER_TYPE_PARENT) {
					// get all student by parent
					$array_student = $this->student_model->get_array(array( 's_parent_id' => $user['user_id'] ));
					
					// stop if parent do not have student here
					if (count($array_student) == 0) {
						$result['message'] = 'Sorry, you do not have active student here.';
						echo json_encode($result);
						exit;
					}
					
					// add session
					$user['student_id'] = $array_student[0]['s_id'];
					$user['array_student'] = $array_student;
				} else {
					// update time
					$param_update['user_id'] = $user['user_id'];
					$param_update['user_logins'] = $user['user_logins'] + 1;
					$param_update['user_lastlogin'] = $this->config->item('current_datetime');
					$this->user_model->update($param_update);
				}
				
				// set session
				$result['status'] = true;
				$this->user_model->set_session($user);
			} else {
				$result['message'] = 'Sorry, passsword didnot match.';
			}
		}
		else if ($action == 'update_password') {
			// user
			$user = $this->user_model->get_session();
			
			// parent
			if ($user['user_type_id'] == USER_TYPE_PARENT) {
				$parent = $this->parents_model->get_by_id(array( 'p_id' => $user['user_id'] ));
				if ($parent['user_pword'] == $_POST['passwd_old']) {
					// update
					$param_update['p_id'] = $parent['p_id'];
					$param_update['p_passwd'] = $_POST['passwd_new'];
					$this->parents_model->update($param_update);
					
					// message
					$result['status'] = true;
					$result['message'] = 'Password successful updated.';
				} else {
					// message
					$result['message'] = 'Old passwords are not the same.';
				}
			}
			// admin
			else {
				$user = $this->user_model->get_by_id(array( 'user_id' => $user['user_id'] ));
				if ($user['user_pword'] == $_POST['passwd_old']) {
					// update
					$param_update['user_id'] = $user['user_id'];
					$param_update['user_pword'] = $_POST['passwd_new'];
					$this->user_model->update($param_update);
					
					// message
					$result['status'] = true;
					$result['message'] = 'Password successful updated.';
				} else {
					// message
					$result['message'] = 'Old passwords are not the same.';
				}
			}
		}
		else if ($action == 'forget_password') {
			// user
			$user = $this->user_model->get_by_id(array( 'user_email' => $_POST['email'] ));
			if (count($user) == 0) {
				$user = $this->parents_model->get_by_id(array( 'p_father_email' => $_POST['email'] ));
			}
			
			// result
			if (count($user) == 0) {
				$result['message'] = 'Sorry, username not found.';
			} else {
				// sent grid
				$param_sent_grid = array(
					'from' => 'noreply@jafariaschool.org',
					'fromname' => 'System',
					'to' => array( array( 'email' => $_POST['email'] ) ),
					'subject' => SITE_TITLE.' - Forget Password',
					'text' => 'You have request forget password, below information for your password.<br />Your password : '.$user['user_pword'],
					'html' => 'You have request forget password, below information for your password.<br />Your password : '.$user['user_pword']
				);
				sent_grid($param_sent_grid);
				
				// update result
				$result['status'] = true;
				$result['message'] = 'Please check your email to get your password.';
			}
		}
		else if ($action == 'change_student') {
			// update session
			$user = $this->user_model->get_session();
			$user['student_id'] = $_POST['student_id'];
			$this->user_model->set_session($user);
			
			$result['status'] = true;
		}
		else if ($action == 'reset_task') {
			$result = $this->task_model->delete(array( 'truncate' => true ));
		}
		else if ($action == 'reset_attendance') {
			$result = $this->attendance_model->delete(array( 'truncate' => true ));
		}
		
		echo json_encode($result);
		exit;
	}
	
	function logout() {
		$this->user_model->del_session();
		header("Location: ".base_url());
		exit;
	}
}