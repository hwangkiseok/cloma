<script type="text/javascript" src="/js/clipboard.min.js"></script>
<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 상품조회</h4>
    </div>

    <div class="row well pd10">
        <div class="row">
            <div class="col-sm-10 col-xs-9" style="line-height:29px;">
                <span style="padding-right:10px;">전체 <b><?php echo number_format($product_count_array['total']['cnt']); ?></b> 건</span>
                <span style="border-left:1px solid #888;padding:0 10px;">판매중 <b><?php echo number_format($product_count_array['sale_state']['Y']['cnt']); ?></b> 건</span>
                <span style="border-left:1px solid #888;padding:0 10px;">판매안함 <b><?php echo number_format($product_count_array['sale_state']['N']['cnt']); ?></b> 건</span>
                <span style="border-left:1px solid #888;padding:0 10px;">진열함 <b><?php echo number_format($product_count_array['display_state']['Y']['cnt']); ?></b> 건</span>
                <span style="border-left:1px solid #888;padding:0 10px;">진열안함 <b><?php echo number_format($product_count_array['display_state']['N']['cnt']); ?></b> 건</span>
            </div>
            <!--<div class="col-sm-2 col-xs-3 text-right">-->
            <div class="pull-right text-right mgr15">
<!--                <a href="#none" class="btn btn-success btn-sm" onclick="product_list_load();">상품불러오기</a>-->
<!--                <a href="/product/insert" class="btn btn-primary btn-sm">상품등록</a>-->
            </div>
        </div>
    </div>

    <div class="row">

        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="p_name">상품명</option>
                                <option value="p_supplier">공급사</option>
                                <option value="p_order_code">상품코드</option>
                                <option value="p_detail">상품내용</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                        <!--<span class="input-group-btn" style="width:auto">-->
                        <!--    <button type="submit" class="btn btn-primary btn-sm">검색</button>-->
                        <!--</span>-->
                    </div>
                </div>
            </div>
            <?
            /*
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">상품분류</label>
                <div class="col-sm-10">
                    <select id="cate" name="cate" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 카테고리 *", $this->config->item("product_category"), $req['cate']); ?>
                    </select>
                </div>
            </div>
            */
            ?>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">일자검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" class="form-control" style="width:auto;">
                            <option value="p_regdatetime">상품등록일</option>
                            <option value="p_termlimit_datetime1">판매시작일</option>
                            <option value="p_termlimit_datetime2">판매종료일</option>
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
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">진열/판매상태</label>
                <div class="col-sm-10 mgt3" id="product_state_checkbox">
                    <?php $checked = ( empty($req['sale_state']) && empty($req['display_state']) ) ? "checked" : ""; ?>
                    <label>
                        <input type="checkbox" id="chk_all_state" <?php echo $checked; ?> /> 전체
                    </label>

                    <?php
                    foreach( $this->config->item('product_sale_state') as $key => $text ) {
                        $checked = ( !empty($req['sale_state']) && array_search($key, $req['sale_state']) !== false ) ? "checked" : "";
                    ?>
                    <label>
                        <input type="checkbox" name="sale_state[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                    </label>
                    <?php } ?>

                    <?php
                    foreach( $this->config->item('product_display_state') as $key => $text ) {
                        $checked = ( !empty($req['display_state']) && array_search($key, $req['display_state']) !== false ) ? "checked" : "";
                    ?>
                    <label>
                        <input type="checkbox" name="display_state[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                    </label>
                    <?php } ?>
                    <label>
                        <input type="checkbox" name="hash_chk" value="Y" <?php echo $req['hash_chk']; ?> /> 해시없음
                    </label>
                    <?/*
                    <label>
                        <input type="checkbox" name="second_prict_yn" value="N" <?php echo $req['second_prict_yn']; ?> /> 2차판매가 없음
                    </label>
                    */?>
                    <label>
                        <input type="checkbox" name="restock_yn" value="Y" /> 품절제외
                    </label>
                </div>
            </div>
            <?/*
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">2/3차 판매가</label>
                <div class="col-sm-10">
                    <label><input type="checkbox" name="price_all" value="" <?=(empty($req['price_second']) && empty($req['price_third'])) ? "checked" : "";?> /> 전체</label>
                    <label class="mgl5"><input type="checkbox" name="price_second[]" value="Y" <?=((is_array($req['price_second']) && in_array("Y", $req['price_second'])) || $req['price_second'] == "Y") ? "checked" : "";?> /> 2차 있음</label>
                    <label class="mgl5"><input type="checkbox" name="price_second[]" value="N" <?=((is_array($req['price_second']) && in_array("N", $req['price_second'])) || $req['price_second'] == "N") ? "checked" : "";?> /> 2차 없음</label>
                    <label class="mgl5"><input type="checkbox" name="price_third[]" value="Y" <?=((is_array($req['price_third']) && in_array("Y", $req['price_third'])) || $req['price_third'] == "Y") ? "checked" : "";?> /> 3차 있음</label>
                    <label class="mgl5"><input type="checkbox" name="price_third[]" value="N" <?=((is_array($req['price_third']) && in_array("N", $req['price_third'])) || $req['price_third'] == "N") ? "checked" : "";?> /> 3차 없음</label>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">메인배너 노출</label>
                <div class="col-sm-10 mgt3" id="product_state_checkbox">
                    <label>
                        <input type="checkbox" name="main_banner_view" value="Y" <?=$req['banner_view']=='Y'?'checked':'' ?> /> 메인배너 노출여부
                    </label>
                </div>
            </div>
            */?>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">배송비 구분</label>
                <div class="col-sm-10 mgt3" id="p_dlv_type_checkbox">
                    <?php
                    foreach( $this->config->item('product_deliveryprice_type') as $key => $text ) {
                        $checked = ( !empty($req['p_dlv_type']) && array_search($key, $req['p_dlv_type']) !== false ) ? "checked" : "";
                        ?>
                        <label class="mgr5">
                            <input type="checkbox" name="p_dlv_type[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                        </label>
                    <?php }//endforeach; ?>
                </div>
            </div>
            <?/*
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">외부공유 불가여부</label>
                <div class="col-sm-10 mgt3" id=" p_outside_display_able_checkbox">
                    <?php
                    foreach( $this->config->item('product_outside_display_able') as $key => $text ) {
                        $checked = ( !empty($req['p_outside_display_able']) && array_search($key, $req['p_outside_display_able']) !== false ) ? "checked" : "";
                        ?>
                        <label class="mgr5">
                            <input type="checkbox" name="p_outside_display_able[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                        </label>
                    <?php }//endforeach; ?>
                </div>
            </div>
            */?>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-left">
