<?php
session_start();
require_once '../../Model/admin/model-admin.order.php';
require_once '../../Model/admin/model-amin.customer.php';
require_once '../../Model/admin/model-admin.info-product.php';
require_once '../../Model/product/model-product.cart.php';
require_once '../../Model/admin/model-admin.employee.php';
require_once '../../Model/database/connectDataBase.php';
require_once '../class/controller.validate.php';
require_once '../class/controller.function.php';
require_once '../../Controller/vendor/autoload.php';
$CartClass = new Cart;
$OrderClass = new Order;
$CustomerClass = new Customer;
$ProductClass = new Product;
$EmployeeClass = new Employee;
$ValidateData = new ValidateData;
$HandlingFunctions = new HandlingFunctions;
$ConnectDataBase = new ConnectDataBase(); 



if (isset($_POST['action']) && $_POST['action'] === 'load_order') {
    // --- Lấy và Validate dữ liệu đầu vào ---
    $limitOrder = isset($_POST['limitOrder']) && is_numeric($_POST['limitOrder']) ? intval($_POST['limitOrder']) : 5;
    $pageOrder = isset($_POST['pageOrder']) && is_numeric($_POST['pageOrder']) ? intval($_POST['pageOrder']) : 1;
    $queryOrder = isset($_POST['queryOrder']) ? trim($ValidateData->standardizeString($_POST['queryOrder'])) : '';
    $sortIDOrder = isset($_POST['sortIDOrder']) && in_array(strtoupper($_POST['sortIDOrder']), ['ASC', 'DESC']) ? strtoupper($_POST['sortIDOrder']) : 'DESC';
    $sortDateOrder = isset($_POST['sortDateOrder']) && in_array(strtoupper($_POST['sortDateOrder']), ['ASC', 'DESC']) ? strtoupper($_POST['sortDateOrder']) : 'ASC';
    $sortStatusOrder = isset($_POST['sortStatusOrder']) && in_array(strtoupper($_POST['sortStatusOrder']), ['ASC', 'DESC']) ? strtoupper($_POST['sortStatusOrder']) : 'ASC';
    $filterStatus = isset($_POST['filterStatus']) ? trim($_POST['filterStatus']) : '';
    if ($filterStatus !== '' && !is_numeric($filterStatus)) {
        $filterStatus = '';
    }

    // --- NHẬN VÀ VALIDATE NGÀY THÁNG ---
    $startDate = isset($_POST['startDate']) ? trim($_POST['startDate']) : '';
    $endDate = isset($_POST['endDate']) ? trim($_POST['endDate']) : '';

    // Validate định dạng YYYY-MM-DD (hoặc định dạng khác nếu input type khác)
    $dateFormat = 'Y-m-d';
    $startDateValid = DateTime::createFromFormat($dateFormat, $startDate);
    $endDateValid = DateTime::createFromFormat($dateFormat, $endDate);

    // Nếu định dạng không đúng hoặc ngày không hợp lệ, đặt lại thành chuỗi rỗng
    if (!$startDateValid || $startDateValid->format($dateFormat) !== $startDate) {
        $startDate = '';
    }
     if (!$endDateValid || $endDateValid->format($dateFormat) !== $endDate) {
        $endDate = '';
    }

    // Optional: Nếu chỉ muốn lọc khi có cả 2 ngày
    // if ($startDate === '' || $endDate === '') {
    //     $startDate = '';
    //     $endDate = '';
    // }

    // Optional: Đảm bảo ngày bắt đầu <= ngày kết thúc
    if ($startDate !== '' && $endDate !== '' && $startDate > $endDate) {
        // Có thể trả về lỗi hoặc đơn giản là không lọc theo ngày
        $startDate = '';
        $endDate = '';
        // Hoặc: echo json_encode(['error' => 'Ngày bắt đầu không hợp lệ']); exit();
    }

    // --- Tính toán offset ---
    $startOrder = ($pageOrder - 1) * $limitOrder;

    // --- Gọi Model (Truyền thêm $startDate, $endDate) ---
    try {
        $totalRecords = $OrderClass->countRecordOrder($queryOrder, $filterStatus, $startDate, $endDate); // Thêm ngày
        $listOrder = $OrderClass->selectLimitOrder($limitOrder, $startOrder, $queryOrder, $sortIDOrder, $sortDateOrder, $sortStatusOrder, $filterStatus, $startDate, $endDate); // Thêm ngày
    } catch (Exception $e) {
        // ... (xử lý lỗi DB như cũ) ...
        error_log("Database error in load_order: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['tableContent' => '<div class="no-data">Lỗi truy vấn cơ sở dữ liệu.</div>', 'pagination' => '']);
        exit();
    }

    // --- Tạo HTML cho bảng (như cũ) ---
    $tableContent = '';
    if (!empty($listOrder)) {
        foreach ($listOrder as $item) {
            // ... (lấy price, count, status như cũ) ...
            $OrderClass->setDH_IDDonHang($item['DH_IDDonHang']);
            $OrderClass->setDH_IDKhachHang($item['DH_IDKhachHang']);
            $totalPrice = $OrderClass->priceBill();
            $itemCount = $OrderClass->countItemBill();
            $productCount = $OrderClass->countProductBill();
            $statusText = ''; $statusClass = '';
            switch ($item['DH_TrangThaiDonHang']) { /* ... */
                case 1: $statusText = 'Đã hủy'; $statusClass = 'status-cancelled'; break;
                case 2: $statusText = 'Hoàn thành'; $statusClass = 'status-completed'; break;
                case 3: $statusText = 'Đã xử lý'; $statusClass = 'status-processed'; break;
                case 4: $statusText = 'Đang xử lý'; $statusClass = 'status-pending'; break;
                default: $statusText = 'Không xác định'; $statusClass = 'status-unknown';
            }

            $tableContent .= '
                <div class="table__order__tbody__tr" data-order-id="' . $item['DH_IDDonHang'] . '" data-customer-id="' . $item['DH_IDKhachHang'] . '" onclick="window.location.href=\'./order-info-admin.php?id-order=' . $item['DH_IDDonHang'] . '&id-customer=' . $item['DH_IDKhachHang'] . '&menu=order\'" style="cursor:pointer;">
                    <div class="table__order__tbody__tr__td">DH' . htmlspecialchars($item['DH_IDDonHang']) . '</div>
                    <div class="table__order__tbody__tr__td">' . htmlspecialchars($item['KH_TenKhachHang']) . '</div>
                    <div class="table__order__tbody__tr__td">' . htmlspecialchars($item['KH_SDTKhachHang']) . '</div>
                    <div class="table__order__tbody__tr__td">' . number_format($totalPrice ?: 0, 0, ',', '.') . 'đ</div>
                    <div class="table__order__tbody__tr__td">' . ($itemCount ?: 0) . '</div>
                    <div class="table__order__tbody__tr__td">' . ($productCount ?: 0) . '</div>
                    <div class="table__order__tbody__tr__td">
                        <span class="order__status ' . $statusClass . '">' . $statusText . '</span>
                    </div>
                    <div class="table__order__tbody__tr__td">' . date("d/m/Y", strtotime($item['DH_NgayDatDonHang'])) . '</div>
                </div>';
        }
    }

        // --- Tạo HTML cho phân trang (Sử dụng DIV, giống customer) ---
        $totalPages = ceil($totalRecords / $limitOrder);
        $paginationHTML = ''; // Khởi tạo chuỗi HTML rỗng
 
        // Chỉ tạo HTML phân trang nếu có nhiều hơn 1 trang
        if ($totalPages > 1) {
            // Container div
            $paginationHTML .= '<div class="pagination__order">'; // Sử dụng class pagination__order
 
            // Nút Previous (Trang trước)
            $prevDisabled = ($pageOrder <= 1) ? 'disabled' : ''; // Disable nếu đang ở trang 1
            $prevPage = ($pageOrder > 1) ? $pageOrder - 1 : 1;
            // Div cho nút Previous, thêm value và class pagination__order__item
            $paginationHTML .= '<div class="pagination__order__item pagination__order__prev ' . $prevDisabled . '" value="' . $prevPage . '">
                                   <i class="bx bx-left-arrow-alt"></i>
                                 </div>';
 
            // Logic tạo các nút số trang (hiển thị giới hạn số nút)
            $maxVisiblePages = 5; // Giữ nguyên logic giới hạn số nút
            $startPage = 1;
            $endPage = $totalPages;
 
            if ($totalPages > $maxVisiblePages) {
                $halfMax = floor($maxVisiblePages / 2);
                $startPage = max(1, $pageOrder - $halfMax);
                $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
 
                if ($endPage === $totalPages) {
                    $startPage = max(1, $totalPages - $maxVisiblePages + 1);
                }
                else if ($startPage === 1 && ($endPage - $startPage + 1) < $maxVisiblePages) {
                     $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
                }
            }
 
            // Thêm nút '...' và trang đầu nếu cần
            if ($startPage > 1) {
                // Div cho nút trang 1, thêm value và class pagination__order__item
                $paginationHTML .= '<div class="pagination__order__item" value="1">1</div>';
                if ($startPage > 2) {
                    // Div cho dấu ..., thêm class disabled để không click được
                    $paginationHTML .= '<div class="pagination__order__item pagination__order__dots disabled">...</div>';
                }
            }
 
            // Tạo các nút số trang trong khoảng hiển thị
            for ($i = $startPage; $i <= $endPage; $i++) {
                $activeClass = ($i == $pageOrder) ? 'active' : ''; // Đánh dấu trang hiện tại
                // Div cho nút số trang, thêm value và class pagination__order__item
                $paginationHTML .= '<div class="pagination__order__item ' . $activeClass . '" value="' . $i . '">' . $i . '</div>';
            }
 
            // Thêm nút '...' và trang cuối nếu cần
            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) {
                     // Div cho dấu ..., thêm class disabled
                    $paginationHTML .= '<div class="pagination__order__item pagination__order__dots disabled">...</div>';
                }
                // Div cho nút trang cuối, thêm value và class pagination__order__item
                $paginationHTML .= '<div class="pagination__order__item" value="' . $totalPages . '">' . $totalPages . '</div>';
            }
            // Kết thúc logic tạo nút số trang
 
            // Nút Next (Trang sau)
            $nextDisabled = ($pageOrder >= $totalPages) ? 'disabled' : ''; // Disable nếu đang ở trang cuối
            $nextPage = ($pageOrder < $totalPages) ? $pageOrder + 1 : $totalPages;
             // Div cho nút Next, thêm value và class pagination__order__item
            $paginationHTML .= '<div class="pagination__order__item pagination__order__next ' . $nextDisabled . '" value="' . $nextPage . '">
                                   <i class="bx bx-right-arrow-alt"></i>
                                 </div>';
 
            $paginationHTML .= '</div>'; // Kết thúc container div
        } // Kết thúc if ($totalPages > 1)
 


    // --- Trả về kết quả dạng JSON ---
    header('Content-Type: application/json');
    echo json_encode([
        'tableContent' => $tableContent,
        'pagination' => $paginationHTML
    ]);
    exit(); // <<<--- RẤT QUAN TRỌNG

} 
function getPriceBill()
{
    $CustomerClass = new Customer;
    $ProductClass = new Product;
    $CartClass = new Cart;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $idCustomer = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $CartClass->setGH_IDKhachHang($idCustomer);
    $listCart = $CartClass->selectAllCart();
    $priceBill = 0;
    $salePrice = 0;
    $quantityProduct = 0;
    $totalSalePrice = 0;
    $VAT = 0.1;
    for ($i = 0; $i < count($listCart); $i++) {
        $ProductClass->setSP_IDSanPham($listCart[$i]['GH_IDSanPham']);
        $salePrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']) -
            (intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']) * intval($ProductClass->selectProductByID()['SP_GiamGiaSanPham']) / 100);
        $priceBill += $salePrice *  intval($listCart[$i]['GH_SLSanPhamGioHang']);
        $quantityProduct += $listCart[$i]['GH_SLSanPhamGioHang'];
        $totalSalePrice += (intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']) * intval($listCart[$i]['GH_SLSanPhamGioHang']))
            - ($salePrice * intval($listCart[$i]['GH_SLSanPhamGioHang']));
    }
    $totalPriceBill = $priceBill + ($priceBill * $VAT);
    return [$priceBill, $totalSalePrice, count($listCart), $quantityProduct, $totalPriceBill];
}

if (isset($_POST['saveBill']) && $_POST['saveBill'] === 'save-bill') {
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $OrderClass->setDH_IDKhachHang($CustomerClass->selectCustomerByEmail()['KH_IDKhachHang']);
    $OrderClass->setDH_NgayDatDonHang(date('Y-m-d'));
    $OrderClass->setDH_TrangThaiDonHang(4);

   
    $diaChiTam = isset($_POST['diachi_tam']) ? trim($_POST['diachi_tam']) : '';
    if ($diaChiTam === '') {
        // nếu không nhập địa chỉ tạm thì dùng địa chỉ mặc định
        $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
        $diaChiTam = $CustomerClass->selectCustomerByEmail()['KH_DiaChiKhachHang'];
    }
    
    // Gọi insert đơn hàng và truyền địa chỉ giao hàng
    $OrderClass->insertBill($diaChiTam);
    $lastBillID = $OrderClass->selectBillNewID(); // Lưu ID đơn hàng vừa tạo để dùng

    if ($lastBillID) {
        $count = 0;
        $CartClass->setGH_IDKhachHang($CustomerClass->selectCustomerByEmail()['KH_IDKhachHang']);
    
        $listCart = $CartClass->selectCartByCustomerID();
    
        for ($i = 0; $i < count($listCart); $i++) {
            $OrderClass->setCTDH_IDDonHang($lastBillID); // Dùng đúng đơn hàng vừa tạo
            $OrderClass->setCTDH_IDSanPham($listCart[$i]['GH_IDSanPham']);
            $OrderClass->setCTDH_SLSanPham($listCart[$i]['GH_SLSanPhamGioHang']);
            $OrderClass->insertBillDetals();
        }

        $idCustomer = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
        $listCart = $CartClass->selectAllCart();

        $output = '';
        for ($i = 0; $i < count($listCart); $i++) {
            $ProductClass->setSP_IDSanPham($listCart[$i]['GH_IDSanPham']);
            $oldPrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']);
            $newPrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']);
            $salePrice = intval($ProductClass->selectProductByID()['SP_GiamGiaSanPham']);
            if ($salePrice === 0) {
                $htmlSalePrice = '';
            } else {
                $newPrice = $oldPrice - ($newPrice * $salePrice) / 100;
                $htmlSalePrice =  '<span class="regular-price">' . number_format($oldPrice) . '</span>';
            }

            $output = '';
            for ($i = 0; $i < count($listCart); $i++) {
                $ProductClass->setSP_IDSanPham($listCart[$i]['GH_IDSanPham']);
                $oldPrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']);
                $newPrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']);
                $salePrice = intval($ProductClass->selectProductByID()['SP_GiamGiaSanPham']);
                if ($salePrice === 0) {
                    $htmlSalePrice = '';
                } else {
                    $newPrice = $oldPrice - ($newPrice * $salePrice) / 100;
                    $htmlSalePrice =  '<span class="regular-price"> style="
                    text-decoration: line-through;
                    color: rgb(207, 207, 211);
                "' . number_format($oldPrice) . '</span>';
                }

                $output .= '
                    <tr>
                        <td style="text-align:center">' . $ProductClass->selectProductByID()['SP_TenSanPham'] . '</td>
                        <td style="text-align:center">' . $listCart[$i]['GH_SLSanPhamGioHang'] . '</td>
                        <td style="text-align:center">' . number_format($newPrice) . '</td>
                    </tr>
                ';
            }
        }

        $fromEmail = 'thanhdai11733621@gmail.com';
        $fromPass = 'ryfwcoruiafszywl';
        $fromName = 'SB MOBILE SHOP';
        $toName = $CustomerClass->selectCustomerByEmail()['KH_TenKhachHang'];
        $toEmail = $CustomerClass->selectCustomerByEmail()['KH_EmailKhachHang'];
        $subject = 'SB MOBILE SHOP - Đặt Hàng Thành Công';
        $body =
            '
        <h1 style="color: #7d5fff">Xin chào ' . $CustomerClass->selectCustomerByEmail()['KH_TenKhachHang'] . '</h1>
        <h3 style="color: #ff3838">CẢM ƠN BẠN ĐÃ MUA HÀNG TẠI SB MOBILE</h3>
        <h3 style="color: #32ff7e">Thông Tin Đơn Hàng Của Bạn:</h3>
        <table style="border: 2px solid #7d5fff">
        <thead>
            <tr>
                <th style="width: 100px; color: #7d5fff"></h5>Mã Đơn Hàng<h5></th>
                <th style="width: 100px; color: #7d5fff"></h5>Tiền Hàng<h5></th>
                <th style="width: 100px; color: #7d5fff"></h5>Giảm Giá<h5></th>
                <th style="width: 100px; color: #7d5fff"></h5>Thuế VAT<h5></th>
                <th style="width: 100px; color: #7d5fff"></h5>Tổng Cộng<h5></th>
                <th style="width: 100px; color: #7d5fff"></h5>Tình Trạng<h5></th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <td style="text-align:center">HD' . $OrderClass->selectBillNewID() . '</td>
                    <td style="text-align:center">' . number_format(getPriceBill()[0]) . '</td>
                    <td style="text-align:center">' . number_format(getPriceBill()[1]) . '</td>
                    <td style="text-align:center">10%</td>
                    <td style="text-align:center">' . number_format(getPriceBill()[4]) . '</td>
                    <td style="text-align:center">Chờ Xử Lý</td>
                </tr> 
            </tbody>
        </table>
        <br>
        <h3 style="color: #32ff7e">Danh Sách Sản Phẩm Của Bạn:</h3>
        <table style="border: 2px solid #7d5fff">
            <thead>
                <tr>
                    <th style="width: 200px; color: #7d5fff"></h5>Tên Sản Phẩm<h5></th>
                    <th style="width: 200px; color: #7d5fff"></h5>Số Lượng Mua<h5></th>
                    <th style="width: 200px; color: #7d5fff"></h5>Giá Bán<h5></th>
                </tr>
            </thead>
        <tbody>' . $output . '</tbody></table>
        </br>
        <h3 style="color: #7d5fff">Chúng Tôi Sẽ Cố Gắng Xử Lý Đơn Hàng Trong Thời Gian Sớm Nhất!</h3>
        SĐT: +84 0373874418 <br>
        Email: sbmobile@gmail.com
        ';
        if ($HandlingFunctions->sendEmailByPHPMailer($fromEmail, $fromPass, $fromName, $toName, $toEmail, $subject, $body)) {
            if ($CartClass->deleteProductCartCustomer()) {
                echo 'success';
            } else {
                echo 'failed';
            }
        } else {
            echo 'failed';
        }
    } else {
        echo 'failed';
    };
}

// $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
// $khach = $CustomerClass->selectCustomerByEmail();

// $diaChiGiaoHang = $khach['KH_DiaChiTamThoi'] !== null && $khach['KH_DiaChiTamThoi'] !== ''
//     ? $khach['KH_DiaChiTamThoi']
//     : $khach['KH_DiaChiKhachHang'];

if (isset($_POST['fetchBill']) && $_POST['fetchBill'] === 'fetch-bill') {
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $idCustomer = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $OrderClass->setDH_IDKhachHang($idCustomer);
    $listBill = $OrderClass->selectAllBillByCustomerID();
    for ($i = 0; $i < count($listBill); $i++) {
        $rowProduct = '';
        $OrderClass->setDH_IDDonHang($listBill[$i]['DH_IDDonHang']);
        $TrangThaiDonHang = '';
        if (intval($listBill[$i]['DH_TrangThaiDonHang']) === 1) {
            $TrangThaiDonHang = '<p class="status-cancel">Đã hủy</p>';
            $CancelDonHang = '';
        } else if (intval($listBill[$i]['DH_TrangThaiDonHang']) === 4) {
            $TrangThaiDonHang = '<p class="status-wait">Chờ xử lý</p>';
            $CancelDonHang = '<div id="CancelBill" value="' . $listBill[$i]['DH_IDDonHang'] . '">Hủy Hàng</div>';
        } else if (intval($listBill[$i]['DH_TrangThaiDonHang']) === 3) {
            $TrangThaiDonHang = '<p class="status-handled">Đã xử lý</p>';
            $CancelDonHang = '';
        } else if (intval($listBill[$i]['DH_TrangThaiDonHang']) === 2) {
            $TrangThaiDonHang = '<p class="status-success">Hoàn thành</p>';
            $CancelDonHang = '';
        }
        for ($j = 0; $j < count($OrderClass->selectProductBill()); $j++) {
        
            $rowProduct .= '
                <div class="Bill__Content__Table__Row">
                    <div class="Bill__Content__Table__Column"><img src="../../Controller/admin/' . $OrderClass->selectProductBill()[$j]['SP_Image1SanPham'] . '" alt=""></div>
                    <div class="Bill__Content__Table__Column">' . $OrderClass->selectProductBill()[$j]['SP_TenSanPham'] . '</div>
                    <div class="Bill__Content__Table__Column">' . $OrderClass->selectProductBill()[$j]['CTDH_SLSanPham'] . '</div>
                    <div class="Bill__Content__Table__Column">' . number_format($OrderClass->selectProductBill()[$j]['GiamGia']) . '</div>
                    <div class="Bill__Content__Table__Column">' . $listBill[$i]['DH_DiaChiGiaoHang']  . '</div>
                    <div class="Bill__Content__Table__Column">' . $OrderClass->selectProductBill()[$j]['DH_NgayDatDonHang'] . '</div>
              
                </div>
            ';
        }
        echo '
        <div class="Profile__Panel__Item__Bill__Left__Bill__Box">
            <div class="Profile__Panel__Item__Bill__Left__Bill__Title">
                <div class="Bill__Title"><i class="fa-solid fa-clipboard"></i> ĐƠN HÀNG <span>HD' . $listBill[$i]['DH_IDDonHang'] . '</span></div>
                <div class="Bill__Info__Box">
                    <div class="Bill__Info__TotalPrice Bill__Info__Item">
                        <p>Tổng Tiền</p>
                        <p>' . number_format($OrderClass->priceBill()) . ' VND</p>
                    </div>
                    <div class="Bill__Info__CreateDay Bill__Info__Item">
                        <p>Ngày tạo</p>
                        <p>' . $listBill[$i]['DH_NgayDatDonHang'] . '</p>
                    </div>
                    <div class="Bill__Info__Status Bill__Info__Item">
                        <p>Tình Trạng</p>
                        <p>' . $TrangThaiDonHang . '</p>
                    </div>
                    ' . $CancelDonHang . '
                </div>
            </div>
            <div class="Profile__Panel__Item__Bill__Left__Bill__Content">
                <div class="Profile__Panel__Item__Bill__Left__Bill__Content__Header">
                    <div class="Bill__Content__Header__Column">Ảnh Sản Phẩm</div>
                    <div class="Bill__Content__Header__Column">Tên Sản Phẩm</div>
                    <div class="Bill__Content__Header__Column">Số Lượng</div>
                    <div class="Bill__Content__Header__Column">Đơn Giá</div>
                    <div class="Bill__Content__Header__Column">' . 'Địa chỉ' . '</div>
                    <div class="Bill__Content__Header__Column">Ngày Mua</div>

                </div>
                <div class="Profile__Panel__Item__Bill__Left__Bill__Content__Table">
                ' . $rowProduct . '
                </div>
            </div>
        </div>
        ';
    }
}


function fetchOrder($limitOrder, $startOrder, $queryOrder, $sortIDOrder, $sortDateOrder, $sortStatusOrder)
{
    $OrderClass = new Order;
    $listOrder = $OrderClass->selectLimitOrder($limitOrder, $startOrder, $queryOrder, $sortIDOrder, $sortDateOrder, $sortStatusOrder);
    $output = '';
    if ($listOrder) {
        for ($i = 0; $i < count($listOrder); $i++) {
            $OrderClass->setDH_IDDonHang($listOrder[$i]['DH_IDDonHang']);
            $OrderClass->setDH_IDKhachHang($listOrder[$i]['KH_IDKhachHang']);
            $output .= '
                <a href="./order-info-admin.php?id-order=' . $listOrder[$i]['DH_IDDonHang'] . '&id-customer=' . $listOrder[$i]['KH_IDKhachHang'] . '&menu=order">
                    <div class="table__order__tbody__tr">
                        <div class="table__order__tbody__tr__td">DH' . $listOrder[$i]['DH_IDDonHang'] . '</div>
                        <div class="table__order__tbody__tr__td">' . $listOrder[$i]['KH_TenKhachHang'] . '</div>
                        <div class="table__order__tbody__tr__td">' . $listOrder[$i]['KH_SDTKhachHang'] . '</div>
                        <div class="table__order__tbody__tr__td">' . number_format($OrderClass->priceBill()) . '</div>
                        <div class="table__order__tbody__tr__td">' . $OrderClass->countItemBill() . '</div>
                        <div class="table__order__tbody__tr__td">' . $OrderClass->countProductBill() . '</div>';

            if (intval($listOrder[$i]['DH_TrangThaiDonHang']) === 1) {
                $output .= '
                    <div class="table__order__tbody__tr__td">
                        <div class="order__status order__status cancel">Đã Hủy</div>
                    </div>';
            } else if (intval($listOrder[$i]['DH_TrangThaiDonHang']) === 2) {
                $output .= '
                    <div class="table__order__tbody__tr__td">
                        <div class="order__status order__status success">Hoàn Thành</div>
                    </div>';
            } else if (intval($listOrder[$i]['DH_TrangThaiDonHang']) === 3) {
                $output .= '
                    <div class="table__order__tbody__tr__td">
                        <div class="order__status order__status handled">Đã Xử Lý</div>
                    </div>';
            } else if (intval($listOrder[$i]['DH_TrangThaiDonHang']) === 4) {
                $output .= '
                    <div class="table__order__tbody__tr__td">
                        <div class="order__status order__status wait">Chờ Xử Lý</div>
                    </div>';
            }

            $output .= '
                        <div class="table__order__tbody__tr__td">' . $listOrder[$i]['DH_NgayDatDonHang'] . '</div>
                    </div>
                </a>';
        }
        return $output;
    }
}

if (isset($_POST['fetchOrder']) && $_POST['fetchOrder'] === 'fetch-order') {
    /* ============================= Xử lý limit ============================= */
    isset($_POST['limitOrder']) ?  $limitOrder = $_POST['limitOrder'] : $limitOrder = '5';

    /* ============================= Xử lý page ============================= */
    if (isset($_POST['pageOrder']) && $_POST['pageOrder'] > 1) {
        $pageOrder = $_POST['pageOrder'];
        $startOrder = ((intval($pageOrder) - 1) * $limitOrder);
    } else {
        $pageOrder = 1;
        $startOrder = 0;
    }

    /* ============================= Xử lý query ============================= */
    isset($_POST['queryOrder']) && $_POST['queryOrder'] !== '' ? $queryOrder = str_replace('"', '%', $_POST['queryOrder']) : $queryOrder = '';

    /* ============================= Xử lý sort ============================= */
    if (isset($_POST['sortIDOrder']) && isset($_POST['sortDateOrder'])) {
        if (($_POST['sortIDOrder'] === '' || $_POST['sortIDOrder'] === 'ASC' || $_POST['sortIDOrder'] === 'DESC') &&
            ($_POST['sortDateOrder'] === '' || $_POST['sortDateOrder'] === 'ASC' || $_POST['sortDateOrder'] === 'DESC') &&
            ($_POST['sortStatusOrder'] === '' || $_POST['sortStatusOrder'] === 'ASC' || $_POST['sortStatusOrder'] === 'DESC')
        ) {
            // Trường hợp tất cả đều không rỗng, thì ưu tiên cho sortID
            if ($_POST['sortIDOrder'] !== '' && $_POST['sortDateOrder'] !== '' && $_POST['sortStatusOrder'] !== '') {
                $sortIDOrder = $_POST['sortIDOrder'];
                $sortDateOrder = '';
                $sortStatusOrder = '';
            }
            // Trường hợp sortID không rỗng, sortDate và sortPosition rỗng thì lấy sortID
            else if ($_POST['sortIDOrder'] !== '' && $_POST['sortDateOrder'] === '' && $_POST['sortStatusOrder'] === '') {
                $sortIDOrder = $_POST['sortIDOrder'];
                $sortDateOrder = '';
                $sortStatusOrder = '';
            }
            // Trường hợp sortDate không rỗng, sortID và sortPosition rỗng thì lấy sortDate
            else if ($_POST['sortIDOrder'] === '' && $_POST['sortDateOrder'] !== '' && $_POST['sortStatusOrder'] === '') {
                $sortIDOrder = '';
                $sortDateOrder = $_POST['sortDateOrder'];
                $sortStatusOrder = '';
            }
            // Trường hợp sortPosition không rỗng, sortID và sortDate rỗng thì lấy sortPosition
            else if ($_POST['sortIDOrder'] === '' && $_POST['sortDateOrder'] === '' && $_POST['sortStatusOrder'] !== '') {
                $sortIDOrder = '';
                $sortDateOrder = '';
                $sortStatusOrder = $_POST['sortStatusOrder'];
            }
            // Trường hợp sortID, sortDate, sortPosition đều rỗng thì ưu tiên sortID
            else if ($_POST['sortIDOrder'] === '' && $_POST['sortDateOrder'] === '' && $_POST['sortStatusOrder'] === '') {
                $sortIDOrder = 'DESC';
                $sortDateOrder = '';
                $sortStatusOrder = '';
            }
        } else {
            $sortIDOrder = 'DESC';
            $sortDateOrder = '';
            $sortStatusOrder = '';
        }
    }

    $dataOrder =  fetchOrder(
        $ValidateData->standardizeString($limitOrder),
        $ValidateData->standardizeString($startOrder),
        $ValidateData->standardizeString($queryOrder),
        $ValidateData->standardizeString($sortIDOrder),
        $ValidateData->standardizeString($sortDateOrder),
        $ValidateData->standardizeString($sortStatusOrder)
    );

    $totalRecords = $OrderClass->countRecordOrder($queryOrder);
    $totalButton = ceil(intval($totalRecords) / intval($limitOrder));
    $prevButton = "";
    $nextButton = "";
    $pageButton = "";
    $arrayButton = array();

    if ($totalButton > 8) {
        if ($pageOrder < 5) {
            for ($i = 1; $i <= 5; $i++) {
                $arrayButton[] = $i;
            }
            $arrayButton[] = '...';
            $arrayButton[] = $totalButton;
        } else {
            $endLimit = $totalButton - 5;
            if ($pageOrder > $endLimit) {
                $arrayButton[] = 1;
                $arrayButton[] = '...';
                for ($i = $endLimit; $i <= $totalButton; $i++) {
                    $arrayButton[] = $i;
                }
            } else {
                $arrayButton[] = 1;
                $arrayButton[] = '...';
                for ($i = $pageOrder - 1; $i <= $pageOrder + 1; $i++) {
                    $arrayButton[] = $i;
                }
                $arrayButton[] = '...';
                $arrayButton[] = $totalButton;
            }
        }
    } else {
        for ($i = 1; $i <= $totalButton; $i++) {
            $arrayButton[] = $i;
        }
    }

    for ($i = 0; $i < count($arrayButton); $i++) {
        if (intval($pageOrder) == $arrayButton[$i]) {
            $pageButton .= '<div class="pagination__order__item active" value="' . $arrayButton[$i] . '">' . $arrayButton[$i] . '</div>';
            $prevID = $arrayButton[$i] - 1;
            if ($prevID > 0) {
                $prevButton = ' <div class="pagination__order__item pagination__order__prev" value="' . $prevID . '">
                                        <i class="bx bx-left-arrow-alt"></i>
                                    </div>';
            } else {
                $prevButton = ' <div class="pagination__order__item pagination__order__prev" value="1">
                                        <i class="bx bx-left-arrow-alt"></i>
                                    </div>';
            }
            $nextID = $arrayButton[$i] + 1;
            if ($nextID > $totalButton) {
                $nextButton = ' <div class="pagination__order__item pagination__order__next" value="' . $totalButton . '">
                                        <i class="bx bx-right-arrow-alt"></i>
                                    </div>';
            } else {
                $nextButton = ' <div class="pagination__order__item pagination__order__next" value="' . $nextID . '">
                                        <i class="bx bx-right-arrow-alt"></i>
                                    </div>';
            }
        } else {
            if ($arrayButton[$i] == '...') {
                $pageButton .= '<div class="pagination__order__item__dots">...</div>';
            } else {
                $pageButton .= '<div class="pagination__order__item" value="' . $arrayButton[$i] . '">' . $arrayButton[$i] . '</div>';
            }
        }
    }

    $paginationButton = $prevButton . $pageButton . $nextButton;
    $dataResponse = [$paginationButton, $dataOrder];
    print_r(json_encode($dataResponse));
}



if (isset($_POST['cancelBill']) && $_POST['cancelBill'] === 'cancel-bill') {
    if (isset($_POST['cancelBillID']) && !empty($_POST['cancelBillID'])) {
        $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
        $idCustomer = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
        $OrderClass->setDH_TrangThaiDonHang(1);
        $OrderClass->setDH_IDDonHang($_POST['cancelBillID']);
        $OrderClass->setDH_IDKhachHang($idCustomer);
        if (intval($OrderClass->selectBillStatus()) === 4) {
            if ($OrderClass->updateBillStatus()) {
                echo 'success';
            } else {
                echo 'failed';
            }
        } else {
            echo 'failed';
        }
    }
}
