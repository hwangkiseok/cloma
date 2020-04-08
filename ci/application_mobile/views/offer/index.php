<form name="offer_form" id="offer_form" method="post" action="/offer/offer_insert_proc/" onsubmit="return offerChk();">

    <ul>
        <li><input type="text" name="user_name" placeholder="이름" title="이름"></li>
        <li><input type="text" name="user_hp" placeholder="휴대전화" title="휴대전화" maxlength="11" numberOnly></li>
        <li><input type="email" name="user_email" placeholder="이메일" title="이메일"></li>
        <li><textarea name="content" title="내용" placeholder="&#13;&#10;상품명 :&#13;&#10;옵션명 :&#13;&#10;구매수량 :&#13;&#10;을 형식으로 입력바랍니다.&#13;&#10;" rows="6"></textarea></li>
    </ul>
    <p style="letter-spacing: -0.3pt">&middot; 대량 구매 문의하는 상품명, 옵션명, 수량을 꼭 입력해주세요.</p>

    <button type="submit" class="btn btn-default btn-full" style="margin-top: 20px;" >문의하기</button>

</form>

<script type="text/javascript">

    $(function(){
        $('#offer_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {

            },
            success: function(result) {

                if(empty(result.msg) == false) alert(result.msg);
                if(result.success == true) modalPop.hide();

            },
            complete : function() {
            }
        })
    });

    function offerChk(){

        if($('form[name="offer_form"] input[name="user_name"]').val() == ''){
            alert('이름을 입력해주세요 !');
            return false;
        };
        if($('form[name="offer_form"] input[name="user_hp"]').val() == ''){
            alert('휴대폰번호를을 입력해주세요 !');
            return false;
        };
        if($('form[name="offer_form"] input[name="user_email"]').val() == ''){
            alert('이메일을 입력해주세요 !');
            return false;
        };
        if($('form[name="offer_form"] textarea[name="content"]').val() == ''){
            alert('내용을 입력해주세요 !');
            return false;
        };

    }
</script>

