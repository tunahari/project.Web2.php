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
    
    $sql = "UPDATE `customer` SET `KH_XoaKhachHang` = '$lock_status' WHERE `KH_IDKhachHang` = $id";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        echo $lock_status == '1' ? "Đã khóa tài khoản thành công!" : "Đã mở khóa tài khoản thành công!";
    } else {
        echo "Có lỗi xảy ra: " . mysqli_error($conn);
    }
} else {
    echo "Dữ liệu không hợp lệ!";
}

mysqli_close($conn);
?>