select
    a.groupon_project_code,
    a.groupon_order_code,
    b.*
from
    groupon_order as a
    left join groupon_order_list as b on a.groupon_order_id = b.groupon_order_id
where
    a.disabled = 0
    and b.disabled = 0
    and b.groupon_order_list_no = '{$listNo}'
    and b.open_id = '{$openId}'