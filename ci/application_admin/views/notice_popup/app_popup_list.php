<style>
    .popover,.popover * {max-width: 300px !important}
</style>
<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">공지팝업 관리 > 목록 <span style="color:red"></span></h4>
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
                                <option value="apo_subject">제목</option>
                            </select>
                            <script>selected_check($('#kfd'), '<?php echo $req['kfd']; ?>')</script>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">일자검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" class="form-control" style="width:auto;">
                            <option value="apo_termlimit_datetime1">노출시작일</option>
                            <option value="apo_termlimit_datetime2">노출종료일</option>
                            <option value="apo_regdattime">팝업등록일</option>
                        </select>
                    </div>
                    <div class="pull-left mgl5" >
                        <div class="input-group date" style="width:123px;">
                            <input type="text" class="form-control" style="width:90px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:123px;">
                            <input type="text" class="form-control" style="width:90px;" name="date2" value="<?php echo $req['date2']; ?>" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-outline btn-sm mgl5" onclick="set_date_term();">오늘</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3d');">3일</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-7d');">7일</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1m');">1개월</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3m');">3개월</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="clear_date_term();">전체</button>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" onclick="app_popup_insert_pop();" style="width:100px;">등록</button>
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

        <div id="app_popup_list" style="position:relative;"></div>
    </div>
</div>


<script src="/plugins/datepicker/bootstrap-datepicker.js" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js" charset="utf-8"></script>

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
     * APP 팝업 등록
     */
    function app_popup_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('APP 메인팝업 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of app_popup_insert_pop()

    /**
     * APP 팝업 수정
     */
    function app_popup_update(apo_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?apo_num=' + apo_num);

        modalPop.createPop('APP 메인팝업 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of app_popup_update_pop()

    /**
     * APP 팝업 삭제
     */
    function app_popup_delete(apo_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {apo_num:apo_num},
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
    }//end of app_popup_delete()

    /**
     * 노출/미노출
     * @param ap_num
     */
    function app_popup_display_toggle(apo_num) {
        if( empty(apo_num) ) {
            return false;
        }

        if( !confirm('상태를 변경하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/app_popup/display_toggle',
            data : {apo_num : apo_num},
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

    //document.ready
    $(function () {
        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

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
                loadingBar.show($('#app_popup_list'));
            },
            success: function(result) {
                $('#app_popup_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#app_popup_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready
</script>

