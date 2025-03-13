<?php
@session_start();
require_once '../../Model/admin/model-admin.info-product.php';
require_once '../../Model/product/model-product.cart.php';
require_once '../../Model/admin/model-amin.customer.php';
require_once '../../Model/database/connectDataBase.php';
require_once '../class/controller.validate.php';

$CustomerClass = new Customer;
$ProductClass = new Product;
$CartClass = new Cart;
$ValidateData = new ValidateData;

function fetchProduct($limitProduct, $startProduct, $queryProduct)
{
    $ProductClass = new Product;
    $CustomerClass = new Customer;
    $listProduct = $ProductClass->selectProductPage($limitProduct, $startProduct, $queryProduct);
    $output = '';
    if ($listProduct) {
        for ($i = 0; $i < count($listProduct); $i++) {
            $oldPrice = intval($listProduct[$i]['SP_GiaBanSanPham']);
            $newPrice = intval($listProduct[$i]['SP_GiaBanSanPham']);
            $salePrice = intval($listProduct[$i]['SP_GiamGiaSanPham']);
        }
        return $output;
    }
}

//////////////////////
// Hàm hiển thị danh sách sản phẩm trong giỏ hàng
function fetchCart()
{
    global $CustomerClass;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $customerID = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $cartItems = getCartItems($customerID);

    $output = '';
    $output .= '
    <ul class="order-lists">
        <li class="li-sanpham">Sản Phẩm</li>
        <li class="li-dongia">Đơn Giá</li>
        <li class="li-tongdongia">Tổng Đơn Giá</li>
        <li class="li-soluong">Số Lượng</li>
        <li class="li-xoa">Acction</li>
    </ul>
    <div class="summary-border"></div>';

    if (!empty($cartItems)) {
        global $ProductClass;
        foreach ($cartItems as $item) {
            $output .= renderCartItem($item, $ProductClass);
        }
    } else {
        $output .= '<div class="order-row">Giỏ hàng trống</div>';
    }

    return $output;
}
// Hàm lấy thông tin giỏ hàng để hiển thị
function getCartItems($customerID)
{
    global $CartClass;
    $CartClass->setGH_IDKhachHang($customerID);
    return $CartClass->selectAllCart();
}
//hàm cập nhật lại tổng tiền của từng sản phẩm
function calculateItemTotalPrice($productID, $quantity)
{
    global $ProductClass;
    $ProductClass->setSP_IDSanPham($productID);
    $product = $ProductClass->selectProductByID();
    $oldPrice = intval($product['SP_GiaBanSanPham']);
    $salePrice = intval($product['SP_GiamGiaSanPham']);
    $newPrice = $oldPrice - ($oldPrice * $salePrice) / 100;
    $newTotalPrice = $quantity * $newPrice;
    return $newTotalPrice;
}

