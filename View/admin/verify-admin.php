<?php
if (isset($_GET['code-verify']) && $_GET['code-verify'] !== '') {
    require_once '../../Model/database/connectDataBase.php';
    require_once '../../Model/admin/model-admin.employee.php';
    $EmployeeClass = new Employee;
    $codeVerify = $_GET['code-verify'];
    $EmployeeClass->setNV_MaChoXacThucNhanVien($codeVerify);
    if ($EmployeeClass->getEmployeeByCodeVerify()[0]['NV_EmailNhanVien'] === 'Chưa cập nhật') {
        $emailDefault = '';
    } else {
        $emailDefault = $EmployeeClass->getEmployeeByCodeVerify()[0]['NV_EmailNhanVien'];
    }
} else {
    echo 'Không có';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản</title>

    <link href='../include/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../include/fontawesome/css/all.min.css" />
    <script src="../include/jquery.min.js"></script>
    <script src="../include/fontawesome/js/all.min.js "></script>
 
    <link rel="stylesheet" href="../include/swiper-bundle.min.css" />

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
    
    <link rel="stylesheet" href="./verify-admin.css">
</head>

<body>
    <div class="Verify__Admin__Container">
        <div class="Verify__Admin__Header">
            <a href="./login-admin.php" class="Verify__Admin__Header__Logo">SB MOBILE</a>
            <div class="Verify__Admin__Header__Info">
                <div class="Verify__Admin__Header__Info__Item">
                    <i class="fa-solid fa-phone-flip"></i>
                    0369969650
                </div>
                <div class="Verify__Admin__Header__Info__Item">
                    <i class="fa-brands fa-facebook-f"></i>
                    <a href="#">Facebook</a>
                </div>
                <div class="Verify__Admin__Header__Info__Item">
                    <i class="fa-solid fa-envelope"></i>
                    <a href="./display_code.php?code-verify=<?php echo $codeVerify ?>" target="_blank">Tunahari86@gmail.com</a>
                </div>
            </div>
        </div>
        <div class="Verify__Admin__Title">XÁC THỰC TÀI KHOẢN</div>
        <div class="Verify__Admin__Box">
            <div class="Verify__Admin__Input__Group">
                <div class="Verify__Admin__Input__Item Verify__Admin__Input__Item__SendCode">
                    <label for="">Email: </label>
                    <input type="text" value="<?php echo $emailDefault ?>" id="EmailVerify">
                    <div class="error-email">Email không hợp lệ, vui lòng nhập lại!</div>
                </div>
                <div class="Verify__Admin__SendCode" value="<?php echo $codeVerify ?>">Gửi Mã Xác Thực</div>
                <div class="Verify__Admin__Input__Item Verify__Admin__Input__Item__CheckCode">
                    <label for="">Mã xác thực: </label>
                    <input type="text" id="CodeVerify">
                    <div class="error-code">Mã xác thực không hợp lệ, vui lòng nhập lại!</div>
                </div>
                <div class="Verify__Admin__CheckCode" id="CheckCode">Xác Thực Tài Khoản</div>
                <div class="Verify__Admin__Input__Item Verify__Admin__Input__Item__Password">
                    <label for="">Mật khẩu mới: </label>
                    <input type="text" id="PassVerify">
                    <div class="error-pass">MK tối thiểu 5 ký tự, bao gồm chữ hoa, thường và số</div>
                </div>
                <div class="Verify__Admin__Input__Item Verify__Admin__Input__Item__Password">
                    <label for="">Xác nhận mật khẩu mới: </label>
                    <input type="text" id="RePassVerify">
                    <div class="error-repass">Mật khẩu không trùng khớp, vui lòng nhập lại!</div>
                </div>
                <div class="Verify__Admin__Password">Đổi Mật Khẩu</div>
            </div>
        </div>
    </div>

    <div class="Verify__Admin__Alert">
        <div class="Verify__Admin__Alert__Top">
            <div class="Verify__Admin__Alert__Top__Icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
        <div class="Verify__Admin__Alert__Bottom">
            <div class="Verify__Admin__Alert__Bottom__Title"></div>
            <div class="Verify__Admin__Alert__Bottom__Text"></div>
        </div>
    </div>

    <!-- LOADING -->
    <div class="loading__box">
        <p>Đang Kiểm Tra...</p>
        <p></p>
        <div class="loading"></div>
    </div>
</body>

</html>
<script src="../../Controller/class/controller.function.js"></script>
<script src="../../Controller/class/controller.validate.js"></script>
<script src="../../Controller/admin/controller-admin.verify.js"></script>