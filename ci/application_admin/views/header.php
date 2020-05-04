<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php
    //모바일버전일때
    if( empty(get_cookie("cki_pageview_mode")) || get_cookie("cki_pageview_mode") == "mobile" ) { ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <?php }//endif; ?>

    <meta http-equiv="cache-control" content="no-cache, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="Expires" content="Mon, 08 Sep 2003 10:10:10 GMT">

    <title><?php echo $this->config->item('site_name_kr');?> : 통합관리자</title>

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?=IMG_HTTP?>/images/favicon/57_57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?=IMG_HTTP?>/images/favicon/60_60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?=IMG_HTTP?>/images/favicon/72_72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?=IMG_HTTP?>/images/favicon/76_76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?=IMG_HTTP?>/images/favicon/114_114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?=IMG_HTTP?>/images/favicon/120_120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?=IMG_HTTP?>/images/favicon/144_144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?=IMG_HTTP?>/images/favicon/152_152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=IMG_HTTP?>/images/favicon/180_180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?=IMG_HTTP?>/images/favicon/192_192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=IMG_HTTP?>/images/favicon/32_32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?=IMG_HTTP?>/images/favicon/96_96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=IMG_HTTP?>/images/favicon/16_16.png">
    <!--    <link rel="manifest" href="/manifest.json">-->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?=IMG_HTTP?>/images/favicon/114_114.png">
    <meta name="theme-color" content="#ffffff">
    <!-- // favicon -->

    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/css/metisMenu.min.css" rel="stylesheet">
    <link href="/css/sb-admin-2.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="/plugins/pace/pace-theme-flash.css" rel="stylesheet">
    <link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />
    <?php echo link_src_html("/css/admin.css", "css"); ?>

    <script>
        //global
        var site_domain = '<?php echo $this->config->item('site_domain');?>';
        var site_domain_cookie = '<?php echo $this->config->item('site_domain_cookie');?>';
        var sidebar_yn = true;
        var status_code = [];
        status_code['success'] = '<?php echo get_status_code('success'); ?>';
        status_code['noauth'] = '<?php echo get_status_code('noauth'); ?>';
        status_code['error'] = '<?php echo get_status_code('error'); ?>';
        var isAjaxErrorAlert = true;

        var img_http = '<?=IMG_HTTPS;?>';

    </script>

    <script src="/js/jquery-1.11.3.min.js" type="text/javascript"></script>
    <script src="/js/jquery.form.min.js" type="text/javascript"></script>
    <script src="/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/js/metisMenu.min.js" type="text/javascript"></script>
    <script src="/js/sb-admin-2.js" type="text/javascript"></script>
    <script src="/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="/plugins/datepicker/bootstrap-datepicker.js" charset="utf-8"></script>
    <script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js" charset="utf-8"></script>

    <?php echo link_src_html("/js/common.js", "js"); ?>
    <?php echo link_src_html("/js/admin.js", "js"); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<!--     20190926 컬러변경-->
    <style>
        .for_color ul li a{color:#312b2b;}
        .for_color ul li a:hover{color:#312b2b;}
    </style>
</head>
<body>

<?php if( !$no_header ) { ?>

<div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle2">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
<!--            <a class="navbar-brand" href="/" style="color:#c9302c; padding:14px 15px "><img src="--><?//=IMG_HTTP?><!--/images/logo_myth.png" style="width:100px;" alt=""></a>-->
            <a class="navbar-brand" href="/" style="color:#c9302c;"><?php echo $this->config->item('site_name_kr');?></a>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            <li>
                <?php
                if(isset($_SESSION['GroupMemberName']) == true && isset($_SESSION['GroupMemberId']) == true) {
                    echo $_SESSION['GroupMemberName'].' ('.$_SESSION['GroupMemberId'].') 로그인 중';
                }else{
                    echo $_SESSION['session_au_name'].' ('.$_SESSION['session_au_loginid'].') 로그인 중';
                }
                ?>

            </li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="/adminuser/update"><i class="fa fa-user fa-fw"></i> 계정관리</a></li>
                    <!--<li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>-->
                    <li class="divider"></li>
                    <li><a href="/auth/logout"><i class="fa fa-sign-out fa-fw"></i> 로그아웃</a></li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav in" id="side-menu">

                        <?if($_SESSION['session_au_level'] == '3'){?>

                            <li>
                                <a href="/proposal"><i class="fa fa-dashboard fa-fw"></i> 외부상품 제안서</a>
                            </li>

                        <?}else{?>
                            <li>
                                <a href="/"><i class="fa fa-dashboard fa-fw"></i> Home<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="/total_stat">통계</a></li>
                                    <!--<li><a href="#none">출석체크관리</a></li>-->
                                </ul>
                            </li>

                            <li>
                                <a href="/member/list"><i class="fa fa-user fa-fw"></i> 회원관리<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="/member/list">회원관리</a></li>
                                    <li><a href="/kakao_member/list">카카오채널 회원관리</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="/order/cancel_list"><i class="fa fa-file-o fa-fw"></i> 주문관리<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="/order/cancel_list">주문취소관리</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="/product"><i class="fa fa-cubes fa-fw"></i> 상품관리<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="/product/list">상품목록</a></li>
                                    <li><a href="/product_md/list">상품MD관리</a></li>
                                    <li><a href="/main_thema/lists">메인테마관리</a></li>
                                    <li><a href="/category_md/list">카테고리MD관리</a></li>
                                    <li><a href="/special_offer/lists">기획전관리</a></li>
<!--                                    <li><a href="/restock/lists">재입고푸시관리</a></li>-->
                                    <li><a href="/product_rel/list">연관상품 관리</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-edit fa-fw"></i> 일반관리<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="/board_help/list">게시물관리</a></li>
                                    <li><a href="/board_qna/list">1:1문의관리</a></li>
                                    <li><a href="/comment/list">댓글관리</a></li>
<!--                                    <li><a href="/comment_report/list">댓글신고관리</a></li>-->
<!--                                    <li><a href="/banner/list">배너관리</a></li>-->
<!--                                    <li><a href="/popup/list">팝업관리</a></li>-->
                                    <li><a href="/banned_words/list">금칙어관리</a></li>
<!--                                    <li><a href="/review/list">구매평 관리</a></li>-->
                                </ul>
                            </li>
                            <li>
                                <a href="/common/list"><i class="fa fa-gear fa-fw"></i> 공통관리</a>
                            </li>
<!--                            <li>-->
<!--                                <a href="#"><i class="fa fa-gift fa-fw"></i> 이벤트<span class="fa arrow"></span></a>-->
<!--                                <ul class="nav nav-second-level collapse">-->
<!--                                    <li><a href="/event/list">이벤트 목록</a></li>-->
<!--                                    <li><a href="/event_active/list">이벤트참여 목록</a></li>-->
                                    <!--                                <li><a href="/everyday/list">매일응모 목록</a></li>-->
                                    <!--                                <li><a href="/everyday_active/list">매일응모참여 목록</a></li>-->
                                    <!--                                <li><a href="/event_gift/list">기프티콘 관리</a></li>-->
<!--                                </ul>-->
<!--                            </li>-->

                            <li>
                                <a href="#"><i class="fa fa-android fa-fw"></i> APP<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="/app_version/list">버전관리</a></li>
                                    <li><a href="/app_push/list">푸시관리</a></li>
<!--                                    <li><a href="/app_push/stat">푸시통계</a></li>-->
                                    <li><a href="/app_splash/list">스플래시관리</a></li>
<!--                                    <li><a href="/app_statusbar/list">상태바색상관리</a></li>-->
                                    <li><a href="/app_popup/list">메인팝업관리</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="/offer/list"><i class="fa fa-gear fa-fw"></i> 대량구매문의</a>
                            </li>

                            <li>
                                <a href="/proposal"><i class="fa fa-dashboard fa-fw"></i> 외부상품 제안서</a>
                            </li>



                            <!--                        <li>-->
                            <!--                            <a href="#"><i class="fa fa-adn fa-fw"></i> 카카오광고<span class="fa arrow"></span></a>-->
                            <!--                            <ul class="nav nav-second-level collapse">-->
                            <!--                                <li><a href="/kakao_product/list">상품관리</a></li>-->
                            <!--                            </ul>-->
                            <!--                        </li>-->
                            <!--                        <li>-->
                            <!--                            <a href="#"><i class="fa fa-adn fa-fw"></i> 카카오스토리<span class="fa arrow"></span></a>-->
                            <!--                            <ul class="nav nav-second-level collapse">-->
                            <!--                                <li><a href="/kstory_product/list">상품관리</a></li>-->
                            <!--                            </ul>-->
                            <!--                        </li>-->
<!--                            <li>-->
<!--                                <a href="#"><i class="fa fa-suitcase fa-fw"></i> 제휴사<span class="fa arrow"></span></a>-->
<!--                                <ul class="nav nav-second-level collapse">-->
<!--                                    <li><a href="/company/list">외부광고</a></li>-->
<!--                                </ul>-->
<!--                            </li>-->
                            <li class="active">
                                <a href="#"><i class="fa fa-external-link fa-fw"></i> 링크<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <li><a href="https://analytics.google.com/analytics/web/?hl=ko#/" target="_blank">Google
                                            Analytics</a></li>
                                    <li><a href="#">Google Play</a></li>
                                    <li><a href="https://play.google.com/apps/publish/?hl=ko" target="_blank">Google Dev
                                            Console</a></li>
                                    <li><a href="#" target="_blank">홈페이지</a></li>
                                    <li><a href="#none"
                                           onclick="new_win_open('<?php echo $this->config->item("order_list_url"); ?>', 'order_win', 800, 800);">주문조회</a>
                                    </li>
                                    <li><a href="https://appstoreconnect.apple.com" target="_blank">APP Store Connect</a>
                                    </li>
                                </ul>
                            </li>

                            <?php if (is_adminuser_high_auth()) { ?>
                                <li>
                                    <a href="/adminuser/list"><i class="fa fa-gears fa-fw"></i> 관리자계정관리</a>
                                </li>
                            <?php } ?>
                            <li>
<!--                                <a href="/cdn_purge"><i class="fa fa-gears fa-fw"></i> CDN Purge--><!--</a>-->
                            </li>

                        <?}?>

                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
        <!-- /.navbar-static-side -->
    </nav>
    <!-- /.navbar -->

    <div id="page-wrapper">
<?php }//end of if( no_header ) ?>