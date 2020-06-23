
<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="google-site-verification" content="85cqipFDlt7hvvbYNc7r463d_qrBtaF-nuks5F5eb9k" />
    <meta name="naver-site-verification" content="4c1e9a0caeee53994d1dfae8d1261bc290a1fb8d" />

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="https://m.cloma.co.kr/images/favicon/57_57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="https://m.cloma.co.kr/images/favicon/60_60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="https://m.cloma.co.kr/images/favicon/72_72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="https://m.cloma.co.kr/images/favicon/76_76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="https://m.cloma.co.kr/images/favicon/114_114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="https://m.cloma.co.kr/images/favicon/120_120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="https://m.cloma.co.kr/images/favicon/144_144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="https://m.cloma.co.kr/images/favicon/152_152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="https://m.cloma.co.kr/images/favicon/180_180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="https://m.cloma.co.kr/images/favicon/192_192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://m.cloma.co.kr/images/favicon/32_32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="https://m.cloma.co.kr/images/favicon/96_96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://m.cloma.co.kr/images/favicon/16_16.png">
    <!--    <link rel="manifest" href="/manifest.json">-->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="https://m.cloma.co.kr/images/favicon/114_114.png">
    <meta name="theme-color" content="#ffffff">
    <!-- // favicon -->
    <meta property="og:url" content="https://m.cloma.co.kr" />
    <meta property="og:type" content="website">
    <meta property="og:title" content="옷쟁이들">
    <meta property="og:image" content="https://m.cloma.co.kr/images/og_image.png?t=1592527723">
    <meta property="og:description" content="">
    <meta property="description" content="">


    <title>옷쟁이들(2)</title>

    <link href="/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/css/bootstrap-custom.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700&amp;display=swap" rel="stylesheet">

    <link href="/css/normalize.css?v=1578549502" rel="stylesheet" />    <link href="/css/common_m.css?v=1592444271" rel="stylesheet" />
    <link href="/css/pc.css?v=1590993774" rel="stylesheet" />
    <script src="/js/jquery-2.2.0.min.js" type="text/javascript"></script>
    <script src="/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="/js/jquery.form.min.js" type="text/javascript"></script>
    <script src="/js/bootstrap-custom.min.js" type="text/javascript"></script>

    <script src="/js/common.js?v=1591166847" type="text/javascript"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <!--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>-->
    <!--<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>-->
    <![endif]-->

    <style type="text/css">
        .overlay {background: rgba(0,0,0,0.3);position: fixed;top: 0;left: 0;width: 100%;height: 100%;}
        .cont { text-align: center; position: fixed;top: 50px;width: 80%;  max-width: 420px;;background: #fff;border: 1px solid #f1f1f1;border-radius: 5px;padding: 15px 15px 20px 15px;; }
        .cont img{width: 60px;margin: 10px 0 20px 0;border-radius: 5px}
        .cont button{background: #fff;color: #333;font-size: 16px;border: 1px solid #333;padding: 10px 15px;border-radius: 5px;margin-top: 20px;font-weight: bold;}
        .cont button:hover{background: #ddd}
        .show_mobile {display: none;}
    </style>

</head>
<body>

    <div class="overlay"></div>
    <div class="cont">

        <img src="https://m.cloma.co.kr/images/favicon/144_144.png" alt="logo" />
        <p>요청하신 서비스는 옷쟁이들 앱에서만</p>
        <p>이용하실 수 있습니다.</p>
        <p class="show_mobile"><br>지금 바로 앱으로 이동 하시겠어요?</p>
        <button class="go_app show_mobile">앱으로 바로가기</button>

    </div>

    <script type="text/javascript">

        function chk_m(){

            var filter = "win16|win32|win64|mac";
            if(navigator.platform){
                if( 0 > filter.indexOf(navigator.platform.toLowerCase()) ) return true;
                else return false;
            }
        }

        $(function(){

            if(chk_m() == true) $('.show_mobile').show();

            var l = ( parseInt(screen.availWidth) - parseInt($('.cont').width()) - 30 ) / 2;
            $('.cont').css('left',l+'px');

            $('.go_app').on('click',function(e){
                e.preventDefault();
                location.href = 'https://bit.ly/2USpcvJ';
            });

        });

    </script>

</body>
</html>