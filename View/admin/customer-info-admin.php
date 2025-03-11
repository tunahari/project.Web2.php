<?php
session_start();
global $conn, $IDCustomer;
$_SESSION['admin'] = true;

$conn = mysqli_connect('localhost', 'root', '', 'projectweb2') or die("Không thể kết nối tới csdl");
if (!$conn) {
    echo "Kết nối không thành công. " . mysqli_connect_error();
    die();
}
// Lấy ID từ method Get
$IDCustomer = (int) $_GET['id-customer'];
$_SESSION['id_customer'] = $IDCustomer;
// Lấy dữ liệu từ method Post
@$TenKhachHang = $_POST['KH_TenKhachHang'];
@$SDTKhachHang = $_POST['KH_SDTKhachHang'];
@$DiaChiKhachHang = $_POST['KH_DiaChiKhachHang'];
@$EmailKhachHang = $_POST['KH_EmailKhachHang'];
@$LoaiKhachHang = $_POST['KH_LoaiKhachHang'];

/* Cập nhật infor Customer */
if (isset($_POST['updateInfoCustomer'])) {
    $sql = "Update `customer` SET 
    `KH_TenKhachHang` = '$TenKhachHang', 
    `KH_SDTKhachHang` = '$SDTKhachHang', 
    `KH_DiaChiKhachHang` = '$DiaChiKhachHang', 
    `KH_EmailKhachHang` = '$EmailKhachHang' 
    WHERE `KH_IDKhachHang` = $IDCustomer";
    $result = mysqli_query($conn, $sql);
}
/* Lấy dữ liệu khách hàng */
$sql = "SELECT * FROM `customer` where `KH_IDKhachHang` = $IDCustomer";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_array($result);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Admin</title>
    <link rel="stylesheet" href="./style-admin.css">
    <link rel="stylesheet" href="./main-admin.css">
    <link rel="stylesheet" href="./customer-info-admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <div class="main">
        <?php include './include.header.php'; ?>
        <div class="containers">
            <?php include './include.menu.php'; ?>

            <div class="Profile__Container">
                <div class="Profile__Container__Left">
                    <div class="Profile__Container__Left__Title">Hồ Sơ Khách Hàng</div>
                    <div class="Profile__Container__Left__Avatar">
                        <img src="../../Controller/product/<?php echo $item['KH_AvatarKhachHang'] ?>" alt="">
                    </div>
                    <div class="Profile__Container__Left__Name">
                        <?php echo $item['KH_TenKhachHang'] ?>
                    </div>

                    <div class="Profile__Container__Left__Info">
                        <div class="Profile__Container__Left__Info__Birth">
                            <div class="Profile__Container__Left__Info__Icon__Group">
                                <div class="Profile__Container__Left__Info__Icon">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <?php echo $item['KH_NgayTaoKhachHang'] ?>
                            </div>
                        </div>
                        <div class="Profile__Container__Left__Info__Address">
                            <div class="Profile__Container__Left__Info__Icon__Group">
                                <div class="Profile__Container__Left__Info__Icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <?php echo $item['KH_DiaChiKhachHang']; ?>
                            </div>
                        </div>
                        <div class="Profile__Container__Left__Info__Email">
                            <div class="Profile__Container__Left__Info__Icon__Group">
                                <div class="Profile__Container__Left__Info__Icon">
                                    <i class="fa-solid fa-envelope-circle-check"></i>
                                </div>
                                <?php echo $item['KH_EmailKhachHang']; ?>
                            </div>
                        </div>
                        <div class="Profile__Container__Left__Info__Phone">
                            <div class="Profile__Container__Left__Info__Icon__Group">
                                <div class="Profile__Container__Left__Info__Icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <?php echo $item['KH_SDTKhachHang'] ?>
                            </div>
                        </div>
                        <div class="Profile__Container__Left__Info__Phone">
                            <div class="Profile__Container__Left__Info__Icon__Group">
                                <div class="Profile__Container__Left__Info__Icon">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                </div>
                                <?php
                                if ($item['KH_TrangThaiDangNhapKhachHang'] == 'login') {
                                    echo "Status: " . $item['KH_TrangThaiDangNhapKhachHang'] . "ed"."🟢" ;
                                }else{
                                    echo "Status: " . $item['KH_TrangThaiDangNhapKhachHang'] . "ed"."🔴" ;
                                };
                                    
                                ?>
                               
                            </div>
                        </div>
                        <hr>
                        <form method="post" id="changePasswordForm">
                            <div class="Info__Input__Box__Content__Input__Item">
                                <label> PassWord (Mã hóa Bcrypt)</label>
                                <input id="KH_MatKhauKhachHang" name="KH_MatKhauKhachHang" type="text" placeholder="" class="Input__NV__Info" id="Input_MatKhau"
                                    value="<?php echo $item['KH_MatKhauKhachHang'] ?>">
                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                <input id="submitPass" name="updatePassWord" type="submit" class="Profile__Save__Pass__Button" value="Submit Change PassWord">
                            </div>
                        </form>

                    </div>

                    <!-- Khóa ng dùg  -->
                    <div>Trạng thái tài khoản:
                        <?php if ($item['KH_XoaKhachHang'] == '1'): ?>
                            <span class="badge badge-danger">❌ Đã Khóa ❌</span>
                        <?php else: ?>
                            <span class="badge badge-success">✅ Hoạt động bình thường ✅</span>
                        <?php endif; ?>
                    </div>
                    <div class="button-group " style="display: flex; justify-content: flex-end; gap: 10px;">
                        <?php if ($item['KH_XoaKhachHang'] == '1'): ?>
                            <button style="background-color: white;"   type="button" id="unlock-btn" class="btn Profile__Save__Info__Button btn-success" onclick="toggleLockAccount('No')">Mở Khóa</button>
                        <?php else: ?>
                            <button style="background-color: white;" type="button" id="lock-btn" class="btn Profile__Save__Info__Button btn-danger" onclick="toggleLockAccount('1')">Khóa</button>
                        <?php endif; ?>
                    </div>

                    <script>
                        function toggleLockAccount(lockStatus) {
                            if (lockStatus === '1' && !confirm("Bạn có chắc muốn khóa tài khoản này?")) {
                                return;
                            }
                            const xhr = new XMLHttpRequest();
                            xhr.open("POST", "lock-account.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.onreadystatechange = function() {
                                if (this.readyState === 4 && this.status === 200) {
                                    alert(this.responseText);
                                    location.reload();
                                }
                            };
                            xhr.send("id=" + <?php echo $IDCustomer; ?> + "&lock_status=" + lockStatus);
                        }
                    </script>


                </div>

                <div class="Profile__Container__Right">
                    <form method="POST" id="updateInfoCustomerForm">
                        <div class="Profile__Container__Right__Info">
                            <div class="Profile__Container__Right__Info__Input__Box">
                                <div class="Profile__Container__Right__Info__Input__Box__Title">
                                    Thông Tin Khách Hàng
                                </div>
                                <?php
                                if (!empty($item)) {
                                ?>

                                    <div class="Profile__Container__Right__Info__Input__Box__Content">
                                        <div class="Profile__Container__Right__Info__Input__Box__Content__Avatar">
                                            <div class="Info__Input__Box__Content__Avatar__Image" id="previewAvtEmployeeImage">
                                                <img src="../../Controller/product/<?php echo $item['KH_AvatarKhachHang'] ?>" alt=""
                                                    id="avtEmployeeDefault">
                                            </div>
                                            <!-- Upload file -->
                                            <!-- <div class="Info__Input__Box__Content__Avatar__Update">
                                                <label for="inputUpdateAvatarEmployee">Chọn File</label>
                                                <input type="file" id="inputUpdateAvatarEmployee">
                                                <button type="button" id="submitUpdateAvatarEmployee">Cập nhật<i class="fa-solid fa-cloud-arrow-up"></i></button>
                                            </div>  -->
                                            <!-- Upload file -->
                                        </div>
                                        <div class="Profile__Container__Right__Info__Input__Box__Content__Input">
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_IDNhanVien">Customer ID:</label>
                                                <input type="text" placeholder="" class="Input__NV__Info" id="Input_NV_IDNhanVien"
                                                    value="KH<?php echo $item['KH_IDKhachHang'] ?>" readonly>
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_TenNhanVien">Tên Đầy Đủ:</label>
                                                <input name="KH_TenKhachHang" type="text" placeholder="" class="Input__NV__Info" id="KH_TenKhachHang"
                                                    value="<?php echo $item['KH_TenKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                        </div>

                                        <div class="Profile__Container__Right__Info__Input__Box__Content__Input">
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_SoDienThoaiNhanVien">Số Điện Thoại:</label>
                                                <input name="KH_SDTKhachHang" type="text" placeholder="" class="Input__NV__Info" id="KH_SDTKhachHang"
                                                    value="<?php echo $item['KH_SDTKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_DiaChiNhanVien">Địa Chỉ:</label>
                                                <input name="KH_DiaChiKhachHang" type="text" placeholder="" class="Input__NV__Info" id="KH_DiaChiKhachHang"
                                                    value="<?php echo $item['KH_DiaChiKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                        </div>

                                        <div class="Profile__Container__Right__Info__Input__Box__Content__Input">
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_EmailNhanVien">Email:</label>
                                                <input name="KH_EmailKhachHang" type="text" placeholder="" class="Input__NV__Info" id="KH_EmailKhachHang"
                                                    value="<?php echo $item['KH_EmailKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_NgaySinhNhanVien">Trạng Thái Đăng Nhập</label>
                                                <input type="text" disabled placeholder="" class="Input__NV__Info"
                                                    value="<?php echo $item['KH_TrangThaiDangNhapKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                        </div>
                                        <div class="Profile__Container__Right__Info__Input__Box__Content__Input">

                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_NgayTaoNhanVien">Ngày Tạo:</label>
                                                <input type="date" placeholder="" class="Input__NV__Info" id="Input_NV_NgayTaoNhanVien" readonly
                                                    value="<?php echo $item['KH_NgayTaoKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>
                                            <div class="Info__Input__Box__Content__Input__Item">
                                                <label for="Input_NV_NgaySinhNhanVien">Loai Khách Hàng</label>
                                                <input type="text" disabled placeholder="" class="Input__NV__Info"
                                                    value="<?php echo $item['KH_LoaiKhachHang'] ?>">
                                                <div class="Input__NV__Info__Error">Input invalid, please check again</div>
                                            </div>

                                        </div>

                                    </div>


                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="Profile__Save__Info__Button">
                            <input id="btnSaveInfoCustomer" name="updateInfoCustomer" type="submit" class="Profile__Save__Info__Button__Save" value="Save">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- LOADING -->
    <div class="loading__box">
        <p>Đang Thực Hiện...</p>
        <div class="loading"></div>
    </div>
    <!-- ALERT NOTIFY SUCCESS -->
    <div class="alert__notify__box__success">
        <div class="alert__notify__box__success__close"><i class="fa-solid fa-xmark"></i></div>
        <div class="alert__notify__box__success__left">
            <div class="alert__notify__box__success__left__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="alert__notify__box__success__right">
            <div class="alert__notify__box__success__right__title">Thành Công</div>
            <div class="alert__notify__box__success__right__content">aaaaaaaaaaaaaa</div>
        </div>
        <div class="alert__notify__box__success__progress"></div>
    </div>
    <!-- ALERT NOTIFY Failed -->
    <div class="alert__notify__box__failed">
        <div class="alert__notify__box__failed__close"><i class="fa-solid fa-xmark"></i></div>
        <div class="alert__notify__box__failed__left">
            <div class="alert__notify__box__failed__left__icon">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
        <div class="alert__notify__box__failed__right">
            <div class="alert__notify__box__failed__right__title">Thất Bại</div>
            <div class="alert__notify__box__failed__right__content"></div>
        </div>
        <div class="alert__notify__box__failed__progress"></div>
    </div>
</body>

</html>
<script src="../../Controller/class/controller.function.js"></script>
<script src="../../Controller/class/controller.validate.js"></script>
<script src="../../Controller/admin/controller-admin.info-customer.js"></script>


<script>
    $(document).ready(function() {
        function checkPower() {
            const ClassFuction = new HandlingFunctions();
            ClassFuction.getAjaxPost('../../Controller/admin/controller-admin.checkpower.php', {
                checkPower: 'check-power'
            }).done(function(response) {
                if (response.trim() === '-1' || response.trim() === '0') {
                    ClassFuction.getAjaxPost('../../Controller/admin/controller-admin.logout.php', {
                        logoutAdmin: 'log-out-admin'
                    }).done(function(response) {
                        if (response.trim() === 'logout-success') {
                            window.location = './login-admin.php'
                        }
                    });
                }
            });
        }
        checkPower()
    })
</script>