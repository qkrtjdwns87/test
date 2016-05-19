<?
    /* ============================================================================== */
    /* =   PAGE : ����ũ�� ����Ȯ�� �� ��� ��û �� ��� ó�� PAGE                  = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   ȯ�� ���� ���� Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ʼ�                                                                  = */
    /* =   �׽�Ʈ �� �ǰ��� ������ site_conf_inc.php������ �����Ͻñ� �ٶ��ϴ�.     = */
    /* = -------------------------------------------------------------------------- = */

    include "../cfg/site_conf_inc.php";        // ȯ�漳�� ���� include
    require "pp_cli_hub_lib.php";              // library [�����Ұ�]

    /* = -------------------------------------------------------------------------- = */
    /* =   ȯ�� ���� ���� Include END                                               = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   POST ���� üũ�κ�                                                       = */
    /* = -------------------------------------------------------------------------- = */
    if ( $_SERVER['REQUEST_METHOD'] != "POST" )
    {
        echo("�߸��� ��η� �����Ͽ����ϴ�.");
        exit;
    }
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   01. ������ ��� ��û ���� ����                                           = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx            = $_POST[ "req_tx"           ]; // ��û����
    $cust_ip           = getenv( "REMOTE_ADDR"      ); // ��û IP
    $tran_cd           = "";
    $res_cd            = "";                                                       // �����ڵ�
    $res_msg           = "";                                                       // ����޽���
    /* ============================================================================== */
    $mod_type          = $_POST[ "mod_type"         ]; // ������� 
    $tno               = $_POST[ "tno"              ]; // �ŷ���ȣ
    $mod_desc          = $_POST[ "mod_desc"         ]; // ��һ���
    $mod_depositor     = $_POST[ "mod_depositor"    ]; // ȯ�Ұ����ָ�(ȯ�ҽÿ��� ���)
    $mod_account       = $_POST[ "mod_account"      ]; // ȯ�Ұ��¹�ȣ(ȯ�ҽÿ��� ���)
    $mod_bankcode      = $_POST[ "mod_bankcode"     ]; // ȯ�������ڵ�(ȯ�ҽÿ��� ���)
    $mod_sub_type      = $_POST[ "mod_sub_type"     ]; // ��һ󼼱���
    $sub_mod_type      = $_POST[ "sub_mod_type"     ]; // �������
    /* ============================================================================== */
    $vcnt_yn           = $_POST[ "vcnt_yn"          ]; // ���º���� ������ü, ������� ����
    /* = -------------------------------------------------------------------------- = */
    $y_rem_mny         = $_POST[ "rem_mny"          ]; // ȯ�� ���� �ݾ�
    $y_mod_mny         = $_POST[ "mod_mny"          ]; // ȯ�� �ݾ�
    $y_tax_mny         = $_POST[ "tax_mny"          ]; // �κ���� �����ݾ�
    $y_free_mod_mny    = $_POST[ "free_mod_mny"     ]; // �κ���� ������ݾ�
    $y_add_tax_mny     = $_POST[ "add_tax_mny"      ]; // �κ���� �ΰ��� �ݾ�
    $y_refund_account  = $_POST[ "a_refund_account" ]; // ȯ�Ұ��¹�ȣ
    $y_refund_nm       = $_POST[ "a_refund_nm"      ]; // ȯ�Ұ����ָ�
    $y_bank_code       = $_POST[ "a_bank_code"      ]; // �����ڵ�
    $y_mod_desc_cd     = $_POST[ "mod_desc_cd"      ]; // ��ұ���
    $y_mod_desc        = $_POST[ "mod_desc"         ]; // ��һ���
    /* = -------------------------------------------------------------------------- = */
    /* =   01. ������ ��� ��û ���� ���� END                                       = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   02. �ν��Ͻ� ���� �� �ʱ�ȭ(���� �Ұ�)                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =               ������ �ʿ��� �ν��Ͻ��� �����ϰ� �ʱ�ȭ �մϴ�.             = */
    /* =               �� ���� �� �� �κ��� �������� ���ʽÿ�                       = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus = new C_PP_CLI;
    $c_PayPlus->mf_clear();
    /* ------------------------------------------------------------------------------ */
    /* =   02. �ν��Ͻ� ���� �� �ʱ�ȭ END                                          = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. ó�� ��û ���� ����                                                  = */
    /* = -------------------------------------------------------------------------- = */
    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. ����ũ�� ���º��� ��û                                             = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "mod_escrow" )
    {
        $c_PayPlus->mf_set_modx_data( "tno",        $_POST[ "tno"       ] );      // KCP ���ŷ� �ŷ���ȣ
        $c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip              );      // ���� ��û�� IP
        $c_PayPlus->mf_set_modx_data( "mod_desc",   $_POST[ "mod_desc"  ] );      // ���� ����

        if( $mod_type == "STE9_C"  || $mod_type == "STE9_CP" ||
            $mod_type == "STE9_A"  || $mod_type == "STE9_AP" ||
            $mod_type == "STE9_AR" || $mod_type == "STE9_V"  ||
            $mod_type == "STE9_VP" )
        {
            $tran_cd = "70200200";
            $c_PayPlus->mf_set_modx_data( "mod_type"    , "STE9"         );
            $c_PayPlus->mf_set_modx_data( "mod_desc_cd" , $y_mod_desc_cd );
            $c_PayPlus->mf_set_modx_data( "mod_desc"    , $y_mod_desc    );

            if( $mod_type == "STE9_C" )
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STSC"            );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC03"          );
            }
            else if( $mod_type == "STE9_CP" )
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STPC"            );
                $c_PayPlus->mf_set_modx_data( "part_canc_yn"    , "Y"               );
                $c_PayPlus->mf_set_modx_data( "rem_mny"         , $y_rem_mny        );
                $c_PayPlus->mf_set_modx_data( "amount"          , $y_mod_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC03"          );
                //$c_PayPlus->mf_set_modx_data( "tax_flag"        , "TG03"            ); // ���հ��� �κ����
                //$c_PayPlus->mf_set_modx_data( "mod_tax_mny"     , $y_tax_mny        ); // ���ް� �κ���� �ݾ�
                //$c_PayPlus->mf_set_modx_data( "mod_free_mny"    , $y_free_mod_mny   ); // ����� �κ���� �ݾ�
                //$c_PayPlus->mf_set_modx_data( "mod_vat_mny"     , $y_add_tax_mny    ); // �ΰ��� �κ���� �ݾ�
            }
            else if( $mod_type == "STE9_A")
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STSC"            );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC03"          );
            }
            else if( $mod_type == "STE9_AP")
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STPC"            );
                $c_PayPlus->mf_set_modx_data( "part_canc_yn"    , "Y"               );
                $c_PayPlus->mf_set_modx_data( "rem_mny"         , $y_rem_mny        );
                $c_PayPlus->mf_set_modx_data( "amount"          , $y_mod_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC04"          );
                //$c_PayPlus->mf_set_modx_data( "tax_flag"        , "TG03"            ); // ���հ��� �κ����
                //$c_PayPlus->mf_set_modx_data( "mod_tax_mny"     , $y_tax_mny        ); // ���ް� �κ���� �ݾ�
                //$c_PayPlus->mf_set_modx_data( "mod_free_mny"    , $y_free_mod_mny   ); // ����� �κ���� �ݾ�
                //$c_PayPlus->mf_set_modx_data( "mod_vat_mny"     , $y_add_tax_mny    ); // �ΰ��� �κ���� �ݾ�
            }
            else if( $mod_type == "STE9_AR")
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STHD"            );
                $c_PayPlus->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC04"          );
                $c_PayPlus->mf_set_modx_data( "mod_bankcode"    , $y_bank_code      );
                $c_PayPlus->mf_set_modx_data( "mod_account"     , $y_refund_account );
                $c_PayPlus->mf_set_modx_data( "mod_depositor"   , $y_refund_nm      );
            }
            else if( $mod_type == "STE9_V")
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STHD"            );
                $c_PayPlus->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC00"          );
                $c_PayPlus->mf_set_modx_data( "mod_bankcode"    , $y_bank_code      );
                $c_PayPlus->mf_set_modx_data( "mod_account"     , $y_refund_account );
                $c_PayPlus->mf_set_modx_data( "mod_depositor"   , $y_refund_nm      );
            }
            else if( $mod_type == "STE9_VP")
            {
                $c_PayPlus->mf_set_modx_data( "sub_mod_type"    , "STPD"            );
                $c_PayPlus->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
				$c_PayPlus->mf_set_modx_data( "rem_mny"         , $y_rem_mny        );
                $c_PayPlus->mf_set_modx_data( "mod_sub_type"    , "MDSC04"          );
                $c_PayPlus->mf_set_modx_data( "mod_bankcode"    , $y_bank_code      );
                $c_PayPlus->mf_set_modx_data( "mod_account"     , $y_refund_account );
                $c_PayPlus->mf_set_modx_data( "mod_depositor"   , $y_refund_nm      );
                //$c_PayPlus->mf_set_modx_data( "tax_flag"        , "TG03"            ); // ���հ��� �κ����           
                //$c_PayPlus->mf_set_modx_data( "mod_tax_mny"     , $y_tax_mny        ); // ���ް� �κ���� �ݾ�
                //$c_PayPlus->mf_set_modx_data( "mod_free_mny"    , $y_free_mod_mny   ); // ����� �κ���� �ݾ�
                //$c_PayPlus->mf_set_modx_data( "mod_vat_mny"     , $y_add_tax_mny    ); // �ΰ��� �κ���� �ݾ�
                $c_PayPlus->mf_set_modx_data( "part_canc_yn"    , "Y"               );
            }
        }
        else
        {
            $tran_cd = "00200000";

            $c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type                           );      // ���ŷ� ���� ��û ����

            if ( $mod_type == "STE1")                                                                  // ���º��� Ÿ���� [��ۿ�û]�� ���
            {
                $c_PayPlus->mf_set_modx_data( "deli_numb", $_POST[ "deli_numb" ] );      // ����� ��ȣ
                $c_PayPlus->mf_set_modx_data( "deli_corp", $_POST[ "deli_corp" ] );      // �ù� ��ü��
            }
            if ( $mod_type == "STE2" || $mod_type == "STE4" )                                       // ���º��� Ÿ���� [������] �Ǵ� [���]�� ������ü, ��������� ���
            {
                if ( $vcnt_yn == "Y" )
                {
                    $c_PayPlus->mf_set_modx_data( "refund_account", $mod_account    );  // ȯ�Ҽ�����¹�ȣ
                    $c_PayPlus->mf_set_modx_data( "refund_nm",      $mod_depositor  );  // ȯ�Ҽ�������ָ�
                    $c_PayPlus->mf_set_modx_data( "bank_code",      $mod_bankcode      );  // ȯ�Ҽ��������ڵ�
                }
            }
        }
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03. ����ũ�� ���º��� ��û END                                           = */
    /* = -------------------------------------------------------------------------- = */
     
    /* ============================================================================== */
    /* =   04. ����                                                                 = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tran_cd != "" )
    {

        $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                              $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, $g_conf_log_level, 0, 0, $g_conf_log_path ); // ���� ���� ó��

        $res_cd  = $c_PayPlus->m_res_cd;  // ��� �ڵ�
        $res_msg = $c_PayPlus->m_res_msg; // ��� �޽���

    }
    else
    {
        $c_PayPlus->m_res_cd  = "9562";
        $c_PayPlus->m_res_msg = "���� ����|Payplus Plugin�� ��ġ���� �ʾҰų� tran_cd���� �������� �ʾҽ��ϴ�.";
    }
    
    /* = -------------------------------------------------------------------------- = */
    /* =   04. ���� END                                                             = */
    /* ============================================================================== */


    /* ================================================================================== */
    /* =   05.����Ȯ�� �� ��� ���� ��� ó��										    = */
    /* = ------------------------------------------------------------------------------ = */
    if ( $req_tx == "mod" )
    {
        if( $res_cd == "0000" )
        {
        } // End of [res_cd = "0000"]


    /* ================================================================================== */
    /* =   05.����Ȯ�� �� ��� ���� ��� ó��                                          = */
    /* ================================================================================== */
        else
        {
        }
    } // End of Process


    //* ============================================================================= */
    /* =   05. �� ���� �� ��������� ȣ��                                           = */
    /* = -------------------------------------------------------------------------- = */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <title>*** KCP [AX-HUB Version] ***</title>
        <script type="text/javascript">
            function goResult()
            {
                document.mod_info.submit();
                openwin.close();
            }

            // ���� �� ���ΰ�ħ ���� ���� ��ũ��Ʈ
            function noRefresh()
            {
                /* CTRL + NŰ ����. */
                if ((event.keyCode == 78) && (event.ctrlKey == true))
                {
                    event.keyCode = 0;
                    return false;
                }
                /* F5 ��Ű ����. */
                if(event.keyCode == 116)
                {
                    event.keyCode = 0;
                    return false;
                }
            }
            document.onkeydown = noRefresh ;
        </script>
    </head>

    <body onload="goResult();" >
    <form name="mod_info" method="post" action="./result.php">
        <input type="hidden" name="res_cd"            value="<?= $res_cd ?>">           <!-- ��� �ڵ� -->
        <input type="hidden" name="res_msg"           value="<?= $res_msg?>">           <!-- ��� �޼��� -->
    </form>
    </body>
</html>

