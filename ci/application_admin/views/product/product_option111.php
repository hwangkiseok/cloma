<form method="post" id="pop_insert_form" action="/product/upsert_option_group">

    <input type="hidden" name="p_num" value="<?=$aProductInfo['p_num']?>" />
    <input type="hidden" name="depth" value="<?=$aInput['depth']?>" />
    <input type="hidden" name="type" value="<?=$aInput['type']?>" />
    <input type="hidden" name="option_token" value="<?=$aInput['option_token']?>" />

    <div style="border:1px solid #ddd;border-radius: 5px;margin-top: -72px; margin-bottom: 8px;padding: 16px;position: fixed;width: calc(100% - 32px);background: #fff;z-index: 10">
        <button class="btn btn-sm btn-success option_add">옵션추가</button>
    </div>

    <table class="table table-hover" style="margin-top: 64px">

        <colgroup>
            <col style="width: 30px" />
            <col style="width: 150px" />
            <col style="width: 150px" />
            <col style="width: 150px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 120px" />
            <col style="width: 150px" />
        </colgroup>

        <thead>
        <tr>
            <th class="active">No.</th>
            <th class="active">1차 옵션명</th>
            <th class="active">2차 옵션명</th>
            <th class="active">3차 옵션명</th>
            <th class="active">재고1</th>
            <th class="active">재고2</th>
            <th class="active">재고3</th>
            <th class="active">판매가</th>
            <th class="active">소비자가</th>
            <th class="active">공급가</th>
            <th class="active">삭제</th>
        </tr>
        </thead>

        <tbody>

        <? foreach ($aProductOptionList as $k => $r) { ?>

            <tr class="update">
                <input type="hidden" name="option_group_id[]" value="<?=$r['option_group_id']?>" />
                <input type="hidden" name="act_type[]" value="update" />
                <td><?=$k+1?></td>
                <td><input type="text" name="option_group1[]" value="<?=$r['option_group1']?>" class="form-control"  /></td>
                <td><input type="text" name="option_group2[]" value="<?=$r['option_group2']?>" class="form-control"  /></td>
                <td><input type="text" name="option_group3[]" value="<?=$r['option_group3']?>" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_group_stock1[]" value="<?=$r['option_group_stock1']?>" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_group_stock2[]" value="<?=$r['option_group_stock2']?>" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_group_stock3[]" value="<?=$r['option_group_stock3']?>" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_sale_price[]" value="<?=$r['option_sale_price']?>" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_org_price[]" value="<?=$r['option_org_price']?>" class="form-control"  /></td>
                <td><input type="text" numberOnly name="option_supply_price[]" value="<?=$r['option_supply_price']?>" class="form-control"  /></td>

                <td> <button class="btn btn-danger btn-xs option_group_del">삭제</button> </td>
            </tr>

        <? } ?>

        <tr class="insert">
            <input type="hidden" name="option_group_id[]" value="" />
            <input type="hidden" name="act_type[]" value="insert" />
            <td><?=count($aProductOptionList)+1?></td>
            <td><input type="text" name="option_group1[]" value="" class="form-control"  /></td>
            <td><input type="text" name="option_group2[]" value="" class="form-control"  /></td>
            <td><input type="text" name="option_group3[]" value="" class="form-control"  /></td>
            <td><input type="text" numberOnly name="option_group_stock1[]" value="" class="form-control"  /></td>
            <td><input type="text" numberOnly name="option_group_stock2[]" value="" class="form-control"  /></td>
            <td><input type="text" numberOnly name="option_group_stock3[]" value="" class="form-control"  /></td>
            <td><input type="text" numberOnly name="option_sale_price[]" value="" class="form-control"  /></td>
            <td><input type="text" numberOnly name="option_org_price[]" value="" class="form-control"  /></td>
            <td><input type="text" numberOnly name="option_supply_price[]" value="" class="form-control"  /></td>
            <td> <button class="btn btn-danger btn-xs option_group_del">삭제</button> </td>
        </tr>
        </tbody>
    </table>

</form>

<script>
    $(function(){

        $('#pop_insert_form').on('submit',function(){

        });

        $('#pop_insert_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
            },
            success: function(result) {

                if(result.msg) alert(result.msg);
                if(result.success == true) get_option_page();
                $('.option_wrap').scrollTop(9999999);


            },
            complete: function() {

            }

        });//end of ajax_form()


        var add_html  = '<tr class="insert">';
        add_html += '<input type="hidden" name="option_group_id[]" value="" />';
        add_html += '<input type="hidden" name="act_type[]" value="insert" />';
        add_html += '   <td>{num}</td>';
        add_html += '   <td><input type="text" name="option_group1[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" name="option_group2[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" name="option_group3[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" numberOnly name="option_group_stock1[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" numberOnly name="option_group_stock2[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" numberOnly name="option_group_stock3[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" numberOnly name="option_sale_price[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" numberOnly name="option_org_price[]" value="" class="form-control"  /></td>';
        add_html += '   <td><input type="text" numberOnly name="option_supply_price[]" value="" class="form-control"  /></td>';
        add_html += '   <td> <button class="btn btn-danger btn-xs option_group_del">삭제</button> </td>';
        add_html += '</tr>';

        $('.option_add').on('click',function(e){
            e.preventDefault();

            var curr_num = parseInt($('#pop_insert_form table tbody tr').length);
            var add_num = curr_num+1;
            var html = add_html.replace(/{num}/g,add_num);
            html = html.replace(/{curr_num}/g,curr_num);

            $('#pop_insert_form table tbody').append(html);
            $('.option_wrap').scrollTop(9999999);

        });

    })

</script>
