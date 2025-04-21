<?php
// get_top_customers.php

@session_start(); // Cần thiết nếu có kiểm tra quyền hoặc session liên quan

// 1. Kết nối cơ sở dữ liệu
require_once '../../Model/database/connectDataBase.php'; // Điều chỉnh đường dẫn nếu cần

// --- Định nghĩa trạng thái đơn hàng (Quan trọng: Phải giống main-admin.php) ---
if (!defined('ORDER_STATUS_COMPLETED')) define('ORDER_STATUS_COMPLETED', 2);
// --- Kết thúc định nghĩa ---

header('Content-Type: application/json'); // Quan trọng: Báo cho trình duyệt biết đây là JSON

$response = [
    'success' => false,
    'data' => [],
    'message' => '',
    'startDate' => null,
    'endDate' => null
];

// 2. Lấy và xử lý ngày lọc từ GET request
$startDateInput = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$endDateInput = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

// Validate date format
$validStartDate = $startDateInput && (DateTime::createFromFormat('Y-m-d', $startDateInput) !== false);
$validEndDate = $endDateInput && (DateTime::createFromFormat('Y-m-d', $endDateInput) !== false);

$startDate = $validStartDate ? $startDateInput : null;
$endDate = $validEndDate ? $endDateInput : null;

// *** THAY ĐỔI LOGIC: Chỉ cần ít nhất một ngày hợp lệ để lọc ***
$canFilter = $startDate || $endDate;

// Nếu có cả hai ngày và ngày bắt đầu sau ngày kết thúc, hoán đổi chúng
if ($startDate && $endDate && $startDate > $endDate) {
    list($startDate, $endDate) = [$endDate, $startDate];
}

$response['startDate'] = $startDate; // Gửi lại ngày đã xử lý (có thể là null)
$response['endDate'] = $endDate;   // Gửi lại ngày đã xử lý (có thể là null)

$topCustomers = [];

// Chỉ thực hiện truy vấn nếu có ít nhất một ngày hợp lệ
if ($canFilter) {
    try {
        if (!class_exists('ConnectDataBase')) {
             $response['message'] = 'Lỗi nghiêm trọng: Không tìm thấy lớp ConnectDataBase.';
             echo json_encode($response);
             exit;
        }
        $ConnectDataBase_Ajax = new ConnectDataBase();
        $conn_ajax = $ConnectDataBase_Ajax->connectDB();

        if (!$conn_ajax) {
            $response['message'] = 'Lỗi kết nối cơ sở dữ liệu.';
            echo json_encode($response);
            exit;
        }

        // --- Xây dựng mệnh đề WHERE động cho ngày ---
        $dateConditions = [];
        $params = [':status_completed' => ORDER_STATUS_COMPLETED]; // Tham số bắt buộc

        if ($startDate) {
            $dateConditions[] = "b.DH_NgayDatDonHang >= :start_date";
            $params[':start_date'] = $startDate;
        }
        if ($endDate) {
            $dateConditions[] = "b.DH_NgayDatDonHang <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $dateWhereClause = "";
        if (!empty($dateConditions)) {
            $dateWhereClause = " AND " . implode(" AND ", $dateConditions);
        }
        // --- Kết thúc xây dựng WHERE động ---


        // --- Truy vấn Top 5 Khách Hàng với WHERE động ---
        $sqlTopCustomers = "
            SELECT
                c.KH_TenKhachHang,
                c.KH_IDKhachHang,
                COALESCE(SUM(bd.CTDH_SLSanPham * CAST(p.SP_GiaBanSanPham AS DECIMAL(15,2))), 0) AS total_spent
            FROM
                customer AS c
            JOIN
                bill AS b ON CAST(c.KH_IDKhachHang AS UNSIGNED) = CAST(b.DH_IDKhachHang AS UNSIGNED)
            JOIN
                billdetails AS bd ON CAST(b.DH_IDDonHang AS UNSIGNED) = CAST(bd.CTDH_IDDonHang AS UNSIGNED)
            JOIN
                product AS p ON CAST(bd.CTDH_IDSanPham AS UNSIGNED) = p.SP_IDSanPham
            WHERE
                b.DH_TrangThaiDonHang = :status_completed
                {$dateWhereClause} -- Sử dụng mệnh đề WHERE động
                AND c.KH_XoaKhachHang = 'No'
            GROUP BY
                c.KH_IDKhachHang, c.KH_TenKhachHang
            ORDER BY
                total_spent DESC
            LIMIT 5;
        ";

        $stmtTopCustomers = $conn_ajax->prepare($sqlTopCustomers);

        // Bind các tham số động
        foreach ($params as $key => $val) {
            // Xác định kiểu dữ liệu (đơn giản hóa: INT hoặc STR)
            $paramType = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmtTopCustomers->bindValue($key, $val, $paramType);
        }

        $stmtTopCustomers->execute();
        $topCustomers = $stmtTopCustomers->fetchAll(PDO::FETCH_ASSOC);

        $response['success'] = true;
        $response['data'] = $topCustomers;
        if (empty($topCustomers)) {
            // Điều chỉnh thông báo dựa trên ngày nhập
            if ($startDate && $endDate) {
                 $response['message'] = 'Không có dữ liệu khách hàng mua hàng trong khoảng thời gian này.';
            } elseif ($startDate) {
                 $response['message'] = 'Không có dữ liệu khách hàng mua hàng từ ngày ' . htmlspecialchars($startDate) . ' trở đi.';
            } elseif ($endDate) {
                 $response['message'] = 'Không có dữ liệu khách hàng mua hàng đến ngày ' . htmlspecialchars($endDate) . '.';
            }
        }

    } catch (PDOException $e) {
        error_log("Lỗi CSDL AJAX (get_top_customers.php): " . $e->getMessage());
        $response['message'] = 'Đã xảy ra lỗi khi truy vấn dữ liệu top khách hàng.';
        // Có thể thêm chi tiết lỗi vào response nếu đang ở môi trường dev
        // $response['error_detail'] = $e->getMessage();
    } finally {
        $conn_ajax = null; // Đóng kết nối
    }
} else {
    // Không có ngày hợp lệ nào được cung cấp (có thể là reset hoặc nhập sai cả 2)
    $response['success'] = true; // Vẫn thành công về mặt kỹ thuật, nhưng không có data lọc
    $response['data'] = []; // Trả về mảng rỗng
    if ($startDateInput || $endDateInput) {
         // Nếu người dùng có nhập gì đó nhưng không hợp lệ
         $response['message'] = 'Vui lòng chọn ngày bắt đầu và/hoặc ngày kết thúc hợp lệ.';
    } else {
         // Nếu là reset (không có tham số ngày)
         $response['message'] = 'Chọn khoảng thời gian để lọc top khách hàng.'; // Hoặc để trống
    }
}

// Trả về kết quả dạng JSON
echo json_encode($response);
exit; // Dừng script tại đây

?>
