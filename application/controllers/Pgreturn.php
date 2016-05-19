<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Pgreturn
 * 결제사 통보 처리
 * (구매자 구매확인)
 *
 * @author : Administrator
 * @date    : 2016. 03.
 * @version:
 */
class Pgreturn extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_uriMethod = 'ordconfirm';
	
	public function __construct()
	{
		parent::__construct ();
		
		$this->load->helper(array('url'));
		$this->load->model(array('order_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'ordconfirm':
				$this->SetPGCallProcess();
				break;			
		}
	}	
	
	/**
	 * @method name : setPrecedeValues
	 * uri 처리관련
	 * post, get 내용 처리 (선행처리가 필요한 것만 - 그외의 것은 메소드 안에서 처리)
	 *
	 */
	private function setPrecedeValues()
	{
		//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
		$this->_uri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
		$this->_arrUri = $this->common->segmentExplode($this->_uri);
		$this->_uriMethod = (!empty($this->uri->segment(2))) ? $this->uri->segment(2) : $this->_uriMethod;
		$this->_uriMethod = $this->common->nullCheck($this->_uriMethod, 'str', 'list');
		
		$this->_uriMethod = (!empty($this->uri->segment(2))) ? $this->uri->segment(2) : $this->_uriMethod;
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->_uriMethod;
		
		/*
		 * 파라메터 처럼 array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'pageMethod' => $this->_uriMethod
		);
	}
	
	private function SetPGCallProcess()
	{
		/* ============================================================================== */
		/* =   01. 공통 통보 페이지 설명(필독!!)                                        = */
		/* = -------------------------------------------------------------------------- = */
		/* =   공통 통보 페이지에서는,                                                  = */
		/* =   가상계좌 입금 통보 데이터와 모바일안심결제 통보 데이터 등을 KCP를 통해   = */
		/* =   실시간으로 통보 받을 수 있습니다.                                        = */
		/* =                                                                            = */
		/* =   common_return 페이지는 이러한 통보 데이터를 받기 위한 샘플 페이지        = */
		/* =   입니다. 현재의 페이지를 업체에 맞게 수정하신 후, 아래 사항을 참고하셔서  = */
		/* =   KCP 관리자 페이지에 등록해 주시기 바랍니다.                              = */
		/* =                                                                            = */
		/* =   등록 방법은 다음과 같습니다.                                             = */
		/* =  - KCP 관리자페이지(admin.kcp.co.kr)에 로그인 합니다.                      = */
		/* =  - [쇼핑몰 관리] -> [정보변경] -> [공통 URL 정보] -> [공통 URL 변경 후]에  = */
		/* =    결과값은 전송받을 가맹점 URL을 입력합니다.                              = */
		/* ============================================================================== */
		

		/* ============================================================================== */
		/* =   02. 공통 통보 데이터 받기                                                = */
		/* = -------------------------------------------------------------------------- = */
		$site_cd      = $this->input->post_get('site_cd', TRUE);                // 사이트 코드
		$tno          = $this->input->post_get('tno', TRUE);                // KCP 거래번호
		$order_no     = $this->input->post_get('order_no', TRUE);               // 주문번호
		$tx_cd        = $this->input->post_get('tx_cd', TRUE);                // 업무처리 구분 코드
		$tx_tm        = $this->input->post_get('tx_tm', TRUE);                 // 업무처리 완료 시간
		/* = -------------------------------------------------------------------------- = */
		$ipgm_name    = "";                                    // 주문자명
		$remitter     = "";                                    // 입금자명
		$ipgm_mnyx    = "";                                    // 입금 금액
		$bank_code    = "";                                    // 은행코드
		$account      = "";                                    // 가상계좌 입금계좌번호
		$op_cd        = "";                                    // 처리구분 코드
		$noti_id      = "";                                    // 통보 아이디
		/* = -------------------------------------------------------------------------- = */
		$refund_nm    = "";                                    // 환불계좌주명
		$refund_mny   = "";                                    // 환불금액
		$bank_code    = "";                                    // 은행코드
		/* = -------------------------------------------------------------------------- = */
		$st_cd        = "";                                    // 구매확인 코드
		$can_msg      = "";                                    // 구매취소 사유
		/* = -------------------------------------------------------------------------- = */
		$waybill_no   = "";                                    // 운송장 번호
		$waybill_corp = "";                                    // 택배 업체명
		/* = -------------------------------------------------------------------------- = */
		$cash_a_no    = "";                                    // 현금영수증 승인번호
		$cash_a_dt    = "";                                    // 현금영수증 승인시간
		
		if (empty($tno)) exit('tno value empty');
		
		/* = -------------------------------------------------------------------------- = */
		/* =   02-1. 가상계좌 입금 통보 데이터 받기                                     = */
		/* = -------------------------------------------------------------------------- = */
		if ( $tx_cd == "TX00" )
		{
			$ipgm_name = $this->input->post_get('ipgm_name', TRUE);                // 주문자명
			$remitter  = $this->input->post_get('remitter', TRUE);               // 입금자명
			$ipgm_mnyx = $this->input->post_get('ipgm_mnyx', TRUE);                // 입금 금액
			$bank_code = $this->input->post_get('bank_code', TRUE);                // 은행코드
			$account   = $this->input->post_get('account', TRUE);                // 가상계좌 입금계좌번호
			$op_cd     = $this->input->post_get('op_cd', TRUE);                // 처리구분 코드
			$noti_id   = $this->input->post_get('noti_id', TRUE);               // 통보 아이디
			$cash_a_no = $this->input->post_get('cash_a_no', TRUE);                // 현금영수증 승인번호
			$cash_a_dt = $this->input->post_get('cash_a_dt', TRUE);                // 현금영수증 승인시간
		}
		
		/* = -------------------------------------------------------------------------- = */
		/* =   02-2. 가상계좌 환불 통보 데이터 받기                                     = */
		/* = -------------------------------------------------------------------------- = */
		else if ( $tx_cd == "TX01" )
		{
			$refund_nm  = $this->input->post_get('refund_nm', TRUE);               // 환불계좌주명
			$refund_mny = $this->input->post_get('refund_mny', TRUE);               // 환불금액
			$bank_code  = $this->input->post_get('bank_code', TRUE);               // 은행코드
		}
		/* = -------------------------------------------------------------------------- = */
		/* =   02-3. 구매확인/구매취소 통보 데이터 받기                                  = */
		/* = -------------------------------------------------------------------------- = */
		else if ( $tx_cd == "TX02" )
		
			$st_cd = $this->input->post_get('st_cd', TRUE);                          // 구매확인 코드
		
			if ( strtoupper($st_cd) == "N"  )                                // 구매확인 상태가 구매취소인 경우
			{
				//PG사에서 넘어오는 사유의 문자인코딩 확인필요
				$can_msg = $this->input->post_get('can_msg', TRUE);               // 구매취소 사유
			}
		
			/* = -------------------------------------------------------------------------- = */
			/* =   02-4. 배송시작 통보 데이터 받기                                           = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX03" )
			{
		
				$waybill_no   = $this->input->post_get('waybill_no', TRUE);          // 운송장 번호
				$waybill_corp = $this->input->post_get('waybill_corp', TRUE);           // 택배 업체명
			}
		
			/* ============================================================================== */
			/* =   03. 공통 통보 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.      = */
			/* = -------------------------------------------------------------------------- = */
			/* =   통보 결과를 DB 작업 하는 과정에서 정상적으로 통보된 건에 대해 DB 작업에  = */
			/* =   실패하여 DB update 가 완료되지 않은 경우, 결과를 재통보 받을 수 있는     = */
			/* =   프로세스가 구성되어 있습니다.                                            = */
			/* =                                                                            = */
			/* =   * DB update가 정상적으로 완료된 경우                                     = */
			/* =   하단의 [04. result 값 세팅 하기] 에서 result 값의 value값을 0000으로     = */
			/* =   설정해 주시기 바랍니다.                                                  = */
			/* =                                                                            = */
			/* =   * DB update가 실패한 경우                                                = */
			/* =   하단의 [04. result 값 세팅 하기] 에서 result 값의 value값을 0000이외의   = */
			/* =   값으로 설정해 주시기 바랍니다.                                           = */
			/* = -------------------------------------------------------------------------- = */
		
			/* = -------------------------------------------------------------------------- = */
			/* =   03-1. 가상계좌 입금 통보 데이터 DB 처리 작업 부분                        = */
			/* = -------------------------------------------------------------------------- = */
			if ( $tx_cd == "TX00" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-2. 가상계좌 환불 통보 데이터 DB 처리 작업 부분                        = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX01" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-3. 구매확인/구매취소 통보 데이터 DB 처리 작업 부분                    = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX02" )
			{
				$confirmYn = 'Y';
				if ( $st_cd = "N"  )                                // 구매확인 상태가 구매취소인 경우
				{
					$confirmYn = 'N';
				}
				//$can_msg = iconv("EUC-KR", "UTF-8", $can_msg);
				$upData = array(
					'tno' => $tno,
					'ORDERCONFIRM_YN' => $confirmYn,
					'ORDERCONFIRM_DATE' => date('Y-m-d H:i:s'),
					'ORDERCONFIRM_MSG' => $can_msg
				);
				$result = $this->order_model->setPGOrderConfirmData($upData);
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-4. 배송시작 통보 데이터 DB 처리 작업 부분                             = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX03" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-5. 정산보류 통보 데이터 DB 처리 작업 부분                             = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX04" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-6. 즉시취소 통보 데이터 DB 처리 작업 부분                             = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX05" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-7. 취소 통보 데이터 DB 처리 작업 부분                                 = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX06" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-8. 발급계좌해지 통보 데이터 DB 처리 작업 부분                         = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX07" )
			{
			}
			/* = -------------------------------------------------------------------------- = */
			/* =   03-9. 모바일안심결제 통보 데이터 DB 처리 작업 부분                       = */
			/* = -------------------------------------------------------------------------- = */
			else if ( $tx_cd == "TX08" )
			{
			}
			/* ============================================================================== */
			
			
			$insData = array(
				'site_cd' =>	$site_cd,
				'tno' => 		$tno,
				'order_no' => 	$order_no,
				'tx_cd' => 		$tx_cd,
				'tx_tm' => 		$tx_tm,
				'ipgm_name' => 	$ipgm_name,
				'remitter' => 	$remitter,
				'ipgm_mnyx' => 	$ipgm_mnyx,
				'bank_code' => 	$bank_code,
				'account' => 	$account,
				'op_cd' => 		$op_cd,
				'noti_id' => 	$noti_id,
				'cash_a_no' => 	$cash_a_no,
				'cash_a_dt' => 	$cash_a_dt,
				'refund_nm' => 	$refund_nm,
				'refund_mny' => $refund_mny,
				'bank_code' => 	$bank_code,
				'st_cd' => 		$st_cd,
				'can_msg' => 	$can_msg,
				'waybill_no' => $waybill_no,
				'waybill_corp' => $waybill_corp					
			);			
			
			$result = $this->order_model->setPGReturnData($insData);
		
		
			/* ============================================================================== */
			/* =   04. result 값 세팅 하기                                                  = */
			/* ============================================================================== */
			
			echo '<html><body><form><input type="hidden" name="result" value="0000"></form></body></html>';
	}
}