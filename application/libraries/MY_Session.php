<?
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * CI Session Class Extension for AJAX calls.
 * api ajax콜이 빈번할 경우 session이 사라지는 문제 처리
 * http://codeigniter-kr.org/qna/view/11279/page/1/q/ajax 내용참고
 *
 * @author : Administrator
 * @date    : 2015. 12. 21.
 * @version:
 */
class MY_Session extends CI_Session {
    /**
     * sess_update()
     *
     * Do not update an existing session on ajax or xajax calls
     *
     * @access    public
     * @return    void
     */
    public function sess_update()
    {
        $CI = get_instance();

        if ( ! $CI->input->is_ajax_request())
        {
            parent::sess_update();
        }
    }

} 
?>