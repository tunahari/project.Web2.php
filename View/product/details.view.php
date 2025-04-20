<?php 
@session_start(); 
if (!isset($_GET['id-product']) || empty($_GET['id-product'])) {
    ///header('location: ./product.view.php');
} 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="../include/header.main.css">
    <link rel="stylesheet" href="../include/footer.main.css">
    <link rel="stylesheet" href="./details.view.css">
    <title>Details Page</title>
</head>

<body>
    <?php include '../include/header.main.php' ?>
    <?php
        require_once '../../Model/admin/model-admin.info-product.php';
        require_once '../../Model/database/connectDataBase.php';
            $ProductClass = new Product;
            $ProductClass->setSP_IDSanPham($_GET['id-product']);
            $SP_IDSanPham = $ProductClass->selectProductByID ()['SP_IDSanPham'];
            $SP_TenSanPham = $ProductClass->selectProductByID ()['SP_TenSanPham'];
            $SP_GiaBanSanPham = $ProductClass->selectProductByID ()['SP_GiaBanSanPham'];
            $SP_GiaNhapSanPham = $ProductClass->selectProductByID ()['SP_GiaNhapSanPham'];
            $SP_GiamGiaSanPham = $ProductClass->selectProductByID ()['SP_GiamGiaSanPham'];
            $SP_ThoiGianTaoSanPham = $ProductClass->selectProductByID ()['SP_ThoiGianTaoSanPham'];

            $SP_TongNhapSanPham = $ProductClass->selectProductByID ()['SP_TongNhapSanPham'];
            $SP_TongBanSanPham = $ProductClass->selectProductByID ()['SP_TongBanSanPham'];
            $SP_BaoHanhSanPham = $ProductClass->selectProductByID ()['SP_BaoHanhSanPham'];
            $SP_ThoiHanBaoHanhSanPham = $ProductClass->selectProductByID ()['SP_ThoiHanBaoHanhSanPham'];
            $SP_CongNgheManHinhSanPham = $ProductClass->selectProductByID ()['SP_CongNgheManHinhSanPham'];
            $SP_DoPhanGiaiSanPham = $ProductClass->selectProductByID ()['SP_DoPhanGiaiSanPham'];
            $SP_KichThuocManHinhSanPham = $ProductClass->selectProductByID ()['SP_KichThuocManHinhSanPham'];
            $SP_DoSangToiDaSanPham = $ProductClass->selectProductByID ()['SP_DoSangToiDaSanPham'];
            $SP_MatKinhCamUngManHinhSanPham = $ProductClass->selectProductByID ()['SP_MatKinhCamUngManHinhSanPham'];
            $SP_DoPhanGiaiCMRSauSanPham = $ProductClass->selectProductByID ()['SP_DoPhanGiaiCMRSauSanPham'];
            $SP_DenFlashSanPham = $ProductClass->selectProductByID ()['SP_DenFlashSanPham'];
            $SP_MatKinhCamUngCMRSauSanPham = $ProductClass->selectProductByID ()['SP_MatKinhCamUngCMRSauSanPham'];
            $SP_DoPhanGiaiCMRTruocSanPham = $ProductClass->selectProductByID ()['SP_DoPhanGiaiCMRTruocSanPham'];
            $SP_HeDieuHanhSanPham = $ProductClass->selectProductByID ()['SP_HeDieuHanhSanPham'];
            $SP_CPUSanPham = $ProductClass->selectProductByID ()['SP_CPUSanPham'];
            $SP_TocDoCPUSanPham = $ProductClass->selectProductByID ()['SP_TocDoCPUSanPham'];
            $SP_GPUSanPham = $ProductClass->selectProductByID ()['SP_GPUSanPham'];
            intval($ProductClass->selectProductByID ()['SP_RAMSanPham']) > 0 ? 
            $SP_RAMSanPham = $ProductClass->selectProductByID ()['SP_RAMSanPham'] : $SP_RAMSanPham = 'Chưa cập nhật';
            intval($ProductClass->selectProductByID ()['SP_ROMSanPham']) > 0 ? 
            $SP_ROMSanPham = $ProductClass->selectProductByID ()['SP_ROMSanPham'] : $SP_ROMSanPham = 'Chưa cập nhật';
            intval($ProductClass->selectProductByID ()['SP_BoNhoKhaDungSanPham']) > 0 ? 
            $SP_BoNhoKhaDungSanPham = $ProductClass->selectProductByID ()['SP_BoNhoKhaDungSanPham'] : $SP_BoNhoKhaDungSanPham = 'Chưa cập nhật';              
            $SP_DanhBaSanPham = $ProductClass->selectProductByID ()['SP_DanhBaSanPham'];
            $SP_MangDiDongSanPham = $ProductClass->selectProductByID ()['SP_MangDiDongSanPham'];
            $SP_SIMSanPham = $ProductClass->selectProductByID ()['SP_SIMSanPham'];
            $SP_WifiSanPham = $ProductClass->selectProductByID ()['SP_WifiSanPham'];
            $SP_GPSSanPham = $ProductClass->selectProductByID ()['SP_GPSSanPham'];
            $SP_BluetoothSanPham = $ProductClass->selectProductByID ()['SP_BluetoothSanPham'];
            $SP_CongKetNoiSanPham = $ProductClass->selectProductByID ()['SP_CongKetNoiSanPham'];
            $SP_JackTaiNgheSanPham = $ProductClass->selectProductByID ()['SP_JackTaiNgheSanPham'];
            $SP_KetNoiKhacSanPham = $ProductClass->selectProductByID ()['SP_KetNoiKhacSanPham'];
            $SP_DungLuongPinSanPham = $ProductClass->selectProductByID ()['SP_DungLuongPinSanPham'];
            $SP_LoaiPinSanPham = $ProductClass->selectProductByID ()['SP_LoaiPinSanPham'];
            $SP_HoTroSacToiDaSanPham = $ProductClass->selectProductByID ()['SP_HoTroSacToiDaSanPham'];
            $SP_CongNghePinSanPham = $ProductClass->selectProductByID ()['SP_CongNghePinSanPham'];
            $SP_BaoMatNangCaoSanPham = $ProductClass->selectProductByID ()['SP_BaoMatNangCaoSanPham'];
            $SP_TinhNangDacBietSanPham = $ProductClass->selectProductByID ()['SP_TinhNangDacBietSanPham'];
            $SP_KhangNuocBuiSanPham = $ProductClass->selectProductByID ()['SP_KhangNuocBuiSanPham'];
            $SP_XemPhimSanPham = $ProductClass->selectProductByID ()['SP_XemPhimSanPham'];
            $SP_GhiAmSanPham = $ProductClass->selectProductByID ()['SP_GhiAmSanPham'];
            $SP_NgheNhacSanPham = $ProductClass->selectProductByID ()['SP_NgheNhacSanPham'];
            $SP_ThietKeSanPham = $ProductClass->selectProductByID ()['SP_ThietKeSanPham'];
            $SP_ChatLieuSanPham = $ProductClass->selectProductByID ()['SP_ChatLieuSanPham'];
            $SP_KichThuocSanPham = $ProductClass->selectProductByID ()['SP_KichThuocSanPham'];
            $SP_KhoiLuongSanPham = $ProductClass->selectProductByID ()['SP_KhoiLuongSanPham'];
            $SP_ThoiDiemRaMatSanPham = $ProductClass->selectProductByID ()['SP_ThoiDiemRaMatSanPham'];
            $SP_MaChiNhanhSanPham = $ProductClass->selectProductByID ()['SP_MaChiNhanhSanPham'];
            $SP_Image1SanPham = $ProductClass->selectProductByID ()['SP_Image1SanPham'];
            $SP_Image2SanPham = $ProductClass->selectProductByID ()['SP_Image2SanPham'];
            $SP_Image3SanPham = $ProductClass->selectProductByID ()['SP_Image3SanPham'];
            $SP_HangSanPham = $ProductClass->selectProductByID ()['SP_HangSanPham'];
    ?>
    <div class="main">
        <div class="quickview">
            <div class="quickview-close">
                <i class='bx bx-x'></i>
                <span>Đóng</span>
            </div>
            <div class="quickview-blur"></div>

        </div>
        <div class="content">
            <div class="content-tittle">
                <a href="./product.view.php">
                    <div class="content-tittle-icon">
                        <i class='bx bxs-home'></i>
                    </div>
                </a>
                <span>/</span>
                <h4><?php echo $SP_TenSanPham ?></h4>
            </div>
            <div class="content-details">
                <div class="content-details-top">
                    <div class="details-top-left">
                        <div class="small-img-group">
                            <div class="details-small-img  active" id="small-img-1">
                                <img src="../../Controller/admin/<?php echo $SP_Image1SanPham ?>" alt="" class="small-img" srcset="">
                            </div>
                            <div class="details-small-img" id="small-img-2">
                                <img src="../../Controller/admin/<?php echo $SP_Image2SanPham ?>" alt="" class="small-img" srcset="">
                            </div>
                            <div class="details-small-img" id="small-img-3">
                                <img src="../../Controller/admin/<?php echo $SP_Image3SanPham ?>" alt="" class="small-img" srcset="">
                            </div>
                        </div>
                        <div class="details-main-img">
                            <img src="../../Controller/admin/<?php echo $SP_Image1SanPham ?>" id="main-img" alt="">
                        </div>
                    </div>
                    <div class="details-top-right">
                        <div class="top-right-tittle">
                            <h4><?php echo $SP_TenSanPham ?></h4>
                        </div>
                        <div class="top-right-review">
                            <div class="review-icon">
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                            </div>
                            <div class="review-button">
                                <i class='bx bxs-conversation'></i>
                                <span> 1 bình luận </span>
                            </div>
                            <span class="right-review-span">| </span>
                            <div class="review-button">
                                <i class='bx bxs-edit-alt'></i>
                                <span>Viết bình luận </span>
                            </div>
                        </div>
                        <div class="top-right-description">
                            <div class="right-description-items">
                                <span class="description-items-tittle">Hãng: </span>
                                <span class="description-items-details"><?php echo $SP_HangSanPham ?></span>
                            </div>
                            <div class="right-description-items">
                                <span class="description-items-tittle">Tình trạng:</span>
                                <span class="description-items-details">
                               New 100%
                                </span>
                            </div>
                            <div class="right-description-items">
                                <span class="description-items-tittle">RAM:</span>
                                <span class="description-items-details"><?php echo $SP_RAMSanPham ?> GB</span>
                            </div>
                            <div class="right-description-items">
                                <span class="description-items-tittle">ROM:</span>
                                <span class="description-items-details"><?php echo $SP_ROMSanPham ?> GB</span>
                            </div>
                        </div>
                        <hr>
                        <div class="top-right-price">
                            <?php
                            $oldPrice = intval($SP_GiaBanSanPham);
                            $newPrice = intval($SP_GiaBanSanPham);
                            $salePrice = intval($SP_GiamGiaSanPham);
                            if ($salePrice === 0) {
                                $htmlSalePrice = '';
                            } else {
                                $newPrice = $oldPrice - ($newPrice * $salePrice) / 100;
                                $htmlSalePrice =  '<span class="price-bfsale">'.number_format($oldPrice).'</span>';
                            }
                            ?>
                            <span class="price-afsale"><?php echo number_format($oldPrice) ?></span>
                            <!-- <?php echo $htmlSalePrice ?> -->
                        </div>
                        <div class="top-right-form">
                            <div class="form-quality">
                                <h6>SL</h6>
                                <div class="form-qualtity-details">
                                    <input type="number" value="1" id="quantityCart">
                                    <div class="form-quantity-btn">
                                        <button class="quantity-btn-up" id="increaseQuantity">
                                            <i class='bx bxs-chevron-up'></i>
                                        </button>
                                        <button class="quantity-btn-down" id="decreaseQuantity">
                                            <i class='bx bxs-chevron-down'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            require_once '../../Model/database/connectDataBase.php';
                            require_once '../../Model/product/model-product.cart.php';
                            require_once '../../Model/admin/model-amin.customer.php';
                            $CartClass = new Cart;
                            $CustomerClass = new Customer;                                            
                            if (isset($_SESSION['email'])) {
                                $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
                                $customerID = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
                                echo '
                                <button class="form-cart" id="addCart" data-productID="'.$SP_IDSanPham.'"
                                data-customerID="'.$customerID.'">
                                    <i class="bx bx-cart"></i>
                                    <span>Thêm Vào Giỏ</span>
                                </button>
                                <div class="form-button" id="addWishlist">
                                    <i class="bx bxs-heart"></i>
                                    <span>Danh Sách Yêu Thích</span>
                                </div>
                                ';
                            } else {
                                echo '
                                <a href="./login.view.php" class="form-cart">
                                    <i class="bx bx-log-in"></i>
                                    <span>Đăng Nhập Để Mua Hàng</span>
                                </a>
                                ';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="content-details-bottom">
                    <div class="content-bottom-tittle">
                        <span class="bottom-tittle-items bottom-tittle-desc">Chi Tiết Sản Phẩm</span>
                       
                    </div>
                    <div class="content-bottom-desciption">
                        <div class="bottom-description">
                            <div style="display: none;" class="content-bottom-desciption-open">Chi tiết</div>
                            <div class="bottom-description--items">
                                <strong>Revolutionary multi-touch interface.</strong>
                                <p>iPod touch features the same multi-touch screen technology as iPhone. Pinch to zoom
                                    in on a photo. Scroll through your songs and videos with a flick. Flip through your
                                    library by album artwork with Cover Flow.</p>
                            </div>
                            <div class="bottom-description--items">
                                <strong>Gorgeous 3.5-inch widescreen display.</strong>
                                <p> Watch your movies, TV shows, and photos come alive with bright, vivid color on the
                                    320-by-480-pixel display.</p>
                            </div>
                            <div class="bottom-description--items">
                                <strong>Music downloads straight from iTunes.</strong>
                                <p>Shop the iTunes Wi-Fi Music Store from anywhere with Wi-Fi.1 Browse or search to find
                                    the music youre looking for, preview it, and buy it with just a tap.</p>
                            </div>
                            <div class="bottom-description--items">
                                <strong>Surf the web with Wi-Fi.</strong>
                                <p>Browse the web using Safari and watch YouTube videos on the first iPod with Wi-Fi
                                    built in</p>
                            </div>
                        </div>
                        <hr style="width: 100%; margin: 15px 0px;">
   
                        <span class="bottom-tittle-items bottom-tittle-review">Bình Luận(1)</span>
                        <div class="bottom-reviews">
                            <div class="customer-review">
                                <div class="customer-review-tittle">
                                    <span class="customer-name">Hoàng Tuấn Hải</span>
                                    <span class="review-date">12:30 15/07/2022</span>
                                </div>
                                <div class="review-content">
                                    <p>The 30-inch Apple Cinema HD Display delivers an amazing 2560 x 1600 pixel
                                        resolution. Designed specifically for the creative professional, this display
                                        provides more space for easier access to all the tools and palettes needed to
                                        edit, format and composite your work. Combine this display with a Mac Pro,
                                        MacBook Pro, or PowerMac G5 and there's no limit to what you can achieve.
                                    </p>
                                </div>
                                <div class="review-rate">
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                    <i class='bx bxs-star'></i>
                                </div>
                            </div>
                            <form class="review-form">
                                
                                <h5>Viết Bình Luận</h5>
                                <div class="form-review-content">
                                    <textarea type="text" id="input-content"> </textarea>
                                </div>
                                <div class="form-rating">
                                    <span class="rating-form-items">Đánh Giá</span>
                                    <div class="reviews-items-star">
                                        <div class="reviews-star-box">
                                            <i class='bx bxs-star'></i>
                                        </div>
                                        <div class="reviews-star-box">
                                            <i class='bx bxs-star'></i>
                                        </div>
                                        <div class="reviews-star-box">
                                            <i class='bx bxs-star'></i>
                                        </div>
                                        <div class="reviews-star-box">
                                            <i class='bx bxs-star'></i>
                                        </div>
                                        <div class="reviews-star-box">
                                            <i class='bx bxs-star'></i>
                                        </div>
                                    </div>
                                    <span class="rating-form-items">Rất Tốt</span>
                                </div>
                                <button type="button" class="submit-form">Đăng lên</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
    <script>
        // details.view.js

$(document).ready(function() {
    // Lấy tham chiếu đến ảnh chính và các ảnh nhỏ
    const mainImg = $('#main-img');
    const smallImgs = $('.details-small-img');

    // Xử lý sự kiện click cho từng ảnh nhỏ
    smallImgs.click(function() {
        // Lấy đường dẫn ảnh (src) của ảnh nhỏ được click
        const smallImgSrc = $(this).find('img').attr('src');

        // Thay đổi đường dẫn ảnh (src) của ảnh chính
        mainImg.attr('src', smallImgSrc);

        // Xóa class 'active' khỏi tất cả các ảnh nhỏ
        smallImgs.removeClass('active');

        // Thêm class 'active' vào ảnh nhỏ được click
        $(this).addClass('active');
    });
});

    </script>
</body>

</html>
<!-- <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script> -->
<script src="./details.view.js"></script>
<script src="../../Controller/class/controller.function.js"></script>
<script src="../../Controller/class/controller.validate.js"></script>
<script src="../../Controller/product/controller-product.product-details.js"></script>