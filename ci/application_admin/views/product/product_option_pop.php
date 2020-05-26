<div id="product_option_wrap"></div>

<script type="text/javascript">

    $(function(){
        get_option_page();
    });

    function get_option_page(){
        $.ajax({
            url : '/product/option',
            data : <?=json_encode($aInput)?>,
            type : 'post',
            dataType : 'html',
            success : function(result) {
                $('#product_option_wrap').html(result);
            }
        });
    }

</script>
