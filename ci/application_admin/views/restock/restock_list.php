<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">재입고푸시관리 > 목록</h4>
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
                        <input type="checkbox" name="restock_yn" value="Y" /> 품절제외
                    </label>

                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-success btn-sm mgl10" style="width:100px;" onclick="location.href=''">전체보기</button>
                <button type="button" class="btn btn-info btn-sm mgl10 selectRestockPush" style="width:100px;">선택 푸시발송</button>
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

        <div id="restock_list" style="position:relative;"></div>
    </div>
</div>

<script type="text/javascript">


    $(document).on('click','.selectRestockPush',function(){

        if($('[name="p_num[]"]:checked').length < 1){
            alert('푸시발송 대상 상품이 없습니다.');
            return false;
        }

        var cf = confirm('재입고 알림을 등록한 대상자에게 푸시를 보내시겠습니까?');
        if(cf == false) return false;

        var p_num_arr = [];
        $('[name="p_num[]"]:checked').each(function(){
            p_num_arr.push($(this).val());
        });

        var p_num_str = p_num_arr.join(',');

        $.ajax({
            url: '/product/restock_push_mass/',
            data: {p_num_str : p_num_str},
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {

                if(result.msg) alert(result.msg);
                $('#search_form').submit();

            }

        });

    });


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

    //목록 체크박스 click
    $(document).on('click', '#all_list_check', function(){
        $('[name="p_num[]"]').prop('checked', $(this).prop('checked'));
    });

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

    //document.ready
    $(function () {

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

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                type: 'post',
                dataType: 'html',
                beforeSubmit: function(formData, jqForm, options) {
                    loadingBar.show($('#restock_list'));
                },
                success: function(result) {
                    $('#restock_list').html(result);
                },
                complete: function() {
                    loadingBar.hide();
                }
            });//end of ajax_form()

            $('#search_form').submit();

            //ajax page
            $(document).on('click', '#restock_list .pagination.ajax a', function(e){
                e.preventDefault();

                if( $(this).attr('href') == '#none' ) {
                    return false;
                }

                //$('#search_form').attr('action', $(this).attr('href'));
                //$('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()

    });//end of document.ready

</script>
