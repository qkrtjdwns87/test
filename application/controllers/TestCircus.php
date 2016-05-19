<?php 
	class TestCircus extends CI_Controller {

		public function index()
		{
			echo CI_VERSION;
		}

		public function test_1($para1, $para2)
		{
			echo 'para1 : ' . $para1;
			echo '<br/>';
			echo 'para2 : ' . $para2;
 		}

 		public function test_2()
 		{
 			$this->load->view('testcircus_view');
 		}

 		public function test_3()
 		{
 			$this->load->model('circusTest_model');

 			$result = $this->circusTest_model->get_userinfo();

 			$data['title'] = "this is Title.";
 			$data['body'] = "this is Body....";
 			$data['aaa'] = $result;

 			$this->load->view('testcircus_view', $data);
 		}

 		public function test_4() 
 		{
 			$this->load->model('circusTest_model');

 			echo $this->circusTest_model->get_userinfo();
 		}

 		// session 문제 해결 필요
 		public function test_5()
 		{
			$this->load->model('circusTest_model'); 			

			print_r($this->circusTest_model->get_userinfo_total());
 		}

 		public function test_6()
 		{
 			print('here i am');
 		}
	}