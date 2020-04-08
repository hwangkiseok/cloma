<?php link_src_html("/plugins/icheck/skins/square/blue.css", "css"); ?>
<?php link_src_html("/plugins/icheck/icheck.min.js", "js"); ?>
<style>
    #container .box:last-of-type:after{ display: block; content: ""; height: 8px;_box-shadow: 0 3px 2px 0 rgba(0,0,0,0.1) inset;background: #f1f1f1 }
</style>
<div class="wish_wrap" style="height: 100%;">
<?if($list_count['cnt'] > 0){?>

    <?foreach ($wish_prod_list as $k => $r) { //zsView($r);//$aListImage = json_decode($r['p_rep_image'],true)[0];
        $aListImage = $r['p_today_image'];
        $p_price = $r['p_sale_price'];

    ?>

        <div class="box">

            <div class="box-in">

                <?if($k == 0){?>

                <div class="ctrl-line">
                    <span class="fl" style="margin-left: 7px;"><input type="checkbox" id="all_check" />&nbsp;&nbsp;전체선택</span>
                    <span class="fr" style="margin-right: 7px;"><a class="del" href="#none" onclick="chkDel();"><i></i>선택삭제</a></span>
                    <div class="clear"></div>
                </div>

                <?}?>

                <div class="sub_prod_list">

                    <div class="chk fl"><input type="checkbox" class="num_check" value="<?=$r['p_num']?>" /></div>
                    <div class="img fl"><img src="<?=$aListImage?>" width="100%" /></div>
                    <div class="cont fl">
                        <ul>
                            <li><?=$r['p_name']?></li>
                            <li><em class="no_font"><?=number_format($p_price)?>원</em></li>
                            <li><a class="btn btn-default wide" href="#none" onclick="go_product('<?=$r['p_num']?>','wish');">상세보기</a></li>
                        </ul>
                    </div>

                    <div class="clear"></div>

                </div>


            </div>
            <?if($k == count($wish_prod_list)-1){?>
<!--                <div style="display: block; content: ''; height: 8px;_box-shadow: 0 3px 2px 0 rgba(0,0,0,0.1) inset;background: #f1f1f1 "></div>-->
            <?}?>
        </div>

    <?}?>

<?}else{?>

<div class="box">

    <div class="box-in">

        <p style="text-align: center;line-height: 40px;height: 40px;">찜한 상품이 없습니다.</p>

    </div>
</div>

<?}?>

</div>
<script type="text/javascript">

    $(function(){

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.wish_wrap').css({'min-height': min_height+'px'});
        }

        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue'
        });
        $('#all_check').on('ifClicked',function(){
            chkAll();
        });
    });

    function chkAll(){

        if( !$('.num_check').length ) {
            return false;
        }

        var checked = $('#all_check').prop('checked');
        if( checked ) {
            $('.num_check').iCheck('uncheck');
        }
        else {
            $('.num_check').iCheck('check');
        }

    }

    function chkDel(){

        if( !$('.num_check:checked').length ) {
            alert('삭제할 상품을 선택해주세요 !');
            return false;
        }

        var arr = [];

        $.each($('.num_check:checked') , function(){
            arr.push($(this).val());
        });

        // show_loader();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {p_num:arr},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    location.reload();
                }
            },
            complete : function() {
                hide_loader();
            }
        });

    }

</script>
