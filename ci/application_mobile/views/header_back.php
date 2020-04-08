<style type="text/css">
    #header nav.depth1 a
    , .header_fixed nav.depth1 a { width: calc(100% / <?=count($depth1_nav)?>); }
    .depth3nav {border-top:1px solid #ddd;}
</style>

<div id="wrapper">

    <div id="header">

        <header>
            <!-- top header -->

            <span class="header-L">
                <a class="back" role="button" onclick="go_back();"></a>
<!--                <a class="search" href="#none" onclick="view_search();"></a>-->
            </span>
            <a href="#none" onclick="go_link('/');"><h3><?=$title?></h3></a>
            <span class="header-R">
                <a class="cart" href="#none" onclick="go_link('/cart')"></a>
                <a class="wish" href="#none" onclick="go_link('/wish')"></a>
            </span>

            <div class="clear"></div>
        </header>

        <!-- navigation -->
        <?if(count($depth1_nav) > 0){ // type 1 (main)?>
            <nav class="depth1">
                <? foreach ($depth1_nav as $k => $r) {?>
                    <a href="#none" onclick="go_link('<?=$r['url']?>')" class="swiper-slide <?=$r['active']?>" ><?=$r['name']?></a>
                <? }?>
            </nav>
            <div class="clear"></div>

            <?if(count($depth3_nav) > 0){ // NAV1 에 종속되는 NAV ?>
                <nav class="depth3 depth3nav">
                    <div class="swiper-wrapper">
                        <? foreach ($depth3_nav as $k => $r) { if(empty($set_k) == true) $set_k = ($r['active']=='active')?$k:''; ?>
                            <a href="#none" onclick="go_link('<?=$r['url']?>')" class="swiper-slide <?=$r['active']?>" ><?=$r['name']?></a>
                        <?}?>
                    </div>
                </nav>
                <div class="clear"></div>
            <?}?>

        <?}?>

        <?if(count($depth2_nav) > 0){ // type 2 (sub page) ?>
            <nav class="depth2 depth2nav">
                <div class="swiper-wrapper">
                    <? foreach ($depth2_nav as $k => $r) { if(empty($set_k) == true) $set_k = ($r['active']=='active')?$k:''; ?>
                        <a href="#none" onclick="go_link('<?=$r['url']?>')" class="swiper-slide <?=$r['active']?>" ><?=$r['name']?></a>
                    <?}?>
                </div>
            </nav>
            <div class="clear"></div>
        <?}?>

        <!-- end of navigation -->

    </div>

    <script type="text/javascript">

        $(function(){

            <?if(count($depth2_nav) > 0){?>

            var depth2_cnt = <?=count($depth2_nav) > 4 ? 4 : count($depth2_nav) ?> ;
            var depth2nav = new Swiper ('.depth2nav', {
                slidesPerView: depth2_cnt
            });

            <?if(empty($set_k) == false){ $set_k = $set_k > 0 ? (int)$set_k-1 : $set_k; ?>
            depth2nav.slideTo('<?=$set_k?>',100);
            <?}?>

            <?}?>

            <?if(count($depth3_nav) > 0){?>

            var depth3_cnt = <?=count($depth3_nav) > 6 ? 6 : count($depth3_nav) ?> ;
            var depth3nav = new Swiper ('.depth3nav', {
                slidesPerView: depth3_cnt
            });

            <?}?>

        });

    </script>

    <div class="header_fixed"></div>

    <section id="container">