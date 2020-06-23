<div class="box no-before">
    <div class="box-in push-top">
        <div class="date_set">
            <ul>
                <li class="active" data-type="info">쇼핑 정보</li>
                <li data-type="product">할인/이벤트 정보</li>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
</div>

<div class="box no-before">

    <div class="box-in push">

        <? if(count($aInfoLists) < 1 ){?>

            <div class="push_area">
                <div class="cont" style="text-align: center;padding: 10px 0;">받은 알림 메시지가 없습니다.</div>
            </div>

        <?}else{ ?>

            <? foreach ($aInfoLists as $r) { ?>

                <div class="push_area info_list" data-seq="<?=$r['seq']?>" data-loc_type="<?=$r['loc_type']?>" role="button">
                    <div class="cont">
                        <div class="text <?if($r['view_flag'] == 'N'){?>on<?}?>"><?=$r['noti_subject']?></div>
                        <div class="btm">
                            <span class="fl link <?if($r['view_flag'] == 'N'){?>on<?}?>"> <?=$r['noti_content']?> </span>
                            <span class="fr date no_font"><?=view_date_format($r['reg_date'])?></span>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>

            <? }?>

        <?}?>

    </div>
</div>

<? link_src_html('/js/page/push.js','js'); ?>