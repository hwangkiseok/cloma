
<style type="text/css">
    #header nav.depth1 a, .header_fixed nav.depth1 a { width: calc(100% / <?=count($depth1_nav)?>); }
</style>

<div id="wrapper">
    <?=$lnb_menu?>
    <?=$rnb_menu?>

    <div id="header">
        <header>
            <!-- top header -->            <span class="header-L">
<!--                <a onclick="side_show('l');"><i class="fas fa-bars"></i></a>-->
                <a class="bars" onclick="side_show('l');"></a>
                <a class="search" href="#none" onclick="view_search();"></a>
            </span>
            <a class="header-title" href="#none" onclick="go_link('/');">
                <h3><?=$this->config->item('site_name_kr');?></h3>
            </a>
            <span class="header-R">
                <a class="cart" href="#none" onclick="go_link('/cart')"></a>
                <a class="wish" href="#none" onclick="go_link('/wish')"></a>
<!--                <a onclick="side_show('r');"><i class="fas fa-user-alt"></i></a>-->
            </span>

            <form name="srh_form" method="get" action="/search" onsubmit="return goSearch();">
                <div class="srh_area">
                    <button style="border: 0;padding: 0;"><i class="search-icon" onclick="goSearch();"></i></button>
                    <input type="hidden" name="kfd" value="p_name">
                    <input type="text" class="srh_input fl" name="kwd" autocomplete="off">
                </div>
            </form>
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
                            <a href="#none" onclick="go_link('<?=$r['url']?>')"  <?if(empty($r['best_code'] == false)){?>data-best="<?=$r['best_code']?>"<?}?> <?if(empty($r['ctgr_code'] == false)){?>data-ctgr="<?=$r['ctgr_code']?>"<?}?> class="swiper-slide <?=$r['active']?>" ><?=$r['name']?></a>
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

        <?if(count($depth4_nav) > 0){ // type 4 (sort area) ?>

            <nav class="depth4 depth4nav box" style="">
                <div class="box-in" >
                <? foreach ($depth4_nav as $k => $r) {  ?>
                    <?if($r['name'] == 'icon'){?>

                        <?if($r['use_flag'] == 'Y'){?>
                            <a href="#none" class="fr" style="margin-right: 15px;" onclick="go_link('<?=$r['url']?>')" class="<?=$r['active']?>" ><?=$r['name']?></a>
                        <?}?>

                    <?}else{?>
                        <a href="#none" onclick="go_link('<?=$r['url']?>')" class="<?=$r['active']?>" ><?=$r['name']?></a>
                    <?}?>

                <? } ?>
                    <div class="clear"></div>
                </div>
            </nav>
            <div class="clear"></div>

        <?}?>

        <!-- end of navigation -->

    </div>
    <div class="clear"></div>
    <script type="text/javascript">

        $(function(){

            <?if(count($depth2_nav) > 0){?>

            var depth2_cnt = <?=count($depth2_nav) > 4 ? 4 : count($depth2_nav) ?> ;
            var depth2nav = new Swiper ('.depth2nav', {
                slidesPerView: depth2_cnt
            });

            <?if(empty($set_k) == false){?>
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