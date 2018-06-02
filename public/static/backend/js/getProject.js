/**
 * Created by GuoHao on 2016/3/22.
 */

$(document).ready(function () {

    var $shop = $('#shop_id'),
        $project = $('#project_id');


    $shop.on('change', getProjectList);

    function getProjectList() {
        var $shopId = $shop.val();

        $.ajax({
            url: '/backend/project/readAllProjectByShopId/' + $shopId,
            dataType: 'json',
            beforeSend: function () {
                //$project.children('option').not(':first').remove();
            },
            success: function (data) {
                $project.append(data);
            },
            error: function () {
                alert('获取项目失败！');
            }
        })
    }


    getProjectList();

})