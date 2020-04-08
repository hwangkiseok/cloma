<table class="table table-bordered table-responsive table-hover">
    <tr>
        <th>쿠폰코드</th>
        <th>유형</th>
        <th>쿠폰명</th>
        <th>금액</th>
        <th>최소구매금액</th>
        <th>발급일</th>
        <th>만료일</th>
        <th>사용일</th>
        <th>상태</th>
        <th>관리</th>
    </tr>

    <?php foreach($data as $key => $item) { ?>

        <tr data-uid="<?php echo $item['uid']; ?>">
            <td><?php echo $item['code']; ?></td>
            <td><?php echo $item['kind_txt']; ?></td>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['price']; ?> <?php echo $item['price_max']; ?></td>
            <td><?php echo $item['min_orderprice']; ?></td>
            <td><?php echo $item['reg_datetime']; ?></td>
            <td><?php echo $item['expire_datetime']; ?></td>
            <td><?php echo $item['use_datetime']; ?></td>
            <td><?php echo $item['state_text']; ?></td>
            <td>
                <?php if( $item['use_able'] == "Y" ) { ?>
                <a href="#none" class="btn btn-xs btn-danger" onclick="coupon_delete('<?php echo $item['uid']; ?>');">삭제</a>
                <?php } ?>
            </td>
        </tr>

    <?php }//end of foreach() ?>
</table>