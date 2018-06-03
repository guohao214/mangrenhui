select
    *
from
    groupon_order_list as a
where
    a.groupon_order_list_id != 1
    and a.project_id != 9
    and a.phone_number = '13524286564'