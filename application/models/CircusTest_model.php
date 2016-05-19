<?php

	class CircusTest_model extends CI_Model {
		
		public function __construct()
		{
			parent::__construct();
		}

		public function get_userinfo()
		{
			return "text data is here.";	
		}


		// 같은 connection 내에서는 db 명만 select 해서 사용 하면 된다. 
		public function circusTest_model()
		{
			//$db_test = $this->load->database('circus_test', TRUE);

			$this->db->db_select('circus_test');

			$query = $this->db->get('UserInfo', 10);

			return $query->result();
		}
	}