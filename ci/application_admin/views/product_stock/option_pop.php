
<style>
    .stock_list {line-height: 22px;}
    .stock_list li {font-size: 14px;}
</style>

<p class="alert alert-danger" style="font-size: 18px"><?=$aProductInfo['item_name']?></p>

<ul class="stock_list">
<? foreach ($option_arr as $r) {
    $option_name  = $r['option_depth1'];
    $option_name .= $r['option_depth2']?' / '.$r['option_depth2']:'';
    $option_name .= $r['option_depth3']?' / '.$r['option_depth3']:'';
?>
<li><?=$option_name?> : 재고 <?=number_format($r['option_count'])?>개</li>
<? } ?>
</ul>