// Hàm render HTML cho từng sản phẩm trong giỏ hàng
function renderCartItem($item, $ProductClass)
{
    $ProductClass->setSP_IDSanPham($item['GH_IDSanPham']);
    $product = $ProductClass->selectProductByID();
    $oldPrice = intval($product['SP_GiaBanSanPham']);
    $salePrice = intval($product['SP_GiamGiaSanPham']);
    $newPrice = $oldPrice - ($oldPrice * $salePrice) / 100;
    $totalPrice = calculateItemTotalPrice($item['GH_IDSanPham'], $item['GH_SLSanPhamGioHang']);

    $htmlSalePrice = '<span class="regular-price">' . number_format($oldPrice) . '</span>';
    $htmltotalPrice = '<span class="total-price" data-product-id="' . $item['GH_IDSanPham'] . '">' . number_format($totalPrice) . '</span>';

    $output = '
    <div class="order-row">
        <div class="order-row-left">
        <a class="order-row-img"  href="../../View/product/details.view.php?id-product=' . $product['SP_IDSanPham'] . '">
            <div class="order-row-img" >
                <img src="../../Controller/admin/' . $product['SP_Image1SanPham'] . '" alt="">
            </div>
         </a>
            <div class="order-row-details">
            <a href="../../View/product/details.view.php?id-product=' . $product['SP_IDSanPham'] . '">
                <h4>' . $product['SP_TenSanPham'] . '</h4>
                </a>
                <div class="row-details-items">
                    <span class="row-items-tittle">Hãng: </span>
                    <span>' . $product['SP_HangSanPham'] . '</span>
                </div>
                <div class="row-details-items">
                    <span class="row-items-tittle">Dung lượng:</span>
                    <span>' . $product['SP_RAMSanPham'] . 'GB/' . $product['SP_ROMSanPham'] . 'GB</span>
                </div>
                <div class="row-details-items-price">
                    <span class="row-details-items-sale-price">' . number_format($newPrice) . '</span>
                    ' . $htmltotalPrice . '
                </div>
                <div class="row-details-items-quantity-details">
                    <input type="number" id="quantityCartLeft" value="' . $item['GH_SLSanPhamGioHang'] . '"
                    data-productID="' . $product['SP_IDSanPham'] . '">
                    <div class="quantity-navigation">
                        <div class="quantity-inc" id="increaseQuantityLeft"><i class="bx bx-chevron-up"></i></div>
                        <div class="quantity-dec" id="decreaseQuantityLeft"><i class="bx bx-chevron-down"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-row-right">
            <div class="order-right-price">
                <span class="sale-price">' . number_format($newPrice) . '</span>
                ' . $htmltotalPrice . '
            </div>
            <div class="order-right-quantity">
                <span>SL</span>
                <div class="quantity-details">
                    <input type="number" min="1" id="quantityCartRight" value="' . $item['GH_SLSanPhamGioHang'] . '"
                    data-productID="' . $product['SP_IDSanPham'] . '">
                    <div class="quantity-navigation">
                        <div class="quantity-inc" id="increaseQuantityRight"><i class="bx bx-chevron-up"></i></div>
                        <div class="quantity-dec" id="decreaseQuantityRight"><i class="bx bx-chevron-down"></i></div>
                    </div>
                </div>
            </div>
            <div class="order-right-remove" id="deleteCart" value="' . $product['SP_IDSanPham'] . '">
                <i class="bx bx-x"></i>
            </div>
        </div>
    </div>
    ';

    return $output;
}


// Hàm trả về thông tin cập nhật số lượng và giá sản phẩm
function updateItem($productID, $newQuantity, $customerID)
{
    global $CartClass;
    $CartClass->setGH_IDSanPham($productID);
    $CartClass->setGH_IDKhachHang($customerID);
    $CartClass->setGH_SLSanPhamGioHang($newQuantity);

    if ($CartClass->updateQuantityCart()) {
        $newTotalPrice = calculateItemTotalPrice($productID, $newQuantity);
        return json_encode(['newTotalPrice' => number_format($newTotalPrice)]);
    } else {
        return json_encode(['status' => 'error', 'message' => 'Cập nhật thất bại']);
    }
}

if (isset($_POST['updateItem'])) {
    $productID = $_POST['productID'];
    $newQuantity = $_POST['newQuantity'];
    $CustomerClass = new Customer;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $customerID = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    echo updateItem($productID, $newQuantity, $customerID);
    exit;
}




