<div class="main_product_wrap">

    <? foreach ($aProductLists as $k => $r) {
        //$aListImage = json_decode($r['p_rep_image'],true)[0];
        $aListImage = $r['p_today_image'];
        ?>

        <div class="main_product_list arrange_2 box no-before">
            <div class="box-in">

                <div onclick="go_product('<?=$r['p_num']?>','fashion');" style="display: block" role="button">
                    <div class="tit">
                        <p class="p_name"><?=$r['p_name']?></p>
                        <?if(empty($r['p_summary']) == false){?><p class="p_summary"><?=nl2br($r['p_summary'])?></p><?}?>
                    </div>
                    <img src="<?=$aListImage?>" alt="<?=$r['p_name']?>" />
                    <div class="p_info">
                        <ul>
                            <!--<li>[<em class="no_font"><?=$r['p_discount_rate']?></em>]<em class="no_font"><?=$r['p_sale_price']?></em></li>-->
<!--                            <li class="tit_2">-->
<!--                                <p><b>[--><?//=$r['p_cate1']?><!--]</b></p>-->
<!--                            </li>-->
                            <li class="tit_2">
                                <p class="p_name"><?=$r['p_name']?></p>
                            </li>
                            <li class="price_info">
                                <a><span style="letter-spacing: -0.2px;"><em class="no_font"><?=number_format($r['p_sale_price'])?></em></span>원</a>
                                <div class="main_buy_btn_area"><button class="main_buy_btn">구매하기</button></div>
                            </li>
                            <li class="delivery_info"><span class="f">무료배송</span></li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    <?}?>

</div>
<div class="clear"></div>

<input type="hidden" name="ctgr_code" value="<?=$req['ctgr_code']?>" title="카테고리코드" />
<input type="hidden" name="page" value="2" title="페이지" />
<input type="hidden" name="more" value="<? if($req['page'] == $total_page){?>0<?} else{?>1<? } ?>" title="리스트가 더 있는지 여부" />

<script type="text/javascript">

    function move_tap(obj){

        var ctgr_code = $(obj).data('ctgr');

        $('.depth3nav a').removeClass('active');
        $('.depth3nav a[data-ctgr="'+ctgr_code+'"]').addClass('active');
        $('input[name="ctgr_code"]').val(ctgr_code);
        $('input[name="more"]').val(1);
        $('input[name="page"]').val(2);
        $(window).scrollTop(0);

        //탭이동시 page값 1로 초기화
        $('input[name="page"]').val(1);

        ajaxPaging(true);

        window.history.replaceState( {} , 'Fashion', '/Fashion?ctgr_code=' + ctgr_code );

    }

    $(document).on('click','.depth3nav a[data-ctgr]',function(){
        move_tap($(this));
    });


    $(function(){

        var ctgr_code = $('input[name="ctgr_code"]').val();

        $('.depth3nav a[data-ctgr="'+ctgr_code+'"]').addClass('active');

        $('.depth3nav a[data-ctgr]').on('click',function(){
            move_tap($(this));
        });

    });

    var ajax_on  = false;
    var obj_name = 'main_product_wrap';

    $(window).scroll(function(){

        var more = $('input[name="more"]').val();

        if(more == 0) return false; //리스트 end
        if(ajax_on == true ) return false; //ajax 중인경우 return

        ajax_on = true;

        var x = parseInt($(this).scrollTop());
        var h = parseInt($('body').height()) - 600;
        var chkH =  parseInt($(window).outerHeight(true)) ;

        if( h < x +chkH ) ajaxPaging();

        ajax_on = false;

    });

    function ajaxPaging(b = false){

        var ctgr_code = $('input[name="ctgr_code"]').val();
        var p = $('input[name="page"]').val();

        $.ajax({
            url : '/product/list_ajax',
            data : {page : p , ctgr_code : ctgr_code , list_type : 'fashion' },
            type : 'post',
            async : false,
            dataType : 'html',
            success : function(result) {
                if(b == true) $('.'+obj_name).html(result);
                else $('.'+obj_name).append(result);
                $('input[name="page"]').val(parseInt(p) + 1);
            }

        });

    }

</script>

