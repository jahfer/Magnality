<? 
	$data['LOGGED_USER_ID'] = $this->session->userdata('user_id');	
	$data['LOGGED_REAL_NAME'] = $this->membership_model->fetch_real_name($data['LOGGED_USER_ID']);
	$data['LOGGED_EMAIL'] = $this->membership_model->fetch_email($data['LOGGED_USER_ID']);
?>

<?$this->load->view('inc/header', $data);?>	
<?$this->load->view($main_content);?>
<?$this->load->view('inc/footer');?>