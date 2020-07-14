

<?if(count($delivery_list) > 0){ ?>

    <? foreach ($delivery_list as $kk => $rr) { ?>

        <div class="sub_prod_list delivery_row">

            <div class="order-tit_v2" >
            <span>
                <span><em class="no_font"><?=date('Y/m/d',strtotime($rr[0]['register_date']))?></em></span>
                <em class="no_font fr" style="color: #aaa;"><?=$kk?></em>
            </span>
            </div>

            <? foreach ($rr as $k => $r) { $aListImage = $r['img_url']; ?>
                <div <?if($k > 0){?>style="margin-top: 15px;"<?}?>>
                    <div class="img fl">
                        <a class="zs-cp" onclick="go_product('<?=$r['p_num']?>','deliveryList')">
                            <img src="<?=$aListImage?>" width="100%" alt="<?=$r['item_name']?>" style="" />
                        </a>
                    </div>
                    <div class="cont fl">
                        <ul>
                            <li class="p_name"><a class="zs-cp" onclick="go_product('<?=$r['p_num']?>','deliveryList')"><?=$r['item_name']?></a></li>
                            <li>결제금액 : <em class="no_font"><?=number_format($r['buy_amt'])?></em> 원</li>
                            <li>수량 : <em class="no_font"><?=number_format($r['buy_count'])?></em> 개</li>

                            <?if(empty($r['option_info']) == false){?>
                                <li class="opt" style="margin-bottom: 10px;">
                                    <span class="fl">옵션&nbsp;:&nbsp;</span>
                                    <span class="fl">
                                <? foreach ($r['option_info'] as $kk => $rr) {?>
                                    <?=$kk > 0 ? '<br>' : ''?><?=$rr['option_name']?>
                                <?}?>
                            </span>
                                    <div class="clear"></div>
                                </li>
                            <?}?>

                            <li class="btn_area">

                                <?if(empty($aOrderCancelLists[$r['trade_no']]) == false){?>

                                    <? if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '166'){?>
                                        <a class="btn btn-border-red btn-thin fl"><?=$this->config->item($aOrderCancelLists[$r['trade_no']]['after_status_cd'],'form_status_cd')?></a>
                                    <?}else if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '167'
                                        || $aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '168'
                                    ){?>
                                        <a class="btn btn-border-purple btn-thin fl"><?=$this->config->item($aOrderCancelLists[$r['trade_no']]['after_status_cd'],'form_status_cd')?></a>
                                    <?}else{?>

                                        <?if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '66'){?>
                                            <a class="btn btn-border-blue btn-thin fl" >취소처리</a>
                                        <?}else if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '67'){?>
                                            <a class="btn btn-border-blue btn-thin fl" >교환처리</a>
                                        <?}else if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '68'){?>
                                            <a class="btn btn-border-blue btn-thin fl" >반품처리</a>
                                        <?}?>

                                        <!--<a class="btn btn-border-blue btn-thin fl" ><?=$this->config->item($aOrderCancelLists[$r['trade_no']]['after_status_cd'],'form_status_cd')?> </a>-->
                                    <?}?>


                                <?}else{ //취소정보가 없는경우?>

                                    <? if($r['status_cd'] == '66'){?>
                                        <a class="btn btn-border-red btn-thin fl"><?=$this->config->item($r['status_cd'],'form_status_cd')?></a>
                                    <?}else if($r['status_cd'] == '67' || $r['status_cd'] == '68'){?>
                                        <a class="btn btn-border-purple btn-thin fl"><?=$this->config->item($r['status_cd'],'form_status_cd')?></a>
                                    <?} else{?>
                                        <a class="btn btn-border-blue btn-thin fl" ><?=$this->config->item($r['status_cd'],'form_status_cd')?> </a>
                                    <?}?>

                                <?}?>
                                <a role="button" style="font-weight: normal;" href="#none" onclick="go_link('/delivery/detail/?tn=<?=$r['trade_no']?>');" class="btn btn-gray btn-thin fl">상세보기</a>
                                <div class="clear"></div>
                            </li>

                        </ul>

                    </div>
                    <div class="clear"></div>
                </div>

            <?}?>


        </div>
    <? } ?>

<?}?>