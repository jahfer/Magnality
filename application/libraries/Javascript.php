<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Javascript {

	private $CI;
	public $scriptList;
	
	public function __construct() {
		$this->CI  =& get_instance();	
	}

	public function addScript($script) {
		$this->scriptList .= $script;
	}
	
	public function output() {
		$this->CI->load->helper("JSMin");
		$script = str_replace(
			array("<script>", "</script>"), 
			array("", ""), 
			$this->scriptList
		);
		echo "<script async>".JSMin::minify($script)."</script>";
	}
	
}

/* End of file Javascript.php */