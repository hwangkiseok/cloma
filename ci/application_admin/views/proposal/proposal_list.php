<script type="text/javascript" src="/js/clipboard.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">제안서파일관리 > 목록</h4>
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
                                <option value="pf_name">제목</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" onclick="proposal_insert_pop();" style="width:100px;">등록</button>
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

        <div id="proposal_list" style="position:relative;"></div>
    </div>
</div>
<button id="dummyClipboard" data-clipboard-text="" style="display: none;"></button>
<iframe id="pf_file_download" style="display: none;"></iframe>
<script>


    function copy_clipboard(str){

        if(str == null || str == '' || str == undefined){
            return false;
        }

        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text','');
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text',str);
        $('#dummyClipboard').off().click();

    }

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
     * 배너 등록 팝업
     */
    function proposal_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('제안서파일 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    }//end of proposal_insert_pop()

    /**
     * 배너 수정 팝업
     */
    function proposal_update_pop(pf_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?pf_num=' + pf_num);

        modalPop.createPop('제안서파일 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    }//end of proposal_update_pop()

    /**
     * 배너 삭제
     */
    function proposal_delete(pf_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {pf_num:pf_num},
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
    }//end of proposal_delete()

    /**
     * 전체 체크
     */
    function all_check(obj) {
        $('[name="pf_num[]"]').prop('checked', $(obj).prop('checked'));
    }//end of all_check()


    $(document).on('click','.pf_file_download',function(e){
        e.preventDefault();
        var path = $(this).data('path');
        var name = $(this).data('name');
        path = 'http://admin.cloma.co.kr/download/?n='+name+'&f=' + path;
        $('#pf_file_download').attr('src', path);
    });

    $(document).on('click','.pf_file_copy',function(e){
        e.preventDefault();
        var path = $(this).data('path');
        var name = $(this).data('name');
        path = 'http://admin.cloma.co.kr/download/?n='+name+'&f=' + path;

        copy_clipboard(path);

    });




    //document.ready
    $(function () {

        //clipboard Copy
        var clipboard = new Clipboard('#dummyClipboard');
        clipboard.on('success', function(e) {
            //console.log(e);
            //console.log('!clipboard CALLBACK');
            alert('단축URL Copied');
            e.clearSelection();
        });
        clipboard.on('error', function(e) {
            alert('------- 클립보드 복사 실패 -------');
            e.clearSelection();
        });
        //clipboard Copy

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
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#proposal_list'));
            },
            success: function(result) {
                $('#proposal_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#proposal_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready
</script>