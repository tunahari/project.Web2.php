<?php
// session_start();
// $conn = mysqli_connect('localhost', 'root', '', 'projectweb2') or die("Không thể kết nối tới csdl");
// if (!$conn) {
//     echo "Kết nối không thành công. " . mysqli_connect_error();
//     die();
// }
// $id_customer = $_SESSION['id_customer'];
// @$MatKhau = trim($_POST['KH_MatKhauKhachHang']);
// $PassWordHashed = password_hash($MatKhau, PASSWORD_DEFAULT);

// if (!empty($_POST['KH_MatKhauKhachHang'])) {
//     $sql = "UPDATE `customer` SET `KH_MatKhauKhachHang` = '$PassWordHashed' WHERE `KH_IDKhachHang` = $id_customer";
//     $result = mysqli_query($conn, $sql);
//     if ($result) {
//         // Gửi lại phản hồi nếu thành công
//         echo $PassWordHashed;
//     } else {
//         // Gửi lại phản hồi nếu thất bại
//         json_encode(['status' => 'error']);
//     }
// } else {
//     json_encode(['status' => 'error']);
// }
//     exit;


// // if (isset($_POST['updateInfoCustomer'])) {

// //     // Lấy dữ liệu từ AJAX
  
    
// //     // Truy vấn SQL để cập nhật
// //     $sql = "UPDATE `customer` 
// //             SET `KH_TenKhachHang` = '$TenKH', 
// //                 `KH_SDTKhachHang` = '$SDTKH', 
// //                 `KH_DiaChiKhachHang` = '$DiaChiKH', 
// //                 `KH_EmailKhachHang` = ' $EmailKH' 
// //             WHERE `KH_IDKhachHang` = '$id_customer'";

// //     $result = mysqli_query($conn, $sql);

// //     if ($result) {
// //         // Phản hồi JSON cho AJAX
// //         echo json_encode([
// //             'success' => true,
    
// //             'data' => [
// //                 'KH_TenKhachHang' => "$TenKH",
// //                 'KH_SDTKhachHang' => "$SDTKH",
// //                 'KH_DiaChiKhachHang' => "$DiaChiKH",
// //                 'KH_EmailKhachHang' => "$EmailKH"
// //             ]
// //         ]);
// //     } else {
// //         // Xử lý lỗi nếu có
// //         echo json_encode([
// //             'success' => false,
// //             'message' => 'Không thể cập nhật thông tin khách hàng: ' . mysqli_error($conn)
// //         ]);
// //     }
// //     exit; // Dừng PHP sau khi trả phản hồi
// // }

session_start();
$conn = mysqli_connect('localhost', 'root', '', 'projectweb2') or die("Không thể kết nối tới csdl");
if (!$conn) {
    echo "Kết nối không thành công. " . mysqli_connect_error();
    die();
}
$id_customer = $_SESSION['id_customer'];

if (isset($_POST['updateInfoCustomer'])) {
    $TenKH = mysqli_real_escape_string($conn, $_POST['KH_TenKhachHang']);
    $SDTKH = mysqli_real_escape_string($conn, $_POST['KH_SDTKhachHang']);
    $DiaChiKH = mysqli_real_escape_string($conn, $_POST['KH_DiaChiKhachHang']);
    $EmailKH = mysqli_real_escape_string($conn, $_POST['KH_EmailKhachHang']);

    $sql = "UPDATE `customer` 
            SET `KH_TenKhachHang` = '$TenKH', 
                `KH_SDTKhachHang` = '$SDTKH', 
                `KH_DiaChiKhachHang` = '$DiaChiKH', 
                `KH_EmailKhachHang` = '$EmailKH' 
            WHERE `KH_IDKhachHang` = $id_customer";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin khách hàng thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật thông tin khách hàng: ' . mysqli_error($conn)]);
    }
    exit;
}

//Phần xử lý đổi mật khẩu (giữ nguyên)
@$MatKhau = trim($_POST['KH_MatKhauKhachHang']);
$PassWordHashed = password_hash($MatKhau, PASSWORD_DEFAULT);

if (!empty($_POST['KH_MatKhauKhachHang'])) {
    $sql = "UPDATE `customer` SET `KH_MatKhauKhachHang` = '$PassWordHashed' WHERE `KH_IDKhachHang` = $id_customer";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo $PassWordHashed; //This should probably be a JSON response as well for consistency.
    } else {
        json_encode(['status' => 'error']);
    }
    exit;
} else {
    json_encode(['status' => 'error']);
}
mysqli_close($conn);
?>

