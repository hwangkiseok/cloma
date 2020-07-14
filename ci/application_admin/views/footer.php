<?php if ( !$no_footer ) { ?>
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<style>
    #stock_pop {display: none; position: fixed;right:0;margin-right: 20px;bottom: 0;margin-bottom: 20px;border: 1px solid #000; width: 250px; line-height: 30px; min-height: 100px;background-color: #fff;padding: 30px; }
    #stock_pop p{text-align: center;margin: 0;font-size: 13px}
    #stock_pop p button {margin-top: 20px;}
    #stock_pop p i{ background: url(https://www.cloma.co.kr/images/mb_icon_set_img.png) no-repeat -54px -408px;display: inline-block;padding: 0 8px;font-size: 15px;width: 35px;height: 35px;margin: auto;background-size: 188px!important;margin-top: 5px;}
</style>
<div id="stock_pop">
    <p><i></i></p>
    <p>재고 부족 옵션 상품이 있습니다.</p>
    <p>관리 메뉴에서 확인해주세요.</p>
    <p>
        <button type="button" class="btn btn-danger" onclick="$('#stock_pop').hide();" style="width: 45%;float: left;">닫기</button>
        <button type="button" class="btn btn-info" onclick="location.href='/product_stock';" style="width: 45%;float: left;margin-left: 5%;">관리메뉴</button>
        <div class="clear"></div>
    </p>
</div>

<script>
    function chk_stock(){
        $.ajax({
            url: '/product_stock/chk_stock/',
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {
                if(result.data.cnt > 0) $('#stock_pop').show();
            }
        });
    }


    <?
    $stock_chk_user = array(5,10,12);
    if(in_array($_SESSION['session_au_num'],$stock_chk_user) == true){
    ?>
    $(function(){
        chk_stock();
    })
    <?} ?>


    /**
     * 회원 수정 팝업
     * @param m_num
     */
    function member_update_pop(m_num) {
        if( empty(m_num) || m_num == 0 ) {
            return false;
        }

        new_win_open('/member/update/?m_num=' + m_num + '&pop=y', 'mem_win', 1200, 800);
    }//end of member_update_pop()
</script>

<?php }//end of if( no_footer ) ?>
<iframe name="actionFrame" id="actionFrame" class="actionFrame hide"></iframe>
</body>
</html>