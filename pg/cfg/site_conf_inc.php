<?
    /* ============================================================================== */
    /* =   PAGE : ���� ���� ȯ�� ���� PAGE                                          = */
    /* =----------------------------------------------------------------------------= */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do			        = */
    /* =----------------------------------------------------------------------------= */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* = �� ���� ��                                                                 = */
    /* = * g_conf_home_dir ���� ����                                                = */
    /* =----------------------------------------------------------------------------= */
    /* =   BIN ���� ��� �Է� (bin������ ����						                = */
    /* ============================================================================== */
    $g_conf_home_dir  = "	/var/www/html/pg";       // BIN ������ �Է� (bin������) 
    
    /* ============================================================================== */
    /* = �� ���� ��                                                                 = */
    /* = * g_conf_gw_url ����                                                       = */
    /* =----------------------------------------------------------------------------= */
    /* = �׽�Ʈ �� : testpaygw.kcp.co.kr�� ������ �ֽʽÿ�.                         = */
    /* = �ǰ��� �� : paygw.kcp.co.kr�� ������ �ֽʽÿ�.                             = */
    /* ============================================================================== */
    //$g_conf_gw_url    = "testpaygw.kcp.co.kr";
    $g_conf_gw_url    = "paygw.kcp.co.kr";

    /* ============================================================================== */
    /* = �� ���� ��                                                                 = */
    /* = * g_conf_log_path ���� ����                                                = */
    /* =----------------------------------------------------------------------------= */
    /* =   log ��� ����                                                            = */
    /* ============================================================================== */
    $g_conf_log_path = "/var/www/pglog";

    /* ============================================================================== */
    /* = �� ���� ��                                                                 = */
    /* = * g_conf_js_url ����                                                       = */
    /* =----------------------------------------------------------------------------= */
	/* = �׽�Ʈ �� : src="http://pay.kcp.co.kr/plugin/payplus_test.js"              = */
	/* =             src="https://pay.kcp.co.kr/plugin/payplus_test.js"             = */
    /* = �ǰ��� �� : src="http://pay.kcp.co.kr/plugin/payplus.js"                   = */
	/* =             src="https://pay.kcp.co.kr/plugin/payplus.js"                  = */
    /* =                                                                            = */
	/* = �׽�Ʈ ��(UTF-8) : src="http://pay.kcp.co.kr/plugin/payplus_test_un.js"    = */
	/* =                    src="https://pay.kcp.co.kr/plugin/payplus_test_un.js"   = */
    /* = �ǰ��� ��(UTF-8) : src="http://pay.kcp.co.kr/plugin/payplus_un.js"         = */
	/* =                    src="https://pay.kcp.co.kr/plugin/payplus_un.js"        = */
    /* ============================================================================== */
    //$g_conf_js_url    = "https://pay.kcp.co.kr/plugin/payplus_test_un.js";
	$g_conf_js_url    = "https://pay.kcp.co.kr/plugin/payplus_un.js";

    /* ============================================================================== */
    /* = ����Ʈ�� SOAP ��� ����                                                     = */
    /* =----------------------------------------------------------------------------= */
    /* = �׽�Ʈ �� : KCPPaymentService.wsdl                                         = */
    /* = �ǰ��� �� : real_KCPPaymentService.wsdl                                    = */
    /* ============================================================================== */
    //$g_wsdl           = "KCPPaymentService.wsdl";
	$g_wsdl           = "real_KCPPaymentService.wsdl";

    /* ============================================================================== */
    /* = g_conf_site_cd, g_conf_site_key ����                                       = */
    /* = �ǰ����� KCP���� �߱��� ����Ʈ�ڵ�(site_cd), ����ƮŰ(site_key)�� �ݵ��   = */
    /* = ������ �ּž� ������ ���������� ����˴ϴ�.                                = */
    /* =----------------------------------------------------------------------------= */
    /* = �׽�Ʈ �� : ����Ʈ�ڵ�(T0000)�� ����ƮŰ(3grptw1.zW0GSo4PQdaGvsF__)��      = */
    /* =            ������ �ֽʽÿ�.                                                = */
	/* = ����ũ�� �׽�Ʈ ��: ����Ʈ�ڵ�(T0007)�� ����ƮŰ(4Ho4YsuOZlLXUZUdOxM1Q7X__)= */
    /* =            ������ �ֽʽÿ�.                                                = */
    /* = �ǰ��� �� : �ݵ�� KCP���� �߱��� ����Ʈ�ڵ�(site_cd)�� ����ƮŰ(site_key) = */
    /* =            �� ������ �ֽʽÿ�.                                             = */
    /* ============================================================================== */
    //$g_conf_site_cd   = "T0007";
    //$g_conf_site_key  = "4Ho4YsuOZlLXUZUdOxM1Q7X__";
    $g_conf_site_cd   = "D8267";
    $g_conf_site_key  = "2kG.FJgImVek3v5IPmv-HqT__";

    /* ============================================================================== */
    /* = g_conf_site_name ����                                                      = */
    /* =----------------------------------------------------------------------------= */
    /* = ����Ʈ�� ����(�ѱ� �Ұ�) : �ݵ�� �����ڷ� �����Ͽ� �ֽñ� �ٶ��ϴ�.       = */
    /* ============================================================================== */
    //$g_conf_site_name = "KCP TEST SHOP";
	$g_conf_site_name = "CIRCUSFLAG SHOP";

    /* ============================================================================== */
    /* = ���� ������ �¾� (���� �Ұ�)                                               = */
    /* ============================================================================== */
    $g_conf_log_level = "3";
    $g_conf_gw_port   = "8090";        // ��Ʈ��ȣ(����Ұ�)
	$module_type      = "01";          // ����Ұ�
?>