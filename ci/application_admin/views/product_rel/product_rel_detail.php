
<p class="alert alert-info">
    <b><?=$oProductInfo['p_name']?></b> 연관상품 리스트
    <a role="button" style="margin-top: -2px;" target="_blank" href="<?=$this->config->item('default_http')?>/product/detail/?p_num=<?=$oProductInfo['p_num']?>" class="btn btn-info btn-xs pull-right">상품바로가기</a>
</p>
<table class="table table-bordered">
    <tr>
        <th class="active">No.</th>
        <th class="active">연관상품</th>
        <th class="active">노출수</th>
        <th class="active">클릭수</th>
        <th class="active">주문수</th>
    </tr>

    <? foreach ($rel_list as $key => $row) { $disable_str = $bg_class = ''; $sort_num    = $key+1;

        if($row['view_yn'] == 'N'){ $bg_class = 'bg-danger'; $sort_num = '-'; ?>
            <?if($rel_list[$key]['view_yn'] != $rel_list[$key-1]['view_yn']){?>
                <tr class="<?=$bg_class?>">
                    <td colspan="5">== 이전상품 ==</td>
                </tr>
            <?}?>
        <?}else if($row['isAble'] == 'N'){ $bg_class = 'bg-warning'; $sort_num = '-'; ?>
            <?if($rel_list[$key]['isAble'] != $rel_list[$key-1]['isAble']){?>
                <tr class="<?=$bg_class?>">
                    <td colspan="5">== 품절상품 ==</td>
                </tr>
            <?}?>
        <? } ?>

        <tr class="<?=$bg_class?>">
            <td><?=$sort_num?></td>
            <td style="text-align: left!important;">
                <a target="_blank" href="<?=$this->config->item('default_http')?>/product/detail/?p_num=<?=$row['c_pnum']?>">
                    <?=$row['p_name']?>
                    <?=$row['reserve_prod']?>
                </a>
            </td>
            <td><?=number_format($row['view_cnt'])?></td>
            <td><?=number_format($row['click_cnt'])?></td>
            <td><?=number_format($row['order_cnt'])?></td>
        </tr>

    <?}?>

</table>

