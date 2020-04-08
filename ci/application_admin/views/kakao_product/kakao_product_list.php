<script type="text/javascript" src="/js/clipboard.min.js"></script>
<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">카카오광고 > 상품관리</h4>
    </div>

    <!--<div class="row well pd10">
        <div class="row">
            <div class="pull-right text-right mgr15">
                <a href="#none" class="btn btn-success btn-sm" onclick="product_list_load();">상품불러오기</a>
                <a href="/product/insert" class="btn btn-info btn-sm" style="width:100px;">상품등록</a>
            </div>
        </div>
    </div>-->

    <div class="row">

        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색 분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="p_name">상품명</option>
                                <option value="kp_title">제목</option>
                                <option value="kp_content">내용</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                        <!--<span class="input-group-btn" style="width:auto">-->
                        <!--    <button type="submit" class="btn btn-primary btn-sm">검색</button>-->
                        <!--</span>-->
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">노출 상태</label>
                <div class="col-sm-10 mgt3" id="product_state_checkbox">
                    <?php $checked = ( empty($req['display_state']) ) ? "checked" : ""; ?>
                    <label>
                        <input type="checkbox" id="chk_all_state" <?php echo $checked; ?> /> 전체
                    </label>

                    <?php
                    foreach( $this->config->item('kakao_product_display_state') as $key => $text ) {
                        $checked = ( !empty($req['display_state']) && array_search($key, $req['display_state']) !== false ) ? "checked" : "";
                    ?>
                    <label>
                        <input type="checkbox" name="display_state[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                    </label>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">상품 구분</label>
                <div class="col-sm-10 mgt3" id="prod_type_checkbox">
                    <?php $checked = ( empty($req['prod_type']) ) ? "checked" : ""; ?>
                    <label>
                        <input type="checkbox" id="chk_all_prod_type" <?php echo $checked; ?> /> 전체
                    </label>

                    <?php
                    foreach( $this->config->item('kakao_product_prod_type') as $key => $text ) {
                        $checked = ( !empty($req['prod_type']) && array_search($key, $req['prod_type']) !== false ) ? "checked" : "";
                        ?>
                        <label>
                            <input type="checkbox" name="prod_type[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                        </label>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">상품 상태</label>
                <div class="col-sm-10 mgt3">
                    <?php $checked = ( empty($req['prod_sale_state']) && empty($req['prod_display_state']) ) ? "checked" : ""; ?>
                    <label>
                        <input type="checkbox" id="prod_chk_all_state" <?php echo $checked; ?> /> 전체
                    </label>

                    <?php
                    foreach( $this->config->item('product_sale_state') as $key => $text ) {
                        $checked = ( !empty($req['prod_sale_state']) && array_search($key, $req['prod_sale_state']) !== false ) ? "checked" : "";
                        ?>
                        <label>
                            <input type="checkbox" name="prod_sale_state[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                        </label>
                    <?php } ?>

                    <?php
                    foreach( $this->config->item('product_display_state') as $key => $text ) {
                        $checked = ( !empty($req['prod_display_state']) && array_search($key, $req['prod_display_state']) !== false ) ? "checked" : "";
                        ?>
                        <label>
                            <input type="checkbox" name="prod_display_state[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                        </label>
                    <?php } ?>
                    <label>
                        <input type="checkbox" name="prod_hash_chk" value="Y" <?php echo $req['prod_hash_chk']; ?> /> 해시없음
                    </label>
                    <label>
                        <input type="checkbox" name="prod_second_price_yn" value="N" <?php echo $req['prod_second_price_yn']; ?> /> 2차판매가 없음
                    </label>

                    <label>
                        <input type="checkbox" name="prod_restock_yn" value="Y" /> 품절제외
                    </label>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <a href="#none" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="kakao_product_insert_pop();">등록</a>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-left">
                <button type="button" class="btn btn-primary btn-sm" onclick="kp_prod_seq_update();">출력순서변경 (브랜드상품)</button>
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
     * 정렬 submit
     * @param sf
     * @param st
     */
    function order_submit(sf, st) {
        $('#search_form input[name="sort_field"]').val(sf);
        $('#search_form input[name="sort_type"]').val(st);
        $('#search_form').submit();
    }//end of form_submit()

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
     * 상태 변경 토글
     * @param kkp_num
     * @param fd
     */
    function kakao_product_update_toggle(kp_num, fd) {
        if( empty(kp_num) || empty(fd) ) {
            return false;
        }

        if( !confirm('상태를 변경하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '/kakao_product/update_toggle',
            data : {kp_num:kp_num, fd:fd},
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
    }//end of kakao_product_update_toggle()

    /**
     * 카카오광고 상품 등록 팝업
     */
    function kakao_product_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('카카오광고 상품 등록', container);
        modalPop.createButton('등록하기', 'btn btn-info btn-sm', function(){
            $('#pop_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }//end of kakao_product_insert_pop()

    /**
     * 카카오광고 상품 수정 팝업
     */
    function kakao_product_update_pop(kp_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?kp_num=' + kp_num + '&' + $('#search_form').serialize());

        modalPop.createPop('카카오광고 상품 수정', container);
        modalPop.createButton('수정하기', 'btn btn-info btn-sm', function(){
            $('#pop_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }//end of kakao_product_update_pop()

    /**
     * 카카오광고 상품 삭제
     */
    function kakao_product_delete(kp_num) {
        if( empty(kp_num) ) {
            return false;
        }
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        //location.href = '<?//=$this->page_link->delete_proc;?>///?kp_num=' + kp_num + '&' + gv;
        $.ajax({
            url : '<?=$this->page_link->delete_proc;?>',
            data : {kp_num:kp_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( !empty(result.message) ) {
                    alert(result.message);
                }

                if( result.status == '<?=get_status_code('success');?>' ) {
                    $('#search_form').submit();
                }
            },
            error : function() {
                alert('삭제 실패!!!');
            }
        });
    }//end of kakao_product_delete()

    /**
     * 카카오광고 상품 단축 URL 추출
     */
    function get_kakao_product_shorten_url(kp_num) {
        if( empty(kp_num) ) {
            return false;
        }

        loadingScreen.show();

        $.ajax({
            url : '/kakao_product/shorten_url',
            data : {kp_num:kp_num},
            type : 'post',
            dataType : 'json',
            async: false,
            success : function(result) {
                //copy_clipboard(result.data.url);
                copy_clipboard_n(result.data.url);
            },
            error : function () {
                alert('Error!');
                $('.url_content_wrap>li>a.btn').removeClass('btn-danger');
                $('.url_content_wrap>li>a.btn').addClass('btn-default');
            },
            complete : function() {
                loadingScreen.hide();
            }
        });
    }//end of get_kakao_product_shorten_url()

    //==clipboard Copy FNC
    function copy_clipboard_n(str){
        if(str == null || str == '' || str == undefined){
            return false;
        }
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text','');
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text',str);
        $('#dummyClipboard').off().click();
    }//end of copy_clipboard_n()

    //동적클래스 처리
    $(document).on('click','.shortUrl-Load',function(){
        $('.url_content_wrap').hide();

        if( $(this).hasClass('Show-Active') == false ) {
            $(this).addClass('Show-Active');
            $(this).parent().find('.url_content_wrap').show();
        }
        else {
            $(this).removeClass('Show-Active');
        }
    });

    //동적클래스 처리
    $(document).on('click','.zclipCopy',function(){
        var obj = $(this);

        $('.url_content_wrap>li>a.btn').removeClass('btn-danger');
        $('.url_content_wrap>li>a.btn').addClass('btn-default');

        $(obj).addClass('btn-danger');
        $(obj).removeClass('btn-default');

        if($(this).attr('data-clipboard-text') != undefined){
            copy_clipboard_n($(this).attr('data-clipboard-text'));
        }
    });
    //==/clipboard Copy FNC

    /**
     * 브랜드상품 출력순서 업데이트
     */
    function kp_prod_seq_update() {
        Pace.restart();

        $.ajax({
            url : '/kakao_product/seq_update_proc',
            data : $('#listForm').serialize(),
            type : 'post',
            dataType : 'json',
            success : function(result) {
                console.log(result);

                if( result.status == '<?=get_status_code("success");?>' ) {
                    $('#search_form').submit();
                }
                else {
                    alert(result.message);
                }
            },
            error : function() {
                alert('error!!');
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end kp_prod_seq_update;


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
            $('.url_content_wrap>li>a.btn').removeClass('btn-danger');
            $('.url_content_wrap>li>a.btn').addClass('btn-default');
            e.clearSelection();
        });
        //clipboard Copy

        //진열/판매상태 click
        $('#chk_all_state').on('click', function(){
            $('[name="display_state[]"]').prop('checked', false);
        });

        $('[name="display_state[]"]').on('click', function(){
            $('#chk_all_state').prop('checked', false);

            if( !$('[name="display_state[]"]:checked').length ) {
                $('#chk_all_state').prop('checked', true);
            }
        });

        //상품구분 click
        $('#chk_all_prod_type').on('click', function(){
            $('[name="prod_type[]"]').prop('checked', false);
        });

        $('[name="prod_type[]"]').on('click', function(){
            $('#chk_all_prod_type').prop('checked', false);

            if( !$('[name="prod_type[]"]:checked').length ) {
                $('#chk_all_prod_type').prop('checked', true);
            }
        });


        //목록 체크박스 click
        $(document).on('click', '#all_list_check', function(){
            $('[name="kp_num[]"]').prop('checked', $(this).prop('checked'));
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //상품 진열/판매상태 전체 click
        $('#prod_chk_all_state').on('click', function(){
            $('[name="prod_sale_state[]"]').prop('checked', false);
            $('[name="prod_display_state[]"]').prop('checked', false);
            $('[name="prod_hash_chk"]').prop('checked', false);
            $('[name="prod_second_prict_yn"]').prop('checked', false);
            $('[name="prod_restock_yn"]').prop('checked', false);
        });

        //상품 진열/판매상태 개별 click
        $('[name="prod_sale_state[]"],[name="prod_display_state[]"],[name="prod_hash_chk"],[name="prod_second_price_yn"],[name="prod_restock_yn"]').on('click', function(){
            $('#prod_chk_all_state').prop('checked', false);

            if(     !$('[name="prod_sale_state[]"]:checked').length
                &&  !$('[name="prod_display_state[]"]:checked').length
                &&  !$('[name="prod_hash_chk"]:checked').length
                &&  !$('[name="prod_second_price_yn"]:checked').length
                &&  !$('[name="prod_restock_yn"]:checked').length
            ) {
                $('#prod_chk_all_state').prop('checked', true);
            }
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                //url : '<?php //echo $this->page_link->list_ajax; ?>//',
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

                //$('#search_form').attr('action', $(this).attr('href'));
                //$('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()
    });//en d of document.ready
</script>