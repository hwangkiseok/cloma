<style>
    .popover,.popover * {max-width: 300px !important}
    .bg_gray td { background-color:#f6f6f6 !important; }
    .bdr_t_1 td { border-top:2px solid #ccc !important; }
</style>
<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">APP 푸시관리 > 목록</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="ap_subject">제목</option>
                                <option value="ap_message">메시지</option>
                                <option value="ap_summary">요약내용</option>
                            </select>
                            <script>selected_check($('#kfd'), '<?php echo $req['kfd']; ?>')</script>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" onclick="app_push_insert_pop();" style="width:100px;">등록</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="app_push_list" style="position:relative;"></div>
    </div>
</div>

<script>
    /**
     * form submit
     */
    function form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#search_form').submit();
    }//end of form_submit()

    /**
     * APP버전 등록
     */
    function app_push_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('APP 푸시 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of app_push_insert_pop()

    /**
     * APP버전 수정
     */
    function app_push_update_pop(ap_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?ap_num=' + ap_num);

        modalPop.createPop('APP 푸시 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of app_push_update_pop()

    /**
     * APP버전 삭제
     */
    function app_push_delete(ap_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {ap_num:ap_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of app_push_delete()

    /**
     * 노출/미노출
     * @param ap_num
     */
    function app_push_display_toggle(ap_num) {
        if( empty(ap_num) ) {
            return false;
        }

        if( !confirm('상태를 변경하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/app_push/display_toggle',
            data : {ap_num:ap_num},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( !empty(result.message) && result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of app_push_display_toggle()

    /**
     * 푸시 테스트 발송
     */
    function app_push_test_send(ap_num) {
        var adm_mem_list = <?=(!empty($adm_mem_list)) ? json_encode_no_slashes($adm_mem_list) : "''";?>;
        var sns_site_arr = <?=json_encode_no_slashes($this->config->item('member_sns_site'));?>;

        var html = '';
        html += '<form name="popForm" id="popForm">';
        html += '<input type="hidden" name="ap_num" value="' + ap_num + '" />'
        html += '<table class="table table-hover table-bordered dataTable">';
        html += '<tr role="row" class="active">';
        html += '   <th><input type="checkbox" name="allCheck" id="allCheck" value="Y" /></th>';
        html += '   <th>SNS</th>';
        html += '   <th>ID</th>';
        html += '   <th>닉네임</th>';
        html += '   <th>이메일</th>';
        html += '   <th>OS</th>';
        html += '</tr>';
        $.each(adm_mem_list, function(index, item){
            var device_os = '안드로이드';
            if( item.m_device_model == 'iPhone' ) {
                device_os = '아이폰';
            }

            if( !empty(item.m_regid) ) {
                html += '<tr>';
                html += '   <td><input type="checkbox" name="adm_num[]" value="' + item.m_num + '" /></td>';
                html += '   <td>' + sns_site_arr[item.m_sns_site] + '</td>';
                html += '   <td>' + item.m_loginid + '</td>';
                html += '   <td>' + item.m_nickname + '</td>';
                html += '   <td>' + item.m_email + '</td>';
                html += '   <td>' + device_os + '</td>';
                html += '</tr>';
            }//endif;
        });
        html += '</table>';
        html += '</form>';

        html += '<div class="push_result_msg" style="display:none;width:100%;height:300px;overflow-y:scroll;word-break:break-all;background:#f5f5f5;color:#666;border:1px solid #ccc;padding:5px;"></div>';

        modalPop.createPop('푸시 테스트 발송 (관리자 회원 선택)', html);
        modalPop.createCloseButton("닫기", "", function () {
            modalPop.hide();
        });
        modalPop.createButton("발송하기", "btn btn-primary", function () {
            //관리자 회원 확인
            if( !$('[name="adm_num[]"]:checked').length ) {
                alert('관리자 회원을 선택하세요.');
                return false;
            }

            Pace.restart();

            //테스트 발송
            $.ajax({
                url : '/app_push/test_send',
                data : $('#popForm').serialize(),
                type : 'post',
                dataType : 'json',
                success : function(resutlt){
                    $('.push_result_msg').html(JSON.stringify(resutlt));
                    $('.push_result_msg').show();
                },
                complete : function(){
                    Pace.stop();
                }
            });
        });
        modalPop.show({
            'backdrop' : 'static'
        });

        //전체선택/해제
        $(document).on('click', '#allCheck', function(){
            $.each($('#popForm [name="adm_num[]"]'), function(index, item){
                $(this).prop('checked', $('#allCheck').prop('checked'));
            });
        });
    }//end app_push_test_send;


    $(document).on('click','.show_productClick',function(){
        var seq = $(this).data('seq');
        var obj = this;
        $.ajax({
            url: '/app_push/getProductViewDetail/',
            data: {ap_num : seq},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                var data = result.data;
                var html  = '';
                if(data.length > 0){
                    $.each(data,function(index,row){
                        html += '<p style="font-size: 12px">'+row.p_name+' : '+row.apv_cnt + '회</p>';
                    });
                }else{
                    html += '<p style="font-size: 12px">데이터 없음</p>';
                }

                var options = {
                    template : "<div class=\"popover\" role=\"tooltip\"><h3 class=\"popover-title\"></h3><div class=\"popover-content\"></div></div>"
                    ,   content : html
                    ,   title : '앱 푸시 페이지 상품클릭수'
                    ,   html : true
                };
                $(obj).popover(options);
                $(obj).popover('toggle');

            }
        });

    });

    //document.ready
    $(function () {
        $('#div').on('change', function(){
            $('#search_form').submit();
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        $('#search_form').ajaxForm({
            //url : '<?php //echo $this->page_link->list_ajax; ?>//',
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#app_push_list'));
            },
            success: function(result) {
                $('#app_push_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#app_push_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready
</script>

