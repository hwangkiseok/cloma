<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="google-site-verification" content="85cqipFDlt7hvvbYNc7r463d_qrBtaF-nuks5F5eb9k" />

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

    <!-- site content -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $meta_content_array['og_title']; ?>">
    <meta property="og:image" content="<?php echo $meta_content_array['og_image']; ?>">
    <meta property="og:description" content="<?php echo $this->config->item("site_description"); ?>">
    <meta name="description" content="<?php echo $this->config->item("site_description"); ?>">
    <!-- // site content -->

    <title><?php echo $meta_content_array['title']; ?></title>

    <!--<link href="http://fonts.googleapis.com/earlyaccess/notosanskr.css" rel="stylesheet" type="text/css" />--><!-- Noto Sans KR / 100, 300, 400, 500, 700, 900 -->
    <link href="/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/css/bootstrap-custom.min.css" rel="stylesheet" />
    <link href="/css/jquery-confirm.min.css" rel="stylesheet" />
    <link href="/css/swiper.min.css" rel="stylesheet" />

    <?php link_src_html("/css/normalize.css", "css"); ?>
    <?php link_src_html("/css/common.css", "css"); ?>

    <?php if( !$this->agent->is_mobile() ) { ?>
        <?php // link_src_html("/css/pc.css", "css"); ?>
    <?php } ?>

    <script src="/js/jquery-2.2.0.min.js" type="text/javascript"></script>
    <script src="/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="/js/jquery.form.min.js" type="text/javascript"></script>
    <script src="/js/bootstrap-custom.min.js" type="text/javascript"></script>
    <script src="/html/swiper/swiper.min.js"></script>

    <?php link_src_html("/js/common.js", "js"); ?>
    <?php link_src_html("/js/front.js", "js"); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <!--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>-->
    <!--<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>-->
    <![endif]-->


    <!-- set cont -->
    <script type="text/javascript">

        var isAjaxErrorAlert = true;

    </script>