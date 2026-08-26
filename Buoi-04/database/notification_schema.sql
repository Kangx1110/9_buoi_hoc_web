CREATE DATABASE IF NOT EXISTS cong_thong_tin_sv
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE cong_thong_tin_sv;

CREATE TABLE IF NOT EXISTS thong_bao (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         tieu_de VARCHAR(255) NOT NULL,
    noi_dung TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,

    doi_tuong ENUM(
                      'SINH_VIEN',
                      'GIANG_VIEN',
                      'TAT_CA'
                  ) NOT NULL DEFAULT 'SINH_VIEN',

    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    trang_thai ENUM(
                       'HIEN_THI',
                       'AN'
                   ) NOT NULL DEFAULT 'HIEN_THI'
    );

-- ==============================
-- DỮ LIỆU MẪU
-- ==============================

INSERT INTO thong_bao
(tieu_de, noi_dung, image_url, doi_tuong)
VALUES
    (
        'Công khai luận án',
        'Công khai thông tin luận án Tiến sĩ của nghiên cứu sinh Trần Thị Thịnh trước khi bảo vệ luận án cấp Trường Đại học Thủ đô Hà Nội. Sinh viên và giảng viên quan tâm có thể đến tham dự tại phòng hội thảo trung tâm nhà A.',
        'https://hnmu.edu.vn/sites/default/files/2026-08/anh-cong-khai.png',
        'TAT_CA'
    ),
    (
        'Thông điệp đầu năm học mới',
        'Thông điệp năm học 2026 - 2027 của Hiệu trưởng gửi tới toàn thể các cán bộ giảng viên, công nhân viên và các bạn sinh viên toàn trường nhân dịp khai giảng.',
        'https://hnmu.edu.vn/sites/default/files/2026-08/logo-hnmu-1.ai_.png',
        'TAT_CA'
    ),
    (
        'Thông báo nộp học phí học kỳ mới',
        'Nhà trường thông báo thời gian nộp học phí cho học kỳ mới. Sinh viên cần hoàn thành đúng thời hạn trước ngày 30/09/2026 thông qua chuyển khoản định danh.',
        'https://hnmu.edu.vn/sites/default/files/2026-06/quyet-dinh_4.jpg',
        'SINH_VIEN'
    );