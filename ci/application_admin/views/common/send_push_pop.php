<form id="push_insert_form" action="/common/send_push_proc/" method="post">
    <table class="table table-bordered">
        <input type="hidden" name="m_num" value="<?=$aInput['m_num']?>" />
        <tr>
            <th class="active">타이틀</th>
            <td> <input type="text" class="form-control" name="push_title"> </td>
        </tr>

        <tr>
            <th class="active">내용</th>
            <td> <input type="text" class="form-control" name="push_content"> </td>
        </tr>
    </table>
</form>

<script>
    $(function(){
        $('#push_insert_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(){

                if( $('#push_insert_form input[name="push_title"]').val() == '' ){
                    alert('보내실 푸시 제목을 입력해주세요.');
                    return false;
                }

                if( $('#push_insert_form input[name="push_content"]').val() == '' ){
                    alert('보내실 푸시 내용을 입력해주세요.');
                    return false;
                }

            },
            success: function(result){
                if(result.msg) alert(result.msg);
            },
            error: function(){
                alert("Submit Error");
            }
        });

    })
</script>