function fetchCartBill()
{
    $CustomerClass = new Customer;
    $ProductClass = new Product;
    $CartClass = new Cart;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $idCustomer = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $CartClass->setGH_IDKhachHang($idCustomer);
    $listCart = $CartClass->selectAllCart();
    $output = '';
    $output .= '
    <ul class="order-lists">
    <li class="li-sanpham">Sản Phẩm</li>
    <li class="li-dongia">Đơn Giá</li>
    <li class="li-tongdongia">Tổng Đơn Giá</li>
    <li class="li-soluong">Số Lượng</li>
    <li class="li-xoa">Acction</li>
    </ul>
    <div class="summary-border"></div>';
    for ($i = 0; $i < count($listCart); $i++) {
        $ProductClass->setSP_IDSanPham($listCart[$i]['GH_IDSanPham']);
        $oldPrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']);
        $newPrice = intval($ProductClass->selectProductByID()['SP_GiaBanSanPham']);
        $salePrice = intval($ProductClass->selectProductByID()['SP_GiamGiaSanPham']);
        $newPrice = $oldPrice - ($newPrice * $salePrice) / 100;
        $htmlSalePrice =  '<span class="regular-price">' . number_format($oldPrice) . '</span>';

        $totalPrice = $listCart[$i]['GH_SLSanPhamGioHang'] * $newPrice;
        $htmltotalPrice =  '<span class="total-price" data-product-id=' . $listCart[$i]['GH_IDSanPham'] . '>' .    number_format($totalPrice) . '</span>';


        $output .= '
    
        <div class="order-row">
            <div class="order-row-left">
                <div class="order-row-img">
                    <img src="../../Controller/admin/' . $ProductClass->selectProductByID()['SP_Image1SanPham'] . '" alt="">
                </div>
                <div class="order-row-details">
                    <h4>' . $ProductClass->selectProductByID()['SP_TenSanPham'] . '</h4>
                    <div class="row-details-items">
                        <span class="row-items-tittle">Hãng: </span>
                        <span>' . $ProductClass->selectProductByID()['SP_HangSanPham'] . '</span>
                    </div>
                    <div class="row-details-items">
                        <span class="row-items-tittle">Dung lượng:</span>
                        <span>' . $ProductClass->selectProductByID()['SP_RAMSanPham'] . 'GB/' . $ProductClass->selectProductByID()['SP_ROMSanPham'] . 'GB</span>
                    </div>
                    <div class="row-details-items-price">
                        <span class="row-details-items-sale-price">' . number_format($newPrice) . '</span>
                        ' . $htmltotalPrice . '
                    </div>
                    <div class="row-details-items-quantity-details">
                        <span>SL: </span>
                        ' . $listCart[$i]['GH_SLSanPhamGioHang'] . '
                    </div>
                </div>
            </div>
            <div class="order-row-right">
                <div class="order-right-price">
                    <span class="sale-price">' . number_format($newPrice) . '</span>
                    ' . $htmltotalPrice . '
                </div>
                <div class="order-right-quantity">
                    <span>SL:</span>
                    <div class="quantity-details">' . $listCart[$i]['GH_SLSanPhamGioHang'] . '</div>
                </div>
            </div>
        </div>
        ';
    }
    return $output;
}

if (isset($_POST['addCart']) && $_POST['addCart'] === 'add-cart') {
    if (isset($_POST['idProduct']) && isset($_POST['idCustomer'])) {
        $error = '';
        $idProduct = $_POST['idProduct'];
        $idCustomer = $_POST['idCustomer'];
        $CartClass->setGH_IDSanPham($idProduct);
        $CartClass->setGH_IDKhachHang($idCustomer);
        if (!$CartClass->checkDuplicate()) {
            // Sản phẩm đã có trong giỏ hàng, tăng số lượng lên 1
            $quantity = $CartClass->getQuantityByID()['GH_SLSanPhamGioHang'];
            $CartClass->setGH_SLSanPhamGioHang(intval($quantity) + 1);
            if (!$CartClass->updateQuantityCart()) {
                $error = 'errorUpdate';
            }
        } else {
            // Sản phẩm chưa có trong giỏ hàng, thêm mới với số lượng 1
            $CartClass->setGH_SLSanPhamGioHang("1");
            if (!$CartClass->insertCart()) {
                $error = 'errorInsert';
            }
        }
        if ($error === '') {
            echo 'success';
        } else {
            echo $error;
        }
    }
}

if (isset($_POST['addCartDetals']) && $_POST['addCartDetals'] === 'add-cart-details') {
    if (isset($_POST['idProductDetals']) && isset($_POST['idCustomerDetals']) && isset($_POST['quantityCartDetals'])) {
        $error = '';
        $idProduct = $_POST['idProductDetals'];
        $idCustomer = $_POST['idCustomerDetals'];
        $_POST['quantityCartDetals'] === '' ? $quantityCart = 1 : $quantityCart = $_POST['quantityCartDetals'];
        $CartClass->setGH_IDSanPham($idProduct);
        $CartClass->setGH_IDKhachHang($idCustomer);
        if (!$CartClass->checkDuplicate()) {
            // Sản phẩm đã có trong giỏ hàng, cập nhật số lượng mới
            $quantity = $CartClass->getQuantityByID()['GH_SLSanPhamGioHang'];
            $CartClass->setGH_SLSanPhamGioHang(intval($quantity) + intval($quantityCart));
            if (!$CartClass->updateQuantityCart()) {
                $error = 'errorUpdate';
            }
        } else {
            // Sản phẩm chưa có trong giỏ hàng, thêm mới với số lượng quantityCart
            $CartClass->setGH_SLSanPhamGioHang($quantityCart);
            if (!$CartClass->insertCart()) {
                $error = 'errorInsert';
            }
        }
        if ($error === '') {
            echo 'success';
        } else {
            echo $error;
        }
    }
}


