<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">공통관리 > 목록</h4>
    </div>

<!--    <div class="row create_url_area" style="padding: 20px 0;margin-bottom:10px;border: 1px solid #ccc;">-->
<!---->
<!--        <div class="col-sm-12 form-inline" style="margin-bottom: 10px">-->
<!---->
<!--            <p class="alert alert-warning" style="margin-bottom: 10px">다이나믹 URL생성</p>-->
<!--            <label class="form-control-static">URL : </label>&nbsp;<input type="text" class="form-control" name="url" />-->
<!--            <label class="form-control-static">리퍼러 : </label>&nbsp;<input type="text" class="form-control" name="referer" />-->
<!--            <label class="form-control-static">실행타입 : </label>&nbsp;-->
<!--            <select class="form-control" name="action_type">-->
<!--                <option value="1">웹 실행</option>-->
<!--                <option value="2">앱/마켓 실행</option>-->
<!--            </select>&nbsp;-->
<!--            <label class="form-control-static">URL 줄이기 :-->
<!--                <select class="form-control" name="shortner">-->
<!--                    <option value="1">URL 줄이기</option>-->
<!--                    <option value="2">그냥사용</option>-->
<!--                </select>&nbsp;&nbsp;&nbsp;&nbsp;-->
<!--                <button class="btn btn-primary create_url" role="button" type="button">생성하기</button>-->
<!--        </div>-->
<!---->
<!--        <div class="col-sm-12 form-inline result_url" style="display: none">-->
<!--            <p class="alert alert-success" style="margin-bottom: 0"></p>-->
<!--        </div>-->
<!---->
<!--    </div>-->

    <script>
        $(function(){
            $('.create_url').on('click',function(e){
                e.preventDefault();

                var base_url    = $('.create_url_area input[name="url"]').val();
                var referer     = $('.create_url_area input[name="referer"]').val();
                var action_type = $('.create_url_area select[name="action_type"]').val();
                var shortner    = $('.create_url_area select[name="shortner"]').val();

                if( base_url == '' ){
                    alert('필수 입력값 누락 [URL]');
                    return false;
                }

                $.ajax({
                    url : '/api/create_dynamic_url',
                    data : {base_url : base_url , referer : referer , action_type : action_type , shortner : shortner },
                    type : 'post',
                    dataType : 'json',
                    success : function(result) {

                        if(result.url){

                            var html  = '<b>RESULT URL</b><br>';
                            if(shortner == '1'){
                                html += 'ShortUrl : ' + result.url;
                                html += '<br>';
                            }
                            html += 'LongUrl : '+result.long_url;
                            $('.result_url p').html(html);
                            $('.result_url').show();

                        }

                    }

                });

            });

        })
    </script>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="code">구분</label>
                <div class="col-sm-10">
                    <select id="code" name="code" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("common_code"), ''); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="usestate">활성여부</label>
                <div class="col-sm-10">
                    <select id="usestate" name="usestate" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("common_usestate"), $req['usestate']); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="cm_content">내용</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" onclick="common_insert_pop();" style="width:100px;">등록</button>
                <!--<a href="--><?php //echo $this->page_link->insert; ?><!--/?--><?php //echo $GV; ?><!--" class="btn btn-info btn-sm mgl10" style="width:100px;">등록</a>-->
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

        <div id="common_list" style="position:relative;"></div>
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
     * 공통관리 등록 팝업
     */
    function common_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('공통관리 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    }//end of common_insert_pop()

    /**
     * 공통관리 수정 팝업
     */
    function common_update_pop(cm_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?cm_num=' + cm_num);

        modalPop.createPop('공통관리 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    }//end of common_update_pop()

    /**
     * 공통관리 삭제
     */
    function common_delete(cm_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {cm_num:cm_num},
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
    }//end of common_delete()


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
                loadingBar.show($('#common_list'));
            },
            success: function(result) {
                $('#common_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#common_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready
</script>