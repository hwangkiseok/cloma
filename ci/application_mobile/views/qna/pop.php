<form name="qna_frm" class="qna_frm" action="<?=$this->page_link->insert_proc?>" >
    <ul>
        <li>
            <p>이용하며 느끼신 불편한 점이나 궁금하신 사항을 알려주세요.</p>
            <p>최대한 빠르게 답변 드리도록 하겠습니다.</p>
        </li>
        <li>
            <select name="bq_category" style="width: 100%" class=" testclass form-control" title="">
                <option value="" disabled selected hidden>문의 유형 선택</option>
                <?php echo get_select_option("", $this->config->item('board_qna_category')); ?>
            </select>
        </li>
        <li> <textarea name="bq_content" title class="form-control" rows="6" placeholder="질문 입력(500자 이내)"></textarea></li>

    </ul>
</form>

<script type="text/javascript">

    function qna_frm_submit(){

        if( empty($('select[name="bq_category"]').val()) ==  true ){
            alert('문의 유형을 선택해주세요 !');
            return false;
        }

        if( empty($('textarea[name="bq_content"]').val()) == true ){
            alert('내용을 입력해주세요 !');
            return false;
        }

        $('form[name="qna_frm"]').submit();

    }

    $(function(){

        $('form[name="qna_frm"]').ajaxForm({
            type: 'post',
            enctype: "multipart/form-data",
            dataType: 'post',
            dataType: 'json',
            beforeSubmit: function(data, form, option) {
            },
            success: function (result) {

                if( !empty(result.message_type) && result.message_type == 'alert' && !empty(result.message) ) {
                    showToast(result.message);
                }

                if( result.status == '<?=get_status_code('success')?>' ) {
                    modalPop.hide();
                    $('input[name="page"]').val(1);
                    ajaxPaging(true);
                }
                else {
                    if( result.error_data ) {
                        var error_text = "";

                        $.each(result.error_data, function(key, msg){
                            error_text += msg + '\n';
                        });

                        if( !empty(error_text) ) {
                            alert(error_text);
                        }
                    }
                }//end of if()

            }

        });

    })

</script>