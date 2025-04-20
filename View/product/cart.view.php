<?php
session_start();
require_once '../../Model/utils.php'; // Đường dẫn đến file utils.php
require_once '../../Model/database/connectDataBase.php'; // File kết nối database của bạn
$conn = mysqli_connect('localhost', 'root', '', 'projectweb2'); // Kết nối database
// ... phần code còn lại của product.view.php ...
checkAccountStatusAndRedirect($conn); // Gọi hàm kiểm tra trạng thái


require_once '../../Model/database/connectDataBase.php';
require_once '../../Model/product/model-product.cart.php';
require_once '../../Model/admin/model-amin.customer.php';

if (!isset($_SESSION['email'])) {
    header('Location: ./login.view.php');
} else {
    $CartClass = new Cart;
    $CustomerClass = new Customer;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $KH_IDKhachHang = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $KH_TenKhachHang = $CustomerClass->selectCustomerByEmail()['KH_TenKhachHang'];
    $KH_SDTKhachHang = $CustomerClass->selectCustomerByEmail()['KH_SDTKhachHang'];
    $KH_DiaChiKhachHang = $CustomerClass->selectCustomerByEmail()['KH_DiaChiKhachHang'];
    $KH_LoaiKhachHang = $CustomerClass->selectCustomerByEmail()['KH_LoaiKhachHang'];
    $KH_EmailKhachHang = $CustomerClass->selectCustomerByEmail()['KH_EmailKhachHang'];
    $KH_AvatarKhachHang = $CustomerClass->selectCustomerByEmail()['KH_AvatarKhachHang'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./cart.view.css">
    <link rel="stylesheet" href="../include/header.main.css">
    <link rel="stylesheet" href="../include/footer.main.css">

    <!-- <link rel="stylesheet" href="../include/all.min.css" />
    <script src="../include/all.min.js"></script>
    <link href='../include/boxicons.min.css' rel='stylesheet'>
    <script src="../include/jquery.min.js"></script>
     -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Cart Page</title>
</head>

<body>
    <?php include '../include/header.main.php'; ?>
    <div class="main">
        <div class="content">
            <div class="content-first">
                <div class="content-order">
                    <div class="order-tittle">
                        <h4>Giỏ Hàng (<?= $CartClass->countProductCart() ?>) </h4>

                        <div class="createBillBox"></div>
                    </div>
                    <div class="order-details"></div>

                </div>
                <div class="content-summary">
                    <div class="summary-tittle">
                        <h4>tóm tắt thanh toán</h4>
                    </div>
                    <div class="summary-details">
                        <div class="summary-discount">
                            <div class="check-login">
                                <span>Xin Chào, <?= $KH_TenKhachHang ?></span>
                            </div>
                            <div class="summary-code">
                                <span class="code-tittle">Mã đơn hàng</span>
                                <span class="code-details">Chưa có</span>
                            </div>
                        </div>
                        <div class="summary-border"></div>
                        <div class="summary-price">
                            <div class="summary-price-items">
                                <span class="summary-price-tittle">tiền đơn hàng</span>
                                <span class="summary-price-details" id="priceBill">0 VND</span>
                            </div>
                            <!-- <div class="summary-price-items">
                                <span class="summary-price-tittle">giảm giá</span>
                                <span class="summary-price-details" style="color: #ff3838" id="priceSale">0 VND</span>
                            </div> -->
                            <div class="summary-price-items">
                                <span class="summary-price-tittle">số mặt hàng</span>
                                <span class="summary-price-sale" style="color: #7d5fff" id="numberItem">0</span>
                            </div>
                            <div class="summary-price-items">
                                <span class="summary-price-tittle">số sản phẩm</span>
                                <span class="summary-price-service" style="color: #7d5fff" id="numberProduct">0</span>
                            </div>
                            <div class="summary-price-items">
                                <span class="summary-price-tittle">Thuế VAT, GTGT...</span>
                                <span class="summary-price-service" style="color:rgb(255, 0, 0)">0%</span>
                            </div>
                            <div class="summary-price-items">
                                <span class="summary-price-tittle">tổng cộng</span>
                                <span class="summary-price-details" style="color:rgb(0, 25, 250)" id="totalPriceBill">0 VND</span>
                            </div>
                        </div>
                        <div class="summary-border"></div>
                        <div class="summary-more">
                            <!-- <div class="order-border order-total">Địa Chỉ giao hàng</div> -->
                            <!-- <p>Địa chỉ mặc định: <?php echo  $KH_DiaChiKhachHang; ?></p> -->
                            <label style="padding-top:5px;">Nhập địa chỉ khác (nếu muốn giao tới địa chỉ mới):</label>
                            <input style="width: 100%;border-radius: 3px; border: 1px solid black; padding: 5px" type="text" id="diachi_tam" name="diachi_tam" placeholder="Để trống nếu muốn giao địa chỉ mặc định">
                            <!-- <div class="summary-price-items order-total">
                                <span class="summary-price-tittle">Tổng số sản phẩm: </span>

                                <span class="summary-price-details" id="totalQuantity">0</span>
                            </div>

                            <div class="summary-price-items order-total">
                                <span class="summary-price-tittle">Tổng tiền: </span>

                                <span class="summary-price-details" id="totalPrice">0 VND</span>
                            </div> -->

                        </div>
                        <div class="summary-save"></div>

                    </div>
                </div>

            </div>
            <div class="content-last" id="fetchCheckout"></div>
        </div>

    </div>

    <!-- LOADING -->
    <div class="loading__bg"></div>
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
            <div class="alert__notify__box__success__right__content"></div>
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
    <?php include '../include/footer.main.php' ?>
    <script src="./cart.view.js"></script>
    <script>
        $(document).ready(function() {
            // Xử lý sự kiện khi nhấn nút "Cập Nhật"
            $(document).on('click', '#updateInfoCartSubmit', function() {
                var idCustomer = $(this).attr('value');
                var newAddress = $('#updateAddressCart').val();
                var newPhone = $('#updatePhoneCart').val();

                console.log("idCustomer:", idCustomer);
                console.log("newAddress:", newAddress);
                console.log("newPhone:", newPhone);

                // Kiểm tra địa chỉ không được để trống hoặc là "Chưa cập nhật"
                if (newAddress === '' || newAddress.toLowerCase() === 'chưa cập nhật') {
                    alert('Địa chỉ không hợp lệ! Vui lòng nhập địa chỉ mới.');
                    return;
                }

                if (!/^\d+$/.test(newPhone)) {
                    alert('Số điện thoại không hợp lệ! Vui lòng nhập số.');
                    return; // Dừng không gửi AJAX request
                }
                // Gửi AJAX request để cập nhật thông tin
                $.ajax({
                    url: '../../Controller/product/controller-product.product.php',
                    type: 'POST',
                    data: {
                        updateInfoCartSubmit: 'update-info-cart',
                        idCustomer: idCustomer,
                        newAddress: newAddress,
                        newPhone: newPhone
                    },
                    success: function(response) {
                        console.log(response); // Thêm dòng này để xem phản hồi từ server
                        response = response.trim();
                        if (response === 'success') {
                            // Cập nhật thành công, có thể reload lại trang hoặc ẩn form
                            alert('Cập nhật thông tin thành công!');
                            // location.reload(); 
                            $('.content-checkout').hide();
                        } else if (response === 'failed') {
                            // Cập nhật thất bại
                            alert('Cập nhật thông tin thất bại!');
                        } else if (response === 'missing_data') {
                            alert('Vui lòng nhập đầy đủ thông tin!');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Có lỗi xảy ra!');
                    }
                });
            });
        });
    </script>
</body>

</html>

<script src="../../Controller/class/controller.function.js"></script>
<script src="../../Controller/class/controller.validate.js"></script>
<script src="../../Controller/product/controller-product.cart.js"></script>