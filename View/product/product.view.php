<?php
@session_start(); // Bắt đầu session nếu chưa có
require_once '../../Model/utils.php'; // Đường dẫn đến file utils.php
require_once '../../Model/database/connectDataBase.php'; // File kết nối database của bạn
$conn = mysqli_connect('localhost','root','','projectweb2'); // Kết nối database
// ... phần code còn lại của product.view.php ...
checkAccountStatusAndRedirect($conn); // Gọi hàm kiểm tra trạng thái

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
    <link rel="stylesheet" href="../include/header.main.css">
    <link rel="stylesheet" href="../include/footer.main.css">
    <link rel="stylesheet" href="./product.view.css">
    <title>Product Page</title>
</head>

<style>
    /* Highlight selected filter items */
    .selected-filter {
        background-color: #7860f6;
        color: white;
    }

    /* Make filter items clickable */
    .Product__Page__Content__Left__Filter__Cate__Item,
    .Product__Page__Content__Left__Filter__Branch__Item,
    .Product__Page__Content__Left__Filter__Ram__Item,
    .Product__Page__Content__Left__Filter__Rom__Item,
    .Product__Page__Content__Left__Filter__Pin__Item,
    .Product__Page__Content__Left__Filter__Camera__Item {
        cursor: pointer;
        padding: 5px 10px;
        margin: 3px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .Product__Page__Content__Left__Filter__Cate__Item:hover,
    .Product__Page__Content__Left__Filter__Branch__Item:hover,
    .Product__Page__Content__Left__Filter__Ram__Item:hover,
    .Product__Page__Content__Left__Filter__Rom__Item:hover,
    .Product__Page__Content__Left__Filter__Pin__Item:hover,
    .Product__Page__Content__Left__Filter__Camera__Item:hover {
        background-color: #e0e0e0;
    }

    /* Style for filter buttons */
    .Product__Page__Content__Left__Filter__Apply,
    .Product__Page__Content__Left__Filter__Reset {
        padding: 8px 15px;
        margin: 5px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .Product__Page__Content__Left__Filter__Apply {
        background-color: #7860f6;
        color: white;
    }

    .Product__Page__Content__Left__Filter__Reset {
        background-color: #f2f2f2;
        color: #333;
    }

    .Product__Page__Content__Left__Filter__Apply:hover {
        background-color: #6040d6;
    }

    .Product__Page__Content__Left__Filter__Reset:hover {
        background-color: #e0e0e0;
    }
</style>

<body>
    <div class="BackGround__Body"></div>
    <div class="Product__Page__Container">
        <?php include '../include/header.main.php';
        require '../../Model/admin/model-admin.info-product.php';
        $ProductClass = new Product;
        ?>
        <div class="Product__Page__Content">
            <div class="Product__Page__Content__Left">
                <div class="Product__Page__Content__Left__SaleOff__Img">
                    <div class="Product__Page__Content__Left__SaleOff__Img__Info">
                        <h1>Samsung Galaxy Note 20 Ultra</h1>
                        <p>Sale Off 30%</p>
                    </div>
                    <img src="../image/ddddd.png" alt="">
                </div>
                <div class="Product__Page__Content__Left__Filter">
                    <div class="Product__Page__Content__Left__Close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>

                    <!-------------------------- lọc sãn phẩm nâng cao -->


                    <div class="Product__Page__Content__Left__Title">LỌC SẢN PHẨM</div>
                    <div class="Product__Page__Content__Left__Filter__Cate">
                        <!-- lọc theo hãng -->
                        <div class="Product__Page__Filter__Title">
                            HÃNG
                        </div>
                        <div class="Product__Page__Content__Left__Filter__Cate__Item__Box">
                            <?php
                            $ListCate = $ProductClass->selectProductDistinct(" SELECT DISTINCT SP_HangSanPham FROM product WHERE SP_XoaSanPham = 'No' ");
                            for ($i = 0; $i < count($ListCate); $i++) {
                                $CateSanPham = $ListCate[$i]['SP_HangSanPham'];
                                if ($CateSanPham !== 'Chưa cập nhật') {
                                    echo '<div class="Product__Page__Content__Left__Filter__Cate__Item" value="' . $CateSanPham . '">' . $CateSanPham . '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <!-- lọc theo chi nhanh -->
                    <div class="Product__Page__Content__Left__Filter__Branch">
                        <div class="Product__Page__Filter__Title">
                            CHI NHÁNH
                        </div>
                        <div class="Product__Page__Content__Left__Filter__Branch__Item__Box">
                            <?php
                            require_once '../../Model/admin/model-admin.branch.php';
                            $BranchClass = new Branch;
                            for ($i = 0; $i < count($BranchClass->selectAllBranch()); $i++) {
                                $MaChiNhanh = $BranchClass->selectAllBranch()[$i]['CN_IDChiNhanh'];
                                $TenChiNhanh = $BranchClass->selectAllBranch()[$i]['CN_TenChiNhanh'];
                                if ($TenChiNhanh !== 'Chưa cập nhật') {
                                    echo '<div class="Product__Page__Content__Left__Filter__Branch__Item" value="' . $MaChiNhanh . '">' . $TenChiNhanh . '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <!-- lọc theo giá -->

                    <div class="">

                        <div class="Product__Page__Filter__Title">
                            GIÁ
                        </div>

                        <div class="Product__Page__Content__Left__Filter__Price__Item__Box">
                            <input style="border: 1px solid #7860f6; border-radius: 4px; padding: 5px;margin-right: 5px;" type="number" name="minPrice" id="minPrice" value="<?= isset($_GET['minPrice']) ? $_GET['minPrice'] : ''; ?>" placeholder="Từ" />

                            <input style="border: 1px solid #7860f6; border-radius: 4px; padding: 5px;margin-right: 5px;" type="number" name="maxPrice" id="maxPrice" value="<?= isset($_GET['maxPrice']) ? $_GET['maxPrice'] : ''; ?>" placeholder="Đến" />
                        </div>
                    </div>

                    <!-- lọc theo RAM -->
                    <div class="Product__Page__Content__Left__Filter__Ram">
                        <div class="Product__Page__Filter__Title">
                            RAM
                        </div>
                        <div class="Product__Page__Content__Left__Filter__Ram__Item__Box">
                            <?php
                            $ListRam = $ProductClass->selectProductDistinct(" SELECT DISTINCT SP_RAMSanPham FROM product WHERE SP_XoaSanPham = 'No' ORDER BY CONVERT(SP_RAMSanPham, INT) ");
                            for ($i = 0; $i < count($ListRam); $i++) {
                                $RamSanPham = $ListRam[$i]['SP_RAMSanPham'];
                                if (intval($RamSanPham) !== -1) {
                                    echo '<div class="Product__Page__Content__Left__Filter__Ram__Item" value="' . $RamSanPham . '">' . $RamSanPham . ' GB</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- loc theo ROM -->
                    <div class="Product__Page__Content__Left__Filter__Rom">
                        <div class="Product__Page__Filter__Title">
                            ROM
                        </div>
                        <div class="Product__Page__Content__Left__Filter__Rom__Item__Box">
                            <?php
                            $ListRom = $ProductClass->selectProductDistinct(" SELECT DISTINCT SP_ROMSanPham FROM product WHERE SP_XoaSanPham = 'No' ORDER BY CONVERT(SP_ROMSanPham, INT) ");
                            for ($i = 0; $i < count($ListRom); $i++) {
                                $RomSanPham = $ListRom[$i]['SP_ROMSanPham'];
                                if (intval($RomSanPham) !== -1) {
                                    echo '<div class="Product__Page__Content__Left__Filter__Rom__Item" value="' . $RomSanPham . '">' . $RomSanPham . ' GB</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <!-- loc theo pin -->
                    <div class="Product__Page__Content__Left__Filter__Pin">
                        <div class="Product__Page__Filter__Title">
                            PIN
                        </div>
                        <div class="Product__Page__Content__Left__Filter__Pin__Item__Box">
                            <?php
                            $ListPin = $ProductClass->selectProductDistinct(" SELECT DISTINCT SP_CongNghePinSanPham FROM product WHERE SP_XoaSanPham = 'No' ");
                            for ($i = 0; $i < count($ListPin); $i++) {
                                $PinSanPham = $ListPin[$i]['SP_CongNghePinSanPham'];
                                if ($PinSanPham !== 'Chưa cập nhật') {
                                    echo '<div class="Product__Page__Content__Left__Filter__Pin__Item" value="' . $PinSanPham . '">' . $PinSanPham . '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <!-- lọc theo camera -->
                    <div class="Product__Page__Content__Left__Filter__Camera">
                        <div class="Product__Page__Filter__Title">
                            CAMERA
                        </div>
                        <div class="Product__Page__Content__Left__Filter__Camera__Item__Box">
                            <?php
                            $ListCamera = $ProductClass->selectProductDistinct(" SELECT DISTINCT SP_DoPhanGiaiSanPham FROM product WHERE SP_XoaSanPham = 'No' ");
                            for ($i = 0; $i < count($ListCamera); $i++) {
                                $CameraSanPham = $ListCamera[$i]['SP_DoPhanGiaiSanPham'];
                                if ($CameraSanPham !== 'Chưa cập nhật') {
                                    echo '<div class="Product__Page__Content__Left__Filter__Camera__Item" value="' . $CameraSanPham . '">' . $CameraSanPham . '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="Product__Page__Content__Left__Filter__Apply__Reset">
                        <button class="Product__Page__Content__Left__Filter__Reset">ĐẶT LẠI</button> <!--Thay <a> bằng <button>-->
                        <button class="Product__Page__Content__Left__Filter__Apply">LỌC</button>
                    </div>
                </div>
                <div class="Product__Page__Content__Left__Services">
                    <div class="Product__Page__Content__Left__Title">DỊCH VỤ KHÁCH HÀNG</div>
                    <div class="Product__Page__Content__Left__Services__Box">
                        <div class="Product__Page__Content__Left__Services__Item">
                            <div class="Product__Page__Content__Left__Services__Item__Icon">
                                <img src="../image/services1.png" alt="">
                            </div>
                            <div class="Product__Page__Content__Left__Services__Item__Info">
                                <div class="Product__Page__Content__Left__Services__Info__Title">
                                    MUA HÀNG BẢO MẬT
                                </div>
                                <div class="Product__Page__Content__Left__Services__Info__Text">
                                    Bảo mật thông tin khách mua hàng.
                                </div>
                            </div>
                        </div>
                        <div class="Product__Page__Content__Left__Services__Item">
                            <div class="Product__Page__Content__Left__Services__Item__Icon">
                                <img src="../image/services2.png" alt="">
                            </div>
                            <div class="Product__Page__Content__Left__Services__Item__Info">
                                <div class="Product__Page__Content__Left__Services__Info__Title">
                                    UY TÍN
                                </div>
                                <div class="Product__Page__Content__Left__Services__Info__Text">
                                    Bảo mật thanh toán, đổi trả dễ dàng.
                                </div>
                            </div>
                        </div>
                        <div class="Product__Page__Content__Left__Services__Item">
                            <div class="Product__Page__Content__Left__Services__Item__Icon">
                                <img src="../image/services3.png" alt="">
                            </div>
                            <div class="Product__Page__Content__Left__Services__Item__Info">
                                <div class="Product__Page__Content__Left__Services__Info__Title">
                                    TRỢ GIÚP 24/7
                                </div>
                                <div class="Product__Page__Content__Left__Services__Info__Text">
                                    Tư vấn, hỗ trợ nhiệt tình, kịp thời.
                                </div>
                            </div>
                        </div>
                        <div class="Product__Page__Content__Left__Services__Item">
                            <div class="Product__Page__Content__Left__Services__Item__Icon">
                                <img src="../image/services4.png" alt="">
                            </div>
                            <div class="Product__Page__Content__Left__Services__Item__Info">
                                <div class="Product__Page__Content__Left__Services__Info__Title">
                                    GIAO HÀNG
                                </div>
                                <div class="Product__Page__Content__Left__Services__Info__Text">
                                    Giao hàng tận nơi trên toàn quốc.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Product__Page__Content__Right">
                <div class="Product__Page__Content__Right__Slide">
                    <div class="swiper Product__Page__Content__Right__Swipper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="../image/slide.jpg" alt="">
                            </div>
                            <div class="swiper-slide">
                                <img src="../image/slide.jpg" alt="">
                            </div>
                            <div class="swiper-slide">
                                <img src="../image/slide.jpg" alt="">
                            </div>
                            <div class="swiper-slide">
                                <img src="../image/slide.jpg" alt="">
                            </div>
                            <div class="swiper-slide">
                                <img src="../image/slide.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-button-prev"><i class="fa-solid fa-arrow-left"></i></div>
                        <div class="swiper-button-next"><i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="Product__Page__Content__Right__Product">
                    <div class="Product__Page__Content__Right__Product__Tab">
                        <div class="Product__Page__Content__Right__Product__Title">SẢN PHẨM ĐANG BÁN</div>
                    </div>
                    <!-- ================================== PRODUCT BOX ================================== -->


                    <div class="Product__Page__Content__Right__Product__Panel">
                        <?php
                        // Kết nối CSDL
                        $conn = mysqli_connect('localhost', 'root', '', 'projectweb2') or die("Không thể kết nối CSDL");

                        // Số sản phẩm mỗi trang
                        $num_per_page = 9;

                        // Xác định trang hiện tại
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        if ($page < 1) $page = 1; // Đảm bảo không có giá trị âm
                        $start = ($page - 1) * $num_per_page;

                        // Xử lý các tham số lọc
                        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
                        $brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';
                        $branch = isset($_GET['branch']) ? trim($_GET['branch']) : '';
                        $minPrice = isset($_GET['minPrice']) ? (int)$_GET['minPrice'] : 0;
                        $maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 0;
                        $ram = isset($_GET['ram']) ? trim($_GET['ram']) : '';
                        $rom = isset($_GET['rom']) ? trim($_GET['rom']) : '';
                        $pin = isset($_GET['pin']) ? trim($_GET['pin']) : '';
                        $camera = isset($_GET['camera']) ? trim($_GET['camera']) : '';

                        // Xây dựng điều kiện WHERE
                        $conditions = array();

                        if (!empty($keyword)) {
                            $conditions[] = "SP_TenSanPham LIKE '%" . mysqli_real_escape_string($conn, $keyword) . "%'";
                        }

                        if (!empty($brand)) {
                            $conditions[] = "SP_HangSanPham = '" . mysqli_real_escape_string($conn, $brand) . "'";
                        }

                        if (!empty($branch)) {
                            $conditions[] = "SP_MaChiNhanhSanPham = '" . mysqli_real_escape_string($conn, $branch) . "'";
                        }

                        if ($minPrice > 0) {
                            $conditions[] = "SP_GiaNhapSanPham >= " . $minPrice;
                        }

                        if ($maxPrice > 0) {
                            $conditions[] = "SP_GiaNhapSanPham <= " . $maxPrice;
                        }

                        if (!empty($ram)) {
                            $conditions[] = "SP_RAMSanPham = '" . mysqli_real_escape_string($conn, $ram) . "'";
                        }

                        if (!empty($rom)) {
                            $conditions[] = "SP_ROMSanPham = '" . mysqli_real_escape_string($conn, $rom) . "'";
                        }

                        if (!empty($pin)) {
                            $conditions[] = "SP_CongNghePinSanPham = '" . mysqli_real_escape_string($conn, $pin) . "'";
                        }

                        if (!empty($camera)) {
                            $conditions[] = "SP_DoPhanGiaiSanPham = '" . mysqli_real_escape_string($conn, $camera) . "'";
                        }

                        // Điều kiện mặc định: chỉ hiển thị sản phẩm chưa bị xóa
                        $conditions[] = "SP_XoaSanPham = 'No'";

                        // Tạo câu truy vấn WHERE
                        $where_clause = "";
                        if (count($conditions) > 0) {
                            $where_clause = "WHERE " . implode(" AND ", $conditions);
                        }

                        // Lấy tổng số bản ghi thỏa mãn điều kiện
                        $total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `product` $where_clause");
                        $total_row = mysqli_fetch_assoc($total_result)['total'];
                        $num_page = ceil($total_row / $num_per_page);

                        // Truy vấn lấy sản phẩm theo trang và điều kiện lọc
                        $sql = "SELECT * FROM `product` $where_clause LIMIT $start, $num_per_page";
                        $result = mysqli_query($conn, $sql);

                        $products = [];
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_array()) {
                                $products[] = $row;
                            }
                        }

                        // Hàm tạo URL với các tham số lọc
                        function buildUrlWithParams($page = 1)
                        {
                            $params = array();
                            $params['page'] = $page;

                            if (!empty($_GET['keyword'])) {
                                $params['keyword'] = $_GET['keyword'];
                            }

                            if (!empty($_GET['brand'])) {
                                $params['brand'] = $_GET['brand'];
                            }

                            if (!empty($_GET['branch'])) {
                                $params['branch'] = $_GET['branch'];
                            }

                            if (!empty($_GET['minPrice'])) {
                                $params['minPrice'] = $_GET['minPrice'];
                            }

                            if (!empty($_GET['maxPrice'])) {
                                $params['maxPrice'] = $_GET['maxPrice'];
                            }

                            if (!empty($_GET['ram'])) {
                                $params['ram'] = $_GET['ram'];
                            }

                            if (!empty($_GET['rom'])) {
                                $params['rom'] = $_GET['rom'];
                            }

                            if (!empty($_GET['pin'])) {
                                $params['pin'] = $_GET['pin'];
                            }

                            if (!empty($_GET['camera'])) {
                                $params['camera'] = $_GET['camera'];
                            }

                            foreach ($_GET as $key => $value) {
                                if ($key !== 'page') {
                                    $params[$key] = $value;
                                }
                            }
                            return './product.view.php?' . http_build_query($params);
                        }

                        if (!empty($products)) {
                            foreach ($products as $product) {

                        ?>

                                <a href="./details.view.php?id-product=<?= $product['SP_IDSanPham']; ?>" class="Product__Page__Content__Right__Product__Panel__Item">
                                    <div class="Product__Page__Content__Right__Product__Panel__Item__Icon__Group">
                                        <div class="Product__Page__Content__Right__Product__Panel__Item__Icon">
                                            <i class="fa-solid fa-eye"></i>
                                        </div>
                                    </div>
                                    <div class="Product__Page__Content__Right__Product__Panel__Item__Image">
                                        <img class="Product__Panel__Item__Image__1" src="../../Controller/admin/<?= $product['SP_Image1SanPham']; ?>" alt="">
                                        <img class="Product__Panel__Item__Image__2" src="../../Controller/admin/<?= $product['SP_Image2SanPham']; ?>" alt="">
                                    </div>
                                    <div class="Product__Page__Content__Right__Product__Panel__Item__Name">
                                        <?= $product['SP_TenSanPham']; ?>
                                    </div>
                                    <div class="Product__Page__Content__Right__Product__Panel__Item__Star">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="Product__Page__Content__Right__Product__Panel__Item__Price">
                                        <div class="Product__Page__Content__Right__Product__Panel__Item__New__Price"><?= number_format($product['SP_GiaBanSanPham'], 0, ',', '.') ?></div>

                                    </div>
                                </a>


                        <?php
                            }
                        } else {
                            echo "<p>Không tìm thấy sản phẩm nào!</p>";
                        }
                        ?>
                    </div>


                    <!-- ================================== PRODUCT BOX ================================== -->



                    <!-- ================================== PHÂN TRANG ================================== -->
                    <?php
                    function get_paging($num_page, $page)
                    {
                        $str_paging = "<div class='Product__Page__Content__Right__Product__Paging'>";

                        // Nút "Trang trước"
                        if ($page > 1) {
                            $prev_page = $page - 1;
                            // Sử dụng buildUrlWithParams để tạo link
                            $prev_url = buildUrlWithParams($prev_page);
                            $str_paging .= "<a href='{$prev_url}' class='Product__Page__Content__Right__Product__Paging__Item Paging__Prev'><i class='fa-solid fa-arrow-left'></i></a>";
                        }

                        // Hiển thị số trang
                        for ($i = 1; $i <= $num_page; $i++) {
                            $active_class = ($i == $page) ? 'active' : '';
                            // Sử dụng buildUrlWithParams để tạo link
                            $page_url = buildUrlWithParams($i);
                            $str_paging .= "<a href='{$page_url}' class='Product__Page__Content__Right__Product__Paging__Item {$active_class}'>{$i}</a>";
                        }

                        // Nút "Trang tiếp"
                        if ($page < $num_page) {
                            $next_page = $page + 1;
                            // Sử dụng buildUrlWithParams để tạo link
                            $next_url = buildUrlWithParams($next_page);
                            $str_paging .= "<a href='{$next_url}' class='Product__Page__Content__Right__Product__Paging__Item Paging__Next'><i class='fa-solid fa-arrow-right'></i></a>";
                        }

                        $str_paging .= "</div>";
                        return $str_paging;
                    }


                    // Hiển thị phân trang
                    echo get_paging($num_page, $page);
                    ?>
                    <!-- <div class="Product__Page__Content__Right__Product__Paging">
                        <a class="Product__Page__Content__Right__Product__Paging__Item Paging__Prev"><i class="fa-solid fa-arrow-left"></i></a>
                        <a href="../product/product.view.php?page=1" class="Product__Page__Content__Right__Product__Paging__Item active">1</a>
                        <a class="Product__Page__Content__Right__Product__Paging__Item">2</a>
                        <a class="Product__Page__Content__Right__Product__Paging__Item">3</a>
                        <a class="Product__Page__Content__Right__Product__Paging__Item">4</a>
                        <a class="Product__Page__Content__Right__Product__Paging__Item">5</a>
                        <a class="Product__Page__Content__Right__Product__Paging__Item Paging__Dots">...</a>
                        <daiv class="Product__Page__Content__Right__Product__Paging__Item">100</daiv>
                        <a class="Product__Page__Content__Right__Product__Paging__Item Paging__Next"><i class="fa-solid fa-arrow-right"></i></a>
                    </div> -->
                </div>

            </div>
        </div>

        <div class="Product__Page__Content__2">
            <div class="Product__Page__Content__2__Title">
                SẢN PHẨM KHUYẾN MÃI
                <div class="SpecialTrend__Swipper__Navigation">
                    <div class="SpecialTrend__Swipper__Prev"><i class="fa-solid fa-arrow-left"></i></div>
                    <div class="SpecialTrend__Swipper__Next"><i class="fa-solid fa-arrow-right"></i></div>
                </div>
            </div>
            <div class="Product__Page__Content__2__SpecialTrend">
                <div class="Product__Page__Content__2__SpecialTrend__Image">
                    <img src="../image/Special_Trend_Banner-265x384.jpg" alt="">
                </div>
                <div class="Product__Page__Content__2__SpecialTrend__Slide">
                    <div class="swiper Product__Page__Content__2__SpecialTrend__Swipper">
                        <div class="swiper-wrapper">
                            <?php
                            $saleProducts = $ProductClass->selectSaleProducts();
                            for ($i = 0; $i < count($saleProducts); $i++) {
                                $oldPrice = intval($saleProducts[$i]['SP_GiaBanSanPham']);
                                $newPrice = intval($saleProducts[$i]['SP_GiaBanSanPham']);
                                $salePrice = intval($saleProducts[$i]['SP_GiamGiaSanPham']);
                                if ($salePrice === 0) {
                                    $htmlSalePrice = '';
                                } else {
                                    $newPrice = $oldPrice - ($newPrice * $salePrice) / 100;
                                    $htmlSalePrice =  '<div class="Product__Page__Content__2__SpecialTrend__Item__OldPrice">' . number_format($oldPrice) . '</div>';
                                }
                                echo '
                                    <a href="./details.view.php?id-product=' . $saleProducts[$i]['SP_IDSanPham'] . '" class="swiper-slide SpecialTrend__Swipper__Slide">
                                        <div class="Product__Page__Content__2__SpecialTrend__Item">
                                            <div class="Product__Page__Content__2__SpecialTrend__Item__Image">
                                                <img class="Product__SpecialTrend__Item__Image__1" src="../../Controller/admin/' . $saleProducts[$i]['SP_Image1SanPham'] . '" alt="">
                                                <img class="Product__SpecialTrend__Item__Image__2" src="../../Controller/admin/' . $saleProducts[$i]['SP_Image1SanPham'] . '" alt="">
                                            </div>
                                            <div class="Product__Page__Content__2__SpecialTrend__Item__Name">' . $saleProducts[$i]['SP_TenSanPham'] . '</div>
                                            <div class="Product__Page__Content__2__SpecialTrend__Item__Star">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="Product__Page__Content__2__SpecialTrend__Item__Price__Box">
                                                <div class="Product__Page__Content__2__SpecialTrend__Item__NewPrice">' . number_format($newPrice) . '</div>
                                                ' . $htmlSalePrice . '
                                            </div>
                                            <div class="Product__Page__Content__2__SpecialTrend__Item__Time">
                                                <div class="Product__Page__Content__2__SpecialTrend__Item__Time__Icon">
                                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                                </div>
                                                <div class="Product__Page__Content__2__SpecialTrend__Item__Time__Date">
                                                    -32:
                                                </div>
                                                <div class="Product__Page__Content__2__SpecialTrend__Item__Time__Hour">
                                                    -18:
                                                </div>
                                                <div class="Product__Page__Content__2__SpecialTrend__Item__Time__Minute">
                                                    -30:
                                                </div>
                                                <div class="Product__Page__Content__2__SpecialTrend__Item__Time__Second">
                                                    -54
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    ';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="Product__Page__Content__2__ShopNow">
                <?php
                $saleOff30Products = $ProductClass->selectSaleOff30Products();
                for ($i = 0; $i < count($saleOff30Products); $i++) {
                    echo '
                    <div class="Product__Page__Content__2__ShopNow__Item">
                        <div class="Product__Page__Content__2__ShopNow__Item__Left">
                            <div class="Product__Page__Content__2__ShopNow__Item__Title">' . $saleOff30Products[$i]['SP_TenSanPham'] . '</div>
                            <div class="Product__Page__Content__2__ShopNow__Item__Sale__Text">
                                Giảm Giá ' . $saleOff30Products[$i]['SP_GiamGiaSanPham'] . '%
                            </div>
                            <a href="./details.view.php?id-product=' . $saleOff30Products[$i]['SP_IDSanPham'] . '" class="Product__Page__Content__2__ShopNow__Item__Button">Chi Tiết</a>
                        </div>
                        <div class="Product__Page__Content__2__ShopNow__Item__Right">
                            <img src="../../Controller/admin/' . $saleOff30Products[$i]['SP_Image1SanPham'] . '" alt="">
                        </div>
                    </div>
                    ';
                }
                ?>
            </div>

            <div class="Product__Page__Content__2__Image__SaleOff">
                <div class="Product__Page__Content__2__Image__SaleOff__Left">
                    <img src="../image/aaaaa.png" alt="">
                </div>
                <div class="Product__Page__Content__2__Image__SaleOff__Right">
                    <div class="Product__Page__Content__2__Image__SaleOff__Title">
                        Best-selling Samsung
                    </div>
                    <div class="Product__Page__Content__2__Image__SaleOff__Line"></div>
                    <div class="Product__Page__Content__2__Image__SaleOff__Price">
                        From $12.990
                    </div>
                    <div class="Product__Page__Content__2__Image__SaleOff__UptoPrice">
                        Up to $30.000 Off*
                    </div>
                </div>
            </div>

            <div class="Product__Page__Content__2__Services">
                <div class="Product__Page__Content__2__Title">CUSTOMER SERVICES</div>
                <div class="Product__Page__Content__2__Services__Box">
                    <div class="Product__Page__Content__2__Services__Item">
                        <div class="Product__Page__Content__2__Services__Item__Icon">
                            <img src="../image/services1.png" alt="">
                        </div>
                        <div class="Product__Page__Content__2__Services__Item__Info">
                            <div class="Product__Page__Content__2__Services__Info__Title">
                                SECURE PAYMENT
                            </div>
                            <div class="Product__Page__Content__2__Services__Info__Text">
                                Moving Your Card Details to a much more secured place.
                            </div>
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Services__Item">
                        <div class="Product__Page__Content__2__Services__Item__Icon">
                            <img src="../image/services2.png" alt="">
                        </div>
                        <div class="Product__Page__Content__2__Services__Item__Info">
                            <div class="Product__Page__Content__2__Services__Info__Title">
                                TRUSTPAY
                            </div>
                            <div class="Product__Page__Content__2__Services__Info__Text">
                                100% Payment Protection. Easy Return policy.
                            </div>
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Services__Item">
                        <div class="Product__Page__Content__2__Services__Item__Icon">
                            <img src="../image/services3.png" alt="">
                        </div>
                        <div class="Product__Page__Content__2__Services__Item__Info">
                            <div class="Product__Page__Content__2__Services__Info__Title">
                                SUPPORT 24/7
                            </div>
                            <div class="Product__Page__Content__2__Services__Info__Text">
                                Got a question? Look no further.Browse our FAQs or Submit yor query here.
                            </div>
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Services__Item">
                        <div class="Product__Page__Content__2__Services__Item__Icon">
                            <img src="../image/services4.png" alt="">
                        </div>
                        <div class="Product__Page__Content__2__Services__Item__Info">
                            <div class="Product__Page__Content__2__Services__Info__Title">
                                SHOP ON THE GO
                            </div>
                            <div class="Product__Page__Content__2__Services__Info__Text">
                                Download the app and get exciting app only offers at your fingertips.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="Product__Page__Content__2__Image__Our__Cate">
                <div class="Product__Page__Content__2__Title">
                    CATEGORY
                </div>
                <div class="Product__Page__Content__2__Image__Our__Cate__Box">
                    <div class="Product__Page__Content__2__Image__Our__Cate__Item">
                        <div class="Product__Page__Content__2__Image__Our__Cate__Item__Icon">
                            <img src="../image/logo-samsung.png" alt="">
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Image__Our__Cate__Item">
                        <div class="Product__Page__Content__2__Image__Our__Cate__Item__Icon">
                            <img src="../image/logo-apple.png" alt="">
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Image__Our__Cate__Item">
                        <div class="Product__Page__Content__2__Image__Our__Cate__Item__Icon">
                            <img src="../image/logo-xiaomi.png" alt="">
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Image__Our__Cate__Item">
                        <div class="Product__Page__Content__2__Image__Our__Cate__Item__Icon">
                            <img src="../image/logo-oppo.png" alt="">
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Image__Our__Cate__Item">
                        <div class="Product__Page__Content__2__Image__Our__Cate__Item__Icon">
                            <img src="../image/logo-vivo.png" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="Product__Page__Content__2__Image__SaleOff__2">
                <div class="Product__Page__Content__2__Image__SaleOff__2__Left">
                    <div class="Product__Page__Content__2__Image__SaleOff__2__Left__1">
                        <div class="Product__Page__Content__2__Image__SaleOff__2__Left__Title">
                            Samsung Galaxy S21
                        </div>
                        <div class="Product__Page__Content__2__Image__SaleOff__2__Left__Text">
                            World's First 5G-upgradable
                        </div>
                        <div class="Product__Page__Content__2__Image__SaleOff__2__Left__ShowNow">
                            Show Now
                        </div>
                    </div>
                    <div class="Product__Page__Content__2__Image__SaleOff__2__Left__2">
                        <img src="../image/ccccc.png" alt="">
                    </div>
                </div>
                <div class="Product__Page__Content__2__Image__SaleOff__2__Right">
                    <div class="Product__Page__Content__2__Image__SaleOff__2__Right__Title__Text">
                        <div class="Product__Page__Content__2__Image__SaleOff__2__Right__Title">Subtitle</div>
                        <div class="Product__Page__Content__2__Image__SaleOff__2__Right__Text">Wireless. Effortless. Magical.</div>
                    </div>
                    <img src="../image/bbbbb.png" alt="">
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
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý sự kiện chọn các mục lọc
            const brandItems = document.querySelectorAll('.Product__Page__Content__Left__Filter__Cate__Item');
            const branchItems = document.querySelectorAll('.Product__Page__Content__Left__Filter__Branch__Item');
            const ramItems = document.querySelectorAll('.Product__Page__Content__Left__Filter__Ram__Item');
            const romItems = document.querySelectorAll('.Product__Page__Content__Left__Filter__Rom__Item');
            const pinItems = document.querySelectorAll('.Product__Page__Content__Left__Filter__Pin__Item');
            const cameraItems = document.querySelectorAll('.Product__Page__Content__Left__Filter__Camera__Item');

            // Lưu trữ các tùy chọn lọc
            let selectedFilters = {
                brand: '',
                branch: '',
                ram: '',
                rom: '',
                pin: '',
                camera: '',
                minPrice: '',
                maxPrice: ''
            };

            // Lấy giá trị từ URL nếu có
            const urlParams = new URLSearchParams(window.location.search);
            selectedFilters.brand = urlParams.get('brand') || '';
            selectedFilters.branch = urlParams.get('branch') || '';
            selectedFilters.ram = urlParams.get('ram') || '';
            selectedFilters.rom = urlParams.get('rom') || '';
            selectedFilters.pin = urlParams.get('pin') || '';
            selectedFilters.camera = urlParams.get('camera') || '';
            selectedFilters.minPrice = urlParams.get('minPrice') || '';
            selectedFilters.maxPrice = urlParams.get('maxPrice') || '';

            // Hiển thị các lựa chọn đã chọn
            function highlightSelected() {
                // Xóa tất cả các lựa chọn hiện tại
                document.querySelectorAll('.selected-filter').forEach(item => {
                    item.classList.remove('selected-filter');
                });

                // Đánh dấu các lựa chọn từ URL
                if (selectedFilters.brand) {
                    brandItems.forEach(item => {
                        if (item.getAttribute('value') === selectedFilters.brand) {
                            item.classList.add('selected-filter');
                        }
                    });
                }

                if (selectedFilters.branch) {
                    branchItems.forEach(item => {
                        if (item.getAttribute('value') === selectedFilters.branch) {
                            item.classList.add('selected-filter');
                        }
                    });
                }

                if (selectedFilters.ram) {
                    ramItems.forEach(item => {
                        if (item.getAttribute('value') === selectedFilters.ram) {
                            item.classList.add('selected-filter');
                        }
                    });
                }

                if (selectedFilters.rom) {
                    romItems.forEach(item => {
                        if (item.getAttribute('value') === selectedFilters.rom) {
                            item.classList.add('selected-filter');
                        }
                    });
                }

                if (selectedFilters.pin) {
                    pinItems.forEach(item => {
                        if (item.getAttribute('value') === selectedFilters.pin) {
                            item.classList.add('selected-filter');
                        }
                    });
                }

                if (selectedFilters.camera) {
                    cameraItems.forEach(item => {
                        if (item.getAttribute('value') === selectedFilters.camera) {
                            item.classList.add('selected-filter');
                        }
                    });
                }

                // Đặt giá trị cho ô nhập giá
                document.getElementById('minPrice').value = selectedFilters.minPrice;
                document.getElementById('maxPrice').value = selectedFilters.maxPrice;
            }

            // Gọi hàm để hiển thị các lựa chọn đã chọn khi tải trang
            highlightSelected();

            // Xử lý sự kiện click vào các mục lọc
            brandItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.getAttribute('value');

                    // Toggle selection
                    if (selectedFilters.brand === value) {
                        selectedFilters.brand = '';
                        this.classList.remove('selected-filter');
                    } else {
                        brandItems.forEach(i => i.classList.remove('selected-filter'));
                        selectedFilters.brand = value;
                        this.classList.add('selected-filter');
                    }
                });
            });

            branchItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.getAttribute('value');

                    if (selectedFilters.branch === value) {
                        selectedFilters.branch = '';
                        this.classList.remove('selected-filter');
                    } else {
                        branchItems.forEach(i => i.classList.remove('selected-filter'));
                        selectedFilters.branch = value;
                        this.classList.add('selected-filter');
                    }
                });
            });

            ramItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.getAttribute('value');

                    if (selectedFilters.ram === value) {
                        selectedFilters.ram = '';
                        this.classList.remove('selected-filter');
                    } else {
                        ramItems.forEach(i => i.classList.remove('selected-filter'));
                        selectedFilters.ram = value;
                        this.classList.add('selected-filter');
                    }
                });
            });

            romItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.getAttribute('value');

                    if (selectedFilters.rom === value) {
                        selectedFilters.rom = '';
                        this.classList.remove('selected-filter');
                    } else {
                        romItems.forEach(i => i.classList.remove('selected-filter'));
                        selectedFilters.rom = value;
                        this.classList.add('selected-filter');
                    }
                });
            });

            pinItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.getAttribute('value');

                    if (selectedFilters.pin === value) {
                        selectedFilters.pin = '';
                        this.classList.remove('selected-filter');
                    } else {
                        pinItems.forEach(i => i.classList.remove('selected-filter'));
                        selectedFilters.pin = value;
                        this.classList.add('selected-filter');
                    }
                });
            });

            cameraItems.forEach(item => {
                item.addEventListener('click', function() {
                    const value = this.getAttribute('value');

                    if (selectedFilters.camera === value) {
                        selectedFilters.camera = '';
                        this.classList.remove('selected-filter');
                    } else {
                        cameraItems.forEach(i => i.classList.remove('selected-filter'));
                        selectedFilters.camera = value;
                        this.classList.add('selected-filter');
                    }
                });
            });

            // Xử lý sự kiện nút Lọc
            document.querySelector('.Product__Page__Content__Left__Filter__Apply').addEventListener('click', function() {
                // Lấy giá trị giá
                selectedFilters.minPrice = document.getElementById('minPrice').value;
                selectedFilters.maxPrice = document.getElementById('maxPrice').value;

                // Tạo URL với các tham số lọc
                let url = './product.view.php?';
                const params = [];

                if (selectedFilters.brand) {
                    params.push(`brand=${encodeURIComponent(selectedFilters.brand)}`);
                }

                if (selectedFilters.branch) {
                    params.push(`branch=${encodeURIComponent(selectedFilters.branch)}`);
                }

                if (selectedFilters.ram) {
                    params.push(`ram=${encodeURIComponent(selectedFilters.ram)}`);
                }

                if (selectedFilters.rom) {
                    params.push(`rom=${encodeURIComponent(selectedFilters.rom)}`);
                }

                if (selectedFilters.pin) {
                    params.push(`pin=${encodeURIComponent(selectedFilters.pin)}`);
                }

                if (selectedFilters.camera) {
                    params.push(`camera=${encodeURIComponent(selectedFilters.camera)}`);
                }

                if (selectedFilters.minPrice) {
                    params.push(`minPrice=${encodeURIComponent(selectedFilters.minPrice)}`);
                }

                if (selectedFilters.maxPrice) {
                    params.push(`maxPrice=${encodeURIComponent(selectedFilters.maxPrice)}`);
                }

                // Giữ lại tham số tìm kiếm nếu có
                const keyword = urlParams.get('keyword');
                if (keyword) {
                    params.push(`keyword=${encodeURIComponent(keyword)}`);
                }

                url += params.join('&');

                // Chuyển hướng đến URL mới
                window.location.href = url;
            });

            // Xử lý sự kiện nút Đặt lại
            document.querySelector('.Product__Page__Content__Left__Filter__Reset').addEventListener('click', function() {
                // Xóa tất cả các lựa chọn
                document.querySelectorAll('.selected-filter').forEach(item => {
                    item.classList.remove('selected-filter');
                });

                // Đặt lại các giá trị
                document.getElementById('minPrice').value = '';
                document.getElementById('maxPrice').value = '';

                // Đặt lại biến lưu trữ lựa chọn
                selectedFilters = {
                    brand: '',
                    branch: '',
                    ram: '',
                    rom: '',
                    pin: '',
                    camera: '',
                    minPrice: '',
                    maxPrice: ''
                };
                // Đặt lại giá trị cho ô nhập giá
                document.getElementById('minPrice').value = '';
                document.getElementById('maxPrice').value = '';

                //xóa keyword nếu có
                const urlParams = new URLSearchParams(window.location.search);
                const keyword = urlParams.get('keyword');
                if (keyword) {
                    urlParams.delete('keyword')

                }
                const newUrl = window.location.pathname + '?' + urlParams.toString();
                window.history.replaceState({}, document.title, newUrl);

                // Chuyển hướng đến trang không có bộ lọc
                let url = './product.view.php';

                // // Giữ lại tham số tìm kiếm nếu có
                // const keyword = urlParams.get('keyword');
                // if (keyword) {
                //     url += `?keyword=${encodeURIComponent(keyword)}`;
                // }

                window.location.href = url;
            });
        });
    </script>
</body>

</html>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="./product.view.js"></script>
<script src="../../Controller/class/controller.function.js"></script>
<script src="../../Controller/class/controller.validate.js"></script>
<script src="../../Controller/product/controller-product.product.js"></script>