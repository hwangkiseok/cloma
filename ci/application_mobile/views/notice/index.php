<!--
<div class="box">
    <div class="box-in">
        <div class="page_tit" style="padding-right:20px;padding-left:20px">
            <h2 style="text-align: left;"> 공지사항 </h2>
            <p style="text-align: left;"><?=$this->config->item('site_name_kr')?> 소식의 중요사항을 안내해드립니다.</p>
        </div>
    </div>
</div>
-->
<div class="box no-before">
    <div class="box-in board" style="background: #f1f1f1;">

        <? if(count($notice_list) < 1 ){?>

            <div class="board_area">
                <div class="cont" style="text-align: center">등록된 공지사항이 없습니다.</div>
            </div>

        <?}else{ ?>

            <? foreach ($notice_list as $r) {?>
            <div class="board_area">
                <p class="tit"><?=$r['bh_subject']?></p>
                <div class="cont"><?=$r['bh_content']?></div>
                <p class="dateNbtn">
                    <span class="date fl no_font"><?=view_date_format($r['bh_regdatetime'])?></span>
                    <span class="btn fr view">자세히보기</span>
                    <span class="btn fr close">줄이기</span>
                </p>
                <div class="clear"></div>
            </div>
            <?}?>

        <?} ?>
    </div>

</div>

<script>
    $(function(){

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.board').css({'min-height': min_height+'px' , 'background' : 'f1f1f1'});
        }

        var img_m_w = 664;
        var tit_w = parseInt($(window).outerWidth(true)) - ((16+12)*2);//- $('#footer').outerHeight(true);
        if(tit_w > img_m_w) tit_w = img_m_w;

        $('.board_area .tit').css('width' , tit_w+'px');
        $('.board_area .cont').css('width' , tit_w+'px');

        $('.board_area .cont').each(function(){

            if($(this).height() < 66){
                $(this).parent().find('.btn').hide();
            }

        });

        $(window).on('resize', function(){
            var tit_w = parseInt($(window).outerWidth(true)) - ((16+12)*2);//- $('#footer').outerHeight(true);
            if(tit_w > img_m_w) tit_w = img_m_w;
            $('.board_area .tit').css('width' , tit_w+'px');
            $('.board_area .cont').css('width' , tit_w+'px');
        });

        $('.board_area .btn').on('click',function(){
            var t_obj = $(this).parent().parent();
           if( $(t_obj).hasClass('active') == false ){
               $(t_obj).addClass('active');
           }else{
               $(t_obj).removeClass('active');
           };
        });

    });
</script>