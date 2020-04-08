<div class="box">
    <div class="box-in search" style="padding: 0;">
        <form name="srh_form" method="get" action="/search" onsubmit="return goSearch();">
            <div class="sub_srh_area">
                <button style="border: 0;padding: 0;"><i class="search-icon" onclick="goSearch();"></i></button>
                <input type="text" class="srh_input" name="kwd" value="<?=$req['kwd']?>">
                <input type="hidden" name="kfd" value="p_name">
            </div>
        </form>
        <p class="result_noti"><span class="result_cnt"><?=$req['kwd']?></span> 로 검색결과 [<?=$list_count['cnt']?>] 개 상품이 검색되었습니다.</p>
    </div>
</div>

<div class="product_list_half box">
    <div class="box-in search_list">

        <? if(count($aProductLists) > 0){?>

            <? foreach ($aProductLists as $k => $r) {
                $aListImage = $r['p_today_image'];//json_decode($r['today_image'],true)[0];
                ?>
                <div class="product_part" onclick="go_product('<?=$r['p_num']?>','search');" role="button" <?if($k > 0){?>style="padding-top: 8px;" <?}?>>

                    <div class="img_l">
                        <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" style="" />
                    </div>

                    <div class="img_r">

                        <ul>
                            <li class="img_r_pname"><?=$r['p_name']?></li>
                            <?if(empty($r['p_summary']) == false){?><li class="img_r_psummary"><?=nl2br($r['p_summary'])?></li><?}?>
                            <li class="img_r_price_tit">
                                <span class="tit"></span> <span class="delivery_f">무료배송</span>
                            </li>
                            <li class="img_r_price">
                                <em class="no_font"><?=number_format($r['p_sale_price'])?></em>원
                            </li>
                        </ul>

                    </div>

                    <div class="clear"></div>
                </div>

                <!--
                    <?if(in_array($r['p_num'],$aWishLists)){?>
                        <button role="button" onclick="setWish('<?=$r['p_num']?>',$(this));" class="active">찜해제</button>
                    <?}else{?>
                        <button role="button" onclick="setWish('<?=$r['p_num']?>',$(this));" class="">찜하기</button>
                    <?}?>
                    -->
            <?}?>

        <?} else{?>

            <p>검색결과가 없습니다.</p>

        <?} ?>

    </div>

</div>

<input type="hidden" name="page" value="2" title="페이지" />
<input type="hidden" name="more" value="<? if($req['page'] == $total_page){?>0<?} else{?>1<? } ?>" title="리스트가 더 있는지 여부" />

<script>

    /* scrolling paging */
    var ajax_on  = false;
    var obj_name = 'search_list';
    $(window).scroll(function(){

        var more = $('input[name="more"]').val();

        if(more == 0) return false; //리스트 end
        if(ajax_on == true ) return false; //ajax 중인경우 return

        ajax_on = true;

        var x = parseInt($(this).scrollTop());
        var h = parseInt($('body').height()) - 200;
        var chkH =  parseInt($(window).outerHeight(true)) ;

        if( h < x +chkH ) ajaxPaging();

        ajax_on = false;

    });

    function ajaxPaging(b = false){

        var p = $('input[name="page"]').val();
        isShowLoader = false;

        $.ajax({
            url : '<?=$this->page_link->list_ajax?>',
            data : {page : p , kwd : $('input[name="kwd"]').val() , kfd : $('input[name="kfd"]').val() },
            type : 'post',
            async : false,
            dataType : 'html',
            success : function(result) {
                if(b == true) $('.'+obj_name).html(result);
                else $('.'+obj_name).append(result);
                $('input[name="page"]').val(parseInt(p) + 1);
            }

        });

    };

</script>


