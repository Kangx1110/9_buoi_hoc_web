<?php

function getNotificationStatus($title, $description)
{
    if (empty($title) || empty($description)) {
        return "Thiếu thông tin";
    }

    if (mb_strlen($description, 'UTF-8') >= 100) {
        return "Thông báo chi tiết";
    }

    return "Thông báo ngắn";
}


/*
 * Chuyển mã đối tượng thành tên hiển thị
 */
function getNotificationAudienceName($audience)
{
    switch ($audience) {

        case 'SINH_VIEN':
            return 'Sinh viên';

        case 'GIANG_VIEN':
            return 'Giảng viên';

        case 'TAT_CA':
            return 'Tất cả';

        default:
            return 'Không xác định';
    }
}