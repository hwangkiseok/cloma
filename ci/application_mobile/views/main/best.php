<div class="main_product_wrap">

    <div class="product_list_half box no-before">
        <div class="box-in">

            <? foreach ($aProductLists as $kk => $rr) {
                $aListImage = $rr['p_today_image'];//json_decode($r['today_image'],true)[0];
                $no = (int)$kk+1;
                ?>
                <div class="product_part" onclick="go_product('<?=$rr['p_num']?>','thema');" role="button" <?if($kk > 0){?>style="padding-top: 8px;" <?}?>>

                    <div class="img_l">
                        <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" style="" />
                        <a class="best-no no-font"><?=$no?></a>
                    </div>

                    <div class="img_r">

                        <ul>
                            <li class="img_r_pname"><?=$rr['p_name']?></li>
                            <?if(empty($rr['p_summary']) == false){?><li class="img_r_psummary"><?=nl2br($rr['p_summary'])?></li><?}?>
                            <li class="img_r_price_tit">
                                <span class="tit">옷쟁이들 쇼핑가</span> <!--<span class="delivery_f">무료배송</span>-->
                            </li>
                            <li class="img_r_price">
                                <em class="no_font"><?=number_format($rr['p_sale_price'])?></em>원
                            </li>
                        </ul>

                    </div>

                    <div class="clear"></div>
                </div>
            <?}?>
        </div>
    </div>

</div>
<div class="clear"></div>


<input type="hidden" name="best_code" value="<?=$req['best_code']?>" title="카테고리코드" />
<input type="hidden" name="page" value="2" title="페이지" />
<input type="hidden" name="more" value="<? if($req['page'] == $total_page){?>0<?} else{?>1<? } ?>" title="리스트가 더 있는지 여부" />

<script type="text/javascript">

    function move_tap(obj){

        var best_code = $(obj).data('best');
        $('.depth3nav a').removeClass('active');
        $('.depth3nav a[data-best="'+best_code+'"]').addClass('active');
        $('input[name="best_code"]').val(best_code);
        ajaxPaging(true);

        window.history.replaceState( {} , 'Best', '/Best?best_code=' + best_code );

    }

    $(document).on('click','.depth3nav a[data-best]',function(){
        move_tap($(this));
    });

    $(function(){

        var best_code = $('input[name="best_code"]').val();

        $('.depth3nav a[data-best="'+best_code+'"]').addClass('active');

        $('.depth3nav a[data-best]').on('click',function(){
            move_tap($(this));
        });

    });

    var ajax_on  = false;
    var obj_name = 'main_product_wrap';

    // $(window).scroll(function(){
    //
    //     var more = $('input[name="more"]').val();
    //
    //     if(more == 0) return false; //리스트 end
    //     if(ajax_on == true ) return false; //ajax 중인경우 return
    //
    //     ajax_on = true;
    //
    //     var x = parseInt($(this).scrollTop());
    //     var h = parseInt($('body').height()) - 200;
    //     var chkH =  parseInt($(window).outerHeight(true)) ;
    //
    //     if( h < x +chkH ) ajaxPaging();
    //
    //     ajax_on = false;
    //
    // });

    function ajaxPaging(b = false){

        var best_code = $('input[name="best_code"]').val();
        var p = $('input[name="page"]').val();

        $.ajax({
            url : '/product/list_ajax',
            data : {page : p , best_code : best_code , list_type : 'best' },
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

