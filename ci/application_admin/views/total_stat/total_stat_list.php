<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">전체통계 > 목록</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="div">년월검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select id="year" name="year" class="form-control">
                            <?php echo get_select_option_year("", date("Y", time()), 2016, date("Y", time())); ?>
                        </select>
                    </div>
                    <div class="pull-left" style="margin-left:20px;">
                        <select id="month" name="month" class="form-control">
                            <?php echo get_select_option_month("", date("m", time())); ?>
                        </select>
                    </div>
                    <!--<div class="pull-left" style="margin-left:20px;">-->
                    <!--    <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>-->
                    <!--</div>-->
                </div>
            </div>
        </form>

        <hr style="width:100%;margin:0 0 10px 0;"/>
    </div>

    <div class="row">
        <div class="row mgb10"></div>

        <div id="total_stat_list" style="position:relative;"></div>
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

    //document.ready
    $(function () {
        $('[name="year"],[name="month"]').on('change', function(){
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
                loadingBar.show($('#total_stat_list'));
            },
            success: function(result) {
                $('#total_stat_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#total_stat_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready
</script>