<?php
//kết nối database truyền thống
$conn = mysqli_connect('localhost', 'root', '', 'projectweb2') or die("Không thể kết nối tới csdl");
if (!$conn) {
    echo "Kết nối không thành công. " . mysqli_connect_error();
    die();
}

$codeVerify = $_GET['code-verify'];

$sql = "SELECT * FROM `employee` WHERE `NV_MaChoXacThucNhanVien` = '$codeVerify'";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);

// echo $item['NV_XacThucEmailNhanVien'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Nhân viên: <?php echo $item['NV_TenNhanVien'] ?></p>
    <p>Mã Nhân Viên: <?php echo ('NV'.$item['NV_IDNhanVien']) ?></p>
    <p>Email: <?php echo $item['NV_EmailNhanVien'] ?></p>
    <p>Trạng Thái xác thức: <?php echo $item['NV_TrangThaiTaiKhoanNhanVien'] ?></p>
    <h1>Mã xác thực: <?php echo $item['NV_XacThucEmailNhanVien'] ?></h1>
</body>
</html>