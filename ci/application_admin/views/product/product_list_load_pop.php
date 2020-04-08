<div class="row">
    <form name="pop_search_form" id="pop_search_form" method="post" action="/product/list_load_ajax" class="form-horizontal">
        <input type="hidden" name="db" value="<?php echo $req['db']; ?>" />
        <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
        <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
        <input type="hidden" name="sort_field" value="" />
        <input type="hidden" name="sort_type" value="" />

        <div class="form-group form-group-sm form-inline">
            <label class="col-sm-2 control-label" for="kwd">검색</label>
            <div class="col-sm-10 form-inline">
                <select name="db" class="form-control" style="width:auto;">
                    <?php echo get_select_option("", $this->config->item("ext_site_name"), $req['db']); ?>
                </select>

                <select name="cate" class="form-control" style="width:auto;">
                    <?php echo get_select_option("* 카테고리 *", $this->config->item("product_category"), $req['cate']); ?>
                </select>

                <div class="input-group">
                    <span class="input-group-btn" style="width:auto">
                        <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                            <option value="p_name">상품명</option>
                            <option value="p_supplier">공급사</option>
                        </select>
                    </span>
                    <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm">
            <label class="col-sm-2 control-label">진열/판매상태</label>
            <div class="col-sm-10 mgt3" id="product_state_checkbox">
                <?php $checked = ( empty($req['sale_state']) && empty($req['display_state']) ) ? "checked" : ""; ?>
                <label>
                    <input type="checkbox" id="pop_chk_all_state" <?php echo $checked; ?> /> 전체
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
            </div>
        </div>

        <hr />

        <div class="form-group form-group-sm text-center">
            <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
        </div>
    </form>
</div>

<div class="row mgb10">
    <div class="col-md-2 pull-left" style="line-height:30px;">
        총 : <span id="total_count" style="font-weight:bold;"></span> 건
    </div>
    <div class="col-md-2 pull-right">
        <select id="pop_list_per_page" class="form-control input-sm">
            <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
        </select>
    </div>
</div>

<div class="row">
    <div id="pop_product_list" style="position:relative;padding:0 10px;"></div>
</div>


<script>
    /**
     * pop form submit
     */
    function pop_form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#pop_search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#pop_search_form').submit();
    }//end of pop_form_submit()


    //document.ready
    $(function() {
        $('[name="db"]').on('change', function(){
            $('#pop_search_form').submit();
        });

        //진열/판매상태 click
        $('#pop_chk_all_state').on('click', function(){
            $('[name="sale_state[]"]').prop('checked', false);
            $('[name="display_state[]"]').prop('checked', false);
        });
        $('[name="sale_state[]"],[name="display_state[]"]').on('click', function(){
            $('#pop_chk_all_state').prop('checked', false);

            if( !$('[name="sale_state[]"]:checked').length && !$('[name="display_state[]"]:checked').length ) {
                $('#pop_chk_all_state').prop('checked', true);
            }
        });

        //목록 갯수보기 select change
        $('#pop_list_per_page').on('change', function(){
            $('#pop_search_form [name="list_per_page"]').val($(this).val());
            $('#pop_search_form').submit();
        });


        //ajaxform
        $('#pop_search_form').ajaxForm({
            //url : '<?php //echo $this->page_link->list_ajax; ?>//',
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#pop_product_list'));
            },
            success: function(result) {
                $('#pop_product_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#pop_search_form').submit();

        //ajax page
        $(document).on('click', '#pop_product_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            pop_form_submit($(this).attr('href'));
        });
    });//end of document.ready()
</script>