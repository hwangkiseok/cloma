<script type="text/javascript" src="/js/clipboard.min.js"></script>
<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 연관상품 관리</h4>
    </div>

    <div class="row">

        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $req['list_per_page']; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">상품검색</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="">전체</option>
                                <option value="p_name">상위상품명</option>
                                <option value="p_rel_name">연관상품명</option>
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
                <label class="col-sm-2 control-label">연관상품 여부</label>
                <div class="col-sm-10">
                    <label><input type="radio" name="rel_yn" value="A" <?if($req['rel_yn'] == '' || $req['rel_yn'] == 'A'){?>checked<?}?> /> 전체</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="rel_yn" value="Y" <?if($req['rel_yn'] == 'Y' ){?>checked<?}?>/> 있음</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="rel_yn" value="N" <?if($req['rel_yn'] == 'N' ){?>checked<?}?>/> 없음</label>
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
                </div>
            </div>

            <!--
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
                            <input type="text" class="form-control" style="width:90px;" name="date1" value="<?php echo $req['date1']; ?>" autocomplete="off" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:123px;">
                            <input type="text" class="form-control" style="width:90px;" name="date2" value="<?php echo $req['date2']; ?>"  autocomplete="off" />
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
            -->

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <!--<div class="col-md-2 pull-left">
                <button type="button" class="btn btn-primary btn-sm" onclick="product_order_update();">진열순서변경 (인기상품전용)</button>
            </div>-->
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $req['list_per_page']); ?>
                </select>
            </div>
        </div>

        <div id="product_list" style="position:relative;"></div>
    </div>
</div>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

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

    $(document).on('click','.viewRelProductDetail',function(){
        var seq = $(this).data('seq');

        var container = $('<div>');
        $(container).load('/Product_rel/detail/?p_num=' + seq);

        modalPop.createPop('연관상품 정보', container);
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();

    });

    //document.ready
    $(function () {

        //상품 진열/판매상태 전체 click
        $('#prod_chk_all_state').on('click', function(){
            $('[name="prod_sale_state[]"]').prop('checked', false);
            $('[name="prod_display_state[]"]').prop('checked', false);
        });

        //상품 진열/판매상태 개별 click
        $('[name="prod_sale_state[]"],[name="prod_display_state[]"]').on('click', function(){
            $('#prod_chk_all_state').prop('checked', false);
            if(     !$('[name="prod_sale_state[]"]:checked').length
                &&  !$('[name="prod_display_state[]"]:checked').length
            ) {
                $('#prod_chk_all_state').prop('checked', true);
            }
        });

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

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

                //$('#search_form').attr('action', $(this).attr('href'));
                //$('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()
    });//en d of document.ready
</script>