<!--                <button type="button" class="btn btn-primary btn-sm" onclick="product_order_update();">진열순서변경 (인기상품전용)</button>-->
                <button type="button" class="btn btn-primary btn-sm" onclick="product_stock_chk();">재고 관리상픔으로 등록</button>
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


<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>


    function product_stock_chk(){

        if( $('input[name="p_num[]"]:checked').length < 1 ){
            alert('재고상품으로 등록할 상품을 선택해주세요.');
            return false;
        }

        var arr = [];

        $('input[name="p_num[]"]:checked').each(function(k,r){
            arr.push($(r).val());
        });

        $.ajax({
            url: '/product/stock_chk/',
            type: 'post',
            data : {p_num : arr},
            dataType: 'json',
            success: function (result) {

                if(result.success) { $('input[name="p_num[]"]').prop('checked',false) }
                if(result.msg) { alert(result.msg); }

            }
        });

    }




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
     * 상품 순서 변경
     */
    function product_order_update () {
        var pnum = {};
        $.each($('[name*="p_order[]"]'), function(){
            pnum[$(this).attr('data-num')] = $(this).val();
        });

        Pace.restart();

        $.ajax({
            url : '/product/order_proc',
            data : {data:pnum},
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
    }//end of product_order_update()

    /**
     * 상품 정보 수정 토글
     * @param p_num
     * @param fd
     */
    function product_update_toggle(p_num, fd) {
        if( empty(p_num) || empty(fd) ) {
            return false;
        }

        if( !confirm('상태를 변경하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '/product/update_toggle',
            data : {p_num:p_num, fd:fd},
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
    }//end of product_update_toggle()

    /**
     * 상품 불러오기
     */
    // function product_list_load() {
    //     var container = $('<div>');
    //     $(container).load('/product/list_load_pop');
    //
    //     modalPop.createPop('상품 불러오기', container);
    //     modalPop.createCloseButton("닫기", "", function () {
    //         modalPop.hide();
    //     });
    //     modalPop.show({
    //         'dialog_class' : 'modal-full',
    //         //'hide_footer' : true,
    //         'backdrop' : 'static'
    //     });
    // }//end of product_list_load()

    /**
     * 상품상세 단축 URL 추출
     */
    function get_product_shorten_url(p_num, site, type) {
        // if( empty(p_num) || empty(site) || empty(type) ) {
        if( empty(p_num) || empty(type) ) {
            return false;
        }

        loadingScreen.show();

        $.ajax({
            url : '/product/shorten_url',
            data : {p_num:p_num, site:site, type:type},
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
    }//end of get_product_shorten_url()

    ////clipboard Copy FNC

    function copy_clipboard_n(str){

        if(str == null || str == '' || str == undefined){
            return false;
        }
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text','');
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text',str);
        $('#dummyClipboard').off().click();

    }

    //동적클래스 처리
    $(document).on('click','.shortUrl-Load',function(){

        $('.url_content_wrap').hide();

        if($(this).hasClass('Show-Active') == false){
            $(this).addClass('Show-Active');
            $(this).parent().find('.url_content_wrap').show();
        }else{
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

    //clipboard Copy FNC

    $(document).on('click','.sendRestockPush',function(e){
        e.preventDefault();

        if( $(this).data('cnt') < 1 ){
            alert('발송대상자가 없습니다.');
            return false;
        }

        var cf = confirm('재입고 알림을 등록한 대상자에게 푸시를 보내시겠습니까?');

        if(cf == false) return false;

        $.ajax({
            url: '/product/restock_push/',
            data: {p_num : $(this).data('seq')},
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {

                if(result.msg) alert(result.msg);
                if(result.success == true) $('#search_form').submit();

            }

        });

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
            $('.url_content_wrap>li>a.btn').removeClass('btn-danger');
            $('.url_content_wrap>li>a.btn').addClass('btn-default');
            e.clearSelection();
        });
        //clipboard Copy

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        //진열/판매상태 click
        $('#chk_all_state').on('click', function(){
            $('[name="sale_state[]"]').prop('checked', false);
            $('[name="display_state[]"]').prop('checked', false);
            $('[name="hash_chk"]').prop('checked', false);
            $('[name="second_prict_yn"]').prop('checked', false);
            $('[name="restock_yn"]').prop('checked', false);

        });

        $('[name="sale_state[]"],[name="display_state[]"],[name="hash_chk"],[name="second_prict_yn"],[name="restock_yn"]').on('click', function(){
            $('#chk_all_state').prop('checked', false);

            if(     !$('[name="sale_state[]"]:checked').length
                &&  !$('[name="display_state[]"]:checked').length
                &&  !$('[name="hash_chk"]:checked').length
                &&  !$('[name="second_prict_yn"]:checked').length
                &&  !$('[name="restock_yn"]:checked').length
            ) {
                $('#chk_all_state').prop('checked', true);
            }
        });

        //2/3차 전체 클릭
        $('[name="price_all"]').on('click change', function() {
            $('[name="price_second[]"]').prop('checked', false);
            $('[name="price_third[]"]').prop('checked', false);
        });

        //2/3차 개별 클릭
        $('[name="price_second[]"], [name="price_third[]"]').on('click change', function() {
            $('[name="price_all"]').prop('checked', false);

            if( !$('[name="price_second[]"]:checked').length && !$('[name="price_third[]"]:checked').length ) {
                $('[name="price_all"]').prop('checked', true);
            }
        });

        //목록 체크박스 click
        $(document).on('click', '#all_list_check', function(){
            $('[name="p_num[]"]').prop('checked', $(this).prop('checked'));
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
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

                // $('#search_form').attr('action', $(this).attr('href'));
                // $('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()
    });//en d of document.ready
</script>