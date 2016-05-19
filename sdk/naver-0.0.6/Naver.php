<?
/**
*	Naver 로그인 Api Class 0.07
*   class : NaverAPI
*   Author : Rawady corp. Jung Jintae
*   date : 2015.12.14
*	https://github.com/rawady/NaverLogin


	! required PHP 5.x Higher
	! required curl enable

*
*   본 클래스는 네이버 공식 라이브러리가 아닙니다.
*  NHN API Reference : http://developer.naver.com/wiki/pages/NaverLogin_Web



The MIT License (MIT)

Copyright (c) 2014 Jung Jintae

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.


*/



/**
 *
 0.07 변경

	- 로그인/로그아웃 버튼 이미지 링크수정
	- 토큰 자동갱신



 0.06 변경

	- ssl 오류 수정
	- 일부 치명적인 오류 수정



 0.4 포함됨

	- 인증 요청
	- 엑세스토큰 획득
	- 사용자 정보 취득
	- 로그아웃

*/

class Naver{
	
	const NAVER_OAUTH_URL = 'https://nid.naver.com/oauth2.0/';

	private $tokenDatas	=	array();

	private $access_token			= '';			// oauth 엑세스 토큰
	private $refresh_token			= '';			// oauth 갱신 토큰
	private $access_token_type		= '';			// oauth 토큰 타입
	private $access_token_expire	= '';			// oauth 토큰 만료


	private $client_id		= '';			// 네이버에서 발급받은 클라이언트 아이디
	private $client_secret	= '';			// 네이버에서 발급받은 클라이언트 시크릿키

	private $returnURL		= '';			// 콜백 받을 URL ( 네이버에 등록된 콜백 URI가 우선됨)
	private $state			= '';			// 네이버 명세에 필요한 검증 키 (현재 버전 라이브러리에서 미검증)


	private $loginMode		= 'request';	// 라이브러리 작동 상태

	private $returnCode		= '';			// 네이버에서 리턴 받은 승인 코드
	private $returnState	 = '';			// 네이버에서 리턴 받은 검증 코드

	private $nhnConnectState	= false;

	private $curl = NULL;
	private $refreshCount = 1;  // 토큰 만료시 갱신시도 횟수

	function __construct($argv = array()) {

		if  ( ! in_array  ('curl', get_loaded_extensions())) {
			echo 'curl required';
			return false;
		}


		if($argv['CLIENT_ID']){
			$this->client_id = trim($argv['CLIENT_ID']);
		}

		if($argv['CLIENT_SECRET']){
			$this->client_secret = trim($argv['CLIENT_SECRET']);
		}

		if($argv['RETURN_URL']){
			$this->returnURL = trim(urlencode($argv['RETURN_URL']));
		}		
	}

	/**
	 * @method name : loginString
	 * 
	 * 
	 * @param string $retUrl 로그인후 돌아갈 webpage url
	 * @return string
	 */
	function loginString($retUrl)
	{
		$this->generate_state();

		if ($retUrl != '')
		{
			$str = Naver::NAVER_OAUTH_URL.'authorize?client_id='.$this->client_id.'&response_type=code&redirect_uri='.$this->returnURL.'/return_url/'.$retUrl.'&state='.$this->state;			
		}
		else 
		{
			$str = Naver::NAVER_OAUTH_URL.'authorize?client_id='.$this->client_id.'&response_type=code&redirect_uri='.$this->returnURL.'&state='.$this->state;			
		}
		return $str;
	}
	
	function logoutString($thisUrl)
	{
		return $thisUrl;
	}

	function logout(){
		$data = array();
		$this->refreshCount = 1;

		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, Naver::NAVER_OAUTH_URL.'token?client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&grant_type=delete&refresh_token='.$this->refresh_token.'&sercive_provider=NAVER');
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false );
		$retVar = curl_exec($this->curl);
		curl_close($this->curl);
	}


	function getUserProfile($code, $state){

		$this->generate_state();
		
		$this->loginMode = 'request_token';
		$this->returnCode = $code;
		$this->returnState = $state;
		
		$this->_getAccessToken();
		
		$data = array();
		$data['Authorization'] = $this->access_token_type.' '.$this->access_token;

		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, 'https://apis.naver.com/nidlogin/nid/getUserProfile.xml');
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
			'Authorization: '.$data['Authorization']
		));
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false );		
		$retVar = curl_exec($this->curl);
		curl_close($this->curl);
		
		$retVar = simplexml_load_string($retVar, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($retVar);
		$arrResult = json_decode($json, TRUE);		
		//echo var_dump($arrResult);

		if ($arrResult['result']['resultcode'] == '00')
		{
			//success
			
			$arrResult['access_token'] = $this->access_token;
			$arrResult['access_token_type'] = $this->access_token_type;
			$this->updateConnectState(TRUE);
		}
		/*인증절차 재진행 실패 (소용없음)
		else 
		{
			if($arrResult['result']['resultcode'] == "024"){	//
			
				if($this->refreshCount > 0){
					//Authentication failed 인증에 실패 
					$this->refreshCount--;
					$this->_refreshAccessToken();
					$this->getUserProfile($this->returnCode, $this->returnState);
				}else{
					return $arrResult;
				}
			}
		}
		*/
		
		return $arrResult;
	}


	/**
	*	 네이버 연결상태를 반환합니다.
	*    엑세스 토큰 발급/저장이 이루어진 후 connected 상태가 됩니다.
	*/
	function getConnectState(){
		return $this->nhnConnectState;
	}

	private function updateConnectState($strState = ''){
		$this->nhnConnectState = $strState;
	}

	private function _getAccessToken(){
		$data = array();
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, Naver::NAVER_OAUTH_URL.'token?client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&grant_type=authorization_code&code='.$this->returnCode.'&state='.$this->returnState);
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false );		
		$retVar = curl_exec($this->curl);
		curl_close($this->curl);
		$NHNreturns = json_decode($retVar, FAlSE);
		
		if(isset($NHNreturns->access_token)){
			$this->access_token			= $NHNreturns->access_token;
			$this->access_token_type	= $NHNreturns->token_type;
			$this->refresh_token		= $NHNreturns->refresh_token;
			$this->access_token_expire	= $NHNreturns->expires_in;

			$this->updateConnectState(true);
		}
	}

	private function _refreshAccessToken(){
		$data = array();
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, Naver::NAVER_OAUTH_URL.'token?client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&grant_type=refresh_token&refresh_token='.$this->refresh_token);
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false );		
		$retVar = curl_exec($this->curl);
		curl_close($this->curl);
		$NHNreturns = json_decode($retVar, FALSE);

		if(isset($NHNreturns->access_token)){
			$this->access_token			= $NHNreturns->access_token;
			$this->access_token_type	= $NHNreturns->token_type;
			$this->access_token_expire	= $NHNreturns->expires_in;

			$this->updateConnectState(true);
		}
	}

	private function generate_state() {
    	$mt = microtime();
		$rand = mt_rand();
		$this->state = md5( $mt . $rand );
  	}
}
