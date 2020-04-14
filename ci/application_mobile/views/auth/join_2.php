<style>
    #join_form {padding: 10px}
    #join_form p{font-size: 20px;font-weight: bold;padding: 10px}
    #join_form label{font-size: 16px;padding: 20px 0 10px 0;display: inline-block}

    #join_form table tr{ border-bottom: 1px solid #ddd;}
    #join_form table tr>* {padding: 10px 0}
    #join_form table tr th{width: 20%}
    #join_form table tr td input{padding: 5px}
    #join_form table tr td select{padding: 5px }

</style>

<form name="join_form" id="join_form" method="post" action="/Auth/join_web_proc">
    <p>부가정보 입력</p>

    <label>필수정보입력</label>
    <table>
        <tr>
            <th><b style="color:red">*</b> 연락처</th>
            <td>
                <input type="text" style="width: 10%">&nbsp;<input type="text" style="width: 20%">&nbsp;<input type="text" style="width: 20%">
            </td>
        </tr>

    </table>

    <label>선택정보입력</label>

    <table>
        <tr>
            <th>생일</th>
            <td>
                <select>
                    <? for ($i = 1; $i < 13 ; $i++) {?>
                        <option value="<?=sprintf("%02d", $i);?>"><?=sprintf("%02d", $i);?></option>
                    <? } ?>
                </select>&nbsp;월&nbsp;&nbsp;
                <select>
                    <? for ($i = 1; $i < 31 ; $i++) {?>
                        <option value="<?=sprintf("%02d", $i);?>"><?=sprintf("%02d", $i);?></option>
                    <? } ?>
                </select>&nbsp;일
            </td>
        </tr>
        <tr>
            <th>연령대</th>
            <td>
                <select>
                    <? for ($i = 1; $i < 7 ; $i++) {?>
                        <option value="<?=$i?>0"><?=$i?>0대</option>
                    <? } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>성별</th>
            <td>
                <select>
                    <option value="F">여성</option>
                    <option value="M">남성</option>
                </select>
            </td>
        </tr>

        <tr>
            <th>배솓지정보</th>
            <td>
                <input type="text" style="width: 20%;margin-bottom: 5px;" readonly>&nbsp;&nbsp;<button class="btn btn-border-blue" style="padding: 6px;">주소검색</button><br>
                <input type="text" style="width: 100%;margin-bottom: 5px;" readonly><br>
                <input type="text" style="width: 100%">
            </td>
        </tr>

    </table>

    <p style="text-align: center;"><button class="btn btn-border-red">저장하기</button></p>

</form>

<script>
    //document.ready
    $(function(){

        toggle_bg(true);

        //이용약관 modal
        $('#btn_agreement').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/common/termofus/?simple=Y');

            modalPop.createPop('이용약관', container);
            modalPop.show({'hide_footer':true});
        });

        //이메일정보수신동의 modal
        $('#btn_agreemaketing').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/common/UseEventNoti/?simple=Y');
            modalPop.createPop('이벤트/쇼핑정보 수신동의', container);
            modalPop.show({'hide_footer':true});
        });

        //개인정보보호정책 modal
        $('#btn_privacy').on('click', function(){
            var container = $('<div style="height:300px;overflow:auto;border:1px solid #eee;padding:1em;"></div>');
            $(container).load('/common/privacy/?simple=Y');
            modalPop.createPop('개인정보 취급방침', container);
            modalPop.show({'hide_footer':true});
        });

        //Ajax Form
        $('#join_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                var cf = confirm('<?=$this->config->item('site_name_kr');?>의 서비스 이용약관, 개인정보취급방침, 이벤트/쇼핑정보 수신동의에 동의합니다.');
                if(cf == false) return false;

            },
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == '<?php echo get_status_code('success'); ?>' ) {

                    if( !empty(result.goUrl) ) {
                        go_link(result.goUrl,'','','', 'R');
                    }else{
                        go_link('/','','','', 'R');
                    }

                }

            }
        });//end of ajax_form()
    });//end of document.ready()
</script>