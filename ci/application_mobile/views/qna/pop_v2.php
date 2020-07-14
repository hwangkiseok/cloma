<style>
    .del_img {text-align: right;}
    .del_img .del_img_warp{display: inline-block;width: 66%;}
    .del_img .del_img_warp span{cursor: pointer; display: inline-block;width: 25%;text-align: center;padding-bottom: 2px;border-bottom: 1px solid #1070ff;color:#1070ff;margin-left: 5%;}
    .del_img .del_img_warp span:first-child{margin-left: 0;}
    .prev_img {padding-top: 15px;}
    .prev_img .add_img {float: left;width: 34%;}
    .prev_img .add_img.first-pop {width: 100%}
    .prev_img .add_img.first-pop button{width: 100%;}
    .prev_img .add_img_list {float: right;width: 66%;text-align: right;}
    .prev_img .add_img_list span {width: 25%;height: 52px; display: inline-block;margin-left: 5%;text-align: center;border: 1px solid #333;position: relative;vertical-align: top;}
    .prev_img .add_img_list span:first-of-type {margin-left: 0!important;}
    .prev_img .add_img_list span {overflow: hidden}
    .prev_img .add_img .sel_img {padding: 10px ;vertical-align: middle;_height: 55px;_line-height: 55px;background: #f1f1f1;}
    .prev_img .add_img .sel_img span{vertical-align: middle}
    .prev_img .add_img .sel_img i{background: url(https://www.cloma.co.kr/images/mb_icon_set_img.png) no-repeat -89px -122px;display: inline-block;
        width: 30px; height: 30px;background-size: 125px!important;vertical-align: middle}
</style>


<form name="qna_frm" class="qna_frm" action="<?=$this->page_link->insert_proc?>" >
    <input multiple="multiple" name="qna_img[]" id="qna_img" type="file" style="display:none"/>
    <input multiple="multiple" name="files[]" id="files" type="file" style="display:none"/>

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

        <li class="prev_img">

            <span class="add_img first-pop">
                <button role="button" class="btn btn-border-black sel_img">
                    첨부하기<i></i>
                </button>
            </span>
            <span class="add_img_list img_group" style="display: none;">

            </span>
            <div class="clear"></div>
        </li>
        <li class="del_img">
            <div class="del_img_warp img_group" style="display: none">
            </div>
        </li>
    </ul>
</form>
<?

$a= '';
$b= '';


if(empty($a) == false || empty($b) == false){
    echo 'a';
}
?>

<script type="text/javascript">

    var fileBuffer = [];

    /**
     * 선택한 파일 미리보기
     */
    function readURL() {

        var ret_msg = '이미지 첨부는 최대 3개까지 가능합니다.';
        var limit_b = false;
        var list_html = '';
        var del_html = '';

        const input = document.getElementsByName('files[]');

        if(fileBuffer.length >= 3 && input[0].files.length > 0) {
            alert(ret_msg);
            return false;
        }

        Array.prototype.push.apply(fileBuffer, input[0].files );

        var l   = fileBuffer.length;
        var seq = 0;

        // var overlap_b = false;
        // var overlap_msg = '같은 이미지가 있습니다.';
        // var l2  = input[0].files.length;
        // for(var i = 0 ; i < l  ; i++){ var fb = fileBuffer[i];
        //
        //     for(var j = 0 ; j < l2 ; j++){ var in_d = input[0].files[j];
        //
        //         if(fb.name == in_d.name){
        //             fileBuffer.splice( parseInt(seq) ,1);
        //             overlap_b = true;
        //         }else{
        //             seq++;
        //         }
        //
        //     }
        // }
        //
        // seq = 0;

        for(var i = 0 ; i < l ; i++){
            if( i > 2 ){
                fileBuffer.splice( parseInt(seq) ,1);
                limit_b = true;
            }else{
                seq++;
            }

            console.log(i);

        }

        $.each(fileBuffer, function(index, file){
            list_html += '<span data-t="'+index+'"> <img src="'+URL.createObjectURL(file)+'" style="width:100%;" /></span>';
            del_html += '<span data-s="'+index+'">삭제</span>';
        });

        $('.add_img_list').html(list_html);
        $('.del_img_warp').html(del_html);

        if(limit_b == true) alert(ret_msg);
        // if(overlap_b == true) alert(overlap_msg+'[2]');

        if( $('.add_img').hasClass('first-pop') == true ) $('.add_img').removeClass('first-pop');
        $('.img_group').show();



    }//end of readURL()

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

    $(document).on('click','.del_img_warp span',function(){

        var seq = $(this).data('s');

        $('.add_img_list span[data-t="'+seq+'"]').remove();
        $('.del_img_warp span[data-s="'+seq+'"]').remove();

        $('.add_img_list span').each(function(k , r){
            $(this).attr('data-t',k);
        });

        $('.del_img_warp span').each(function(k , r){
            $(this).attr('data-s',k);
        });

        fileBuffer.splice(seq,1);

    });


    $(function(){

        $('.sel_img').on('click',function(e){
            e.preventDefault();
            $('#files').click();
        });

        $('#files').on('change',function(e){
            readURL();
        });

        $('form[name="qna_frm"]').ajaxForm({
            type: 'post',
            enctype: "multipart/form-data",
            // dataType: 'post',
            dataType: 'json',
            beforeSubmit: function(data, form, option) {

                var fileSize = fileBuffer.length;
                if (fileSize>0){
                    for(var i=0; i<fileSize; i++){
                        var obj = {
                            name : "qna_img[]",
                            value : fileBuffer[i],
                            type : "file"
                        };
                        data.push(obj);
                    }
                }

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