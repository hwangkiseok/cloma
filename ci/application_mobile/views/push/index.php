<div class="box no-before">
    <div class="box-in push" style="padding-top:18px;">

        <? if(count($aPushLists) < 1 ){?>

            <div class="push_area">
                <div class="cont" style="text-align: center;padding: 10px 0;">받은 알림 메시지가 없습니다.</div>
            </div>

        <?}else{ ?>

            <? foreach ($aPushLists as $r) {
                $list_image = json_decode($r['p_rep_image'] , true)[0];
                ?>

                <div class="push_area" data-seq="<?=$r['ap_pnum']?>" role="button">
                    <div class="img">
                        <img src="<?=$list_image?>" alt="img1" />
                    </div>
                    <div class="cont">
                        <div class="text"><?=nl2br($r['ap_list_comment'])?></div>
                        <div class="btm">
                            <span class="fl link">
                                <?if(empty($r['ap_list_btn_msg']) == false){?>
                                    <?=$r['ap_list_btn_msg']?>
                                <?}else{?>
                                    상품바로가기
                                <?}?>
                            </span>
                            <span class="fr date no_font"><?=view_date_format($r['ap_reserve_datetime'])?></span>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>

            <? }?>

        <?}?>

    </div>
</div>

<script type="text/javascript">
    $(function(){

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.push').css({'min-height': min_height+'px' , 'background' : 'f1f1f1'});
        }

        $('.push_area').on('click',function(){
            var seq = $(this).data('seq');
            go_product(seq,'push');
        });
        window.history.replaceState({} , '', window.location.pathname);
        $('#container').css('background','#eff0f4');


    });
</script>