if (isset($_POST['fetchCart']) && $_POST['fetchCart'] === 'fetch-cart') {
    echo fetchCart();
}

if (isset($_POST['fetchQuantityCart']) && $_POST['fetchQuantityCart'] === 'fetch-quantity-cart') {
    $CartClass = new Cart;
    $CustomerClass = new Customer;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $customerID = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $CartClass->setGH_IDKhachHang($customerID);
    echo $CartClass->countProductCart();
}

if (isset($_POST['deleteCart']) && $_POST['deleteCart'] === 'delete-cart') {
    if (isset($_POST['idProduct'])) {
        $CartClass = new Cart;
        $CustomerClass = new Customer;
        $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
        $productID = $_POST['idProduct'];
        $customerID = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
        $CartClass->setGH_IDSanPham($productID);
        $CartClass->setGH_IDKhachHang($customerID);
        if ($CartClass->deleteProductCart()) {
            echo 'success';
        } else {
            echo 'failed';
        }
    }
}

if (isset($_POST['flagUpdateQuantity']) && $_POST['flagUpdateQuantity'] === 'update-quantity') {
    if (isset($_POST['idProduct']) && isset($_POST['quantityCart'])) {
        $productID = $_POST['idProduct'];
        $_POST['quantityCart'] !== '' ? $quantityCart = $_POST['quantityCart'] : $quantityCart = '1';
        $CartClass = new Cart;
        $CustomerClass = new Customer;
        $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
        $customerID = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
        $CartClass->setGH_IDSanPham($productID);
        $CartClass->setGH_IDKhachHang($customerID);
        $CartClass->setGH_SLSanPhamGioHang($quantityCart);
        if ($CartClass->updateQuantityCart()) {
            echo 'success';
        } else {
            echo 'failed';
        }
    }
}

function fetchPhoneAddress()
{
    $CustomerClass = new Customer;
    $CustomerClass->setKH_EmailKhachHang($_SESSION['email']);
    $KH_IDKhachHang = $CustomerClass->selectCustomerByEmail()['KH_IDKhachHang'];
    $KH_SDTKhachHang = $CustomerClass->selectCustomerByEmail()['KH_SDTKhachHang'];
    $KH_DiaChiKhachHang = $CustomerClass->selectCustomerByEmail()['KH_DiaChiKhachHang'];
    if ($KH_SDTKhachHang === 'Chưa cập nhật' || $KH_DiaChiKhachHang === 'Chưa cập nhật') {
        return '
        <div class="content-checkout">
            <div class="content-info">
                <div class="content-info-logo">
                    <i class="bx bx-error-alt"></i>
                </div>
                <div class="content-info-details">Vui lòng cập nhật địa chỉ và số điện thoại</div>
            </div>
            <div class="content-address">
                <div class="content-address-details">
                    <div class="Update__Info__Input__Box">
                        <label for="updateAddressCart">Địa chỉ:</label>
                        <input type="text" value="' . $KH_DiaChiKhachHang . '" id="updateAddressCart">
                        <div class="Update__Info__Error">Địa chỉ phải trên 4 ký tự</div>
                    </div> 
                    <div class="Update__Info__Input__Box">
                        <label for="updatePhoneCart">Số điện thoại:</label>
                        <input type="text" value="' . $KH_SDTKhachHang . '" id="updatePhoneCart">
                        <div class="Update__Info__Error">Số điện thoại không hợp lệ</div>
                    </div> 
                    <div id="updateInfoCartSubmit" value="' . $KH_IDKhachHang . '">Cập Nhật</div>
                </div>
            </div>
        </div>
        ';
    } else {
        return '';
    }
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

if (isset($_POST['createBill']) && $_POST['createBill'] === 'create-bill') {
    $arrayResponse = [
        fetchCartBill(),
        number_format(getPriceBill()[0]),
        number_format(getPriceBill()[1]),
        getPriceBill()[2],
        getPriceBill()[3],
        number_format(getPriceBill()[4]),
        fetchPhoneAddress()
    ];
    print_r(json_encode($arrayResponse));
}
