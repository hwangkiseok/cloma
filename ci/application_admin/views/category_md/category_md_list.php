<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">카테고리MD관리 > 목록</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />


            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">구분</label>
                <div class="col-sm-10">
                    <select name="division" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("category_md_division"), $req['division']); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">활성여부</label>
                <div class="col-sm-10 mgt3">
                    <label><input type="radio" name="state" value="" <?php echo (empty($req['state'])) ? "checked" : ""; ?> /> 전체</label>
                    <?php echo get_input_radio("state", $this->config->item("category_md_state"), $req['state']); ?>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="all">전체</option>
                                <option value="cmd_name">카테고리명</option>
                                <option value="cmd_product_cate">상품카테고리명</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="category_md_insert_pop();">등록</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-left">
                <button type="button" class="btn btn-primary btn-sm" onclick="category_md_order_update();">진열순서변경</button>
            </div>
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="product_list" style="position:relative;"></div>
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
     * 카테고리 MD 등록 팝업
     */
    function category_md_insert_pop() {
        //modal 출력
        var container = $("<div>");
        $(container).load('<?php echo $this->page_link->base; ?>/insert_pop');

        modalPop.createPop("카테고리 MD 등록", container);
        modalPop.createButton("등록", "btn btn-primary btn-sm", function(){
            $('#pop_form').submit();
        });
        modalPop.createCloseButton("취소", "btn btn-default btn-sm", function(){});
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of category_md_insert_pop()

    /**
     * 카테고리 MD 수정 팝업
     */
    function category_md_update_pop(cmd_num) {
        if( empty(cmd_num) ) {
            return false;
        }

        //modal 출력
        var container = $("<div>");
        $(container).load('<?php echo $this->page_link->base; ?>/update_pop/?cmd_num=' + cmd_num);

        modalPop.createPop("카테고리 MD 수정", container);
        modalPop.createButton("수정", "btn btn-primary btn-sm", function(){
            $('#pop_form').submit();
        });
        modalPop.createCloseButton("취소", "btn btn-default btn-sm", function(){});
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of category_md_update_pop()

    /**
     * 카테고리 MD 삭제
     * @param cmd_num
     */
    function category_md_delete(cmd_num) {
        if( empty(cmd_num) ) {
            return false;
        }
        if( !confirm('삭제하시겠습니까?') ) {
             return false;
        }

        Pace.restart();
        loadingScreen.show();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {cmd_num:cmd_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
                loadingScreen.hide();
            }
        });
    }//end of md_delete()

    /**
     * 카테고리 MD 순서변경
     */
    function category_md_order_update () {
        var req_data = {};
        $.each($('[name*="cmd_order[]"]'), function(){
            req_data[$(this).attr('data-num')] = $(this).val();
        });

        Pace.restart();

        $.ajax({
            url : '/category_md/order_proc',
            data : {data:req_data},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' ) {
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
    }//end of md_order_update()

    /**
     * 카테고리 MD 수정 토글
     * @param p_num
     * @param fd
     */
    function category_md_update_toggle(cmd_num, fd) {
        if( empty(cmd_num) || empty(fd) ) {
            return false;
        }

        if( !confirm('활성상태를 변경하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '/category_md/update_toggle',
            data : {cmd_num:cmd_num, fd:fd},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' && !empty(result.message) ) {
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
    }//end of category_md_update_toggle()

    //document.ready
    $(function () {
        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //목록 체크박스 click
        $(document).on('click', '#all_list_check', function(){
            $('[name*="num["]').prop('checked', $(this).prop('checked'));
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                type: 'post',
                dataType: 'html',
                beforeSubmit: function(formData, jqForm, options) {
                    loadingBar.show($('#product_list'));
                },
                success: function(result) {
                    $('#product_list').html(result);
                },
                complete: function() {
                    loadingBar.hide();
                }
            });//end of ajax_form()

            $('#search_form').submit();

            //ajax page
            $(document).on('click', '#product_list .pagination.ajax a', function(e){
                e.preventDefault();

                if( $(this).attr('href') == '#none' ) {
                    return false;
                }

                $('#search_form').attr('action', $(this).attr('href'));
                $('#search_form').submit();
            });
        }//end of if()
    });//en d of document.ready
</script>