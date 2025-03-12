<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo "Bạn không có quyền thực hiện thao tác này!";
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'projectweb2') or die("Không thể kết nối tới csdl");
if (!$conn) {
    echo "Kết nối không thành công. " . mysqli_connect_error();
    die();
}


if (isset($_POST['id']) && isset($_POST['lock_status'])) {
    $id = (int)$_POST['id'];
    $lock_status = $_POST['lock_status']; // Nhận giá trị là 0 hoặc 1

    // Đảm bảo giá trị là '0' hoặc '1' dưới dạng chuỗi
    $lock_status = ($lock_status == '1') ? '1' : 'No';

    // Lấy trạng thái đăng nhập hiện tại của khách hàng
    $sql_get_login_status = "SELECT `KH_TrangThaiDangNhapKhachHang` FROM `customer` WHERE `KH_IDKhachHang` = $id";
    $result_get_login_status = mysqli_query($conn, $sql_get_login_status);
    $login_status_row = mysqli_fetch_assoc($result_get_login_status);
    $current_login_status = $login_status_row['KH_TrangThaiDangNhapKhachHang'];

    // Nếu khóa tài khoản và đang login, thì chuyển thành logout
    if ($lock_status == '1' && $current_login_status == 'login') {
        $sql_update_login_status = "UPDATE `customer` SET `KH_TrangThaiDangNhapKhachHang` = 'logout' WHERE `KH_IDKhachHang` = $id";
        mysqli_query($conn, $sql_update_login_status);
    // Nếu mở khóa tài khoản thì set status về logout
    } else if($lock_status == 'No'){
         $sql_update_login_status = "UPDATE `customer` SET `KH_TrangThaiDangNhapKhachHang` = 'logout' WHERE `KH_IDKhachHang` = $id";
         mysqli_query($conn, $sql_update_login_status);
    }

    // Cập nhật trạng thái khóa/mở khóa
    $sql_update_lock_status = "UPDATE `customer` SET `KH_XoaKhachHang` = '$lock_status' WHERE `KH_IDKhachHang` = $id";
    $result_update_lock_status = mysqli_query($conn, $sql_update_lock_status);

    if ($result_update_lock_status) {
        echo $lock_status == '1' ? "Đã khóa tài khoản thành công!" : "Đã mở khóa tài khoản thành công!";
    
    } else {
        echo "Có lỗi xảy ra: " . mysqli_error($conn);
    }
} else {
    echo "Dữ liệu không hợp lệ!";
}


mysqli_close($conn);
?>
