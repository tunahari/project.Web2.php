<?php
// Đặt session_start() lên đầu tiên, trước mọi output HTML
@session_start(); // Sử dụng @ để tạm thời ẩn cảnh báo nếu session đã được start ở file khác, nhưng tốt nhất là đảm bảo nó chỉ được gọi một lần.

// Kiểm tra xem HandlingFunctions đã được include chưa (nếu cần thiết ở đây)
// include_once '../../path/to/HandlingFunctions.php'; // Ví dụ đường dẫn
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Thống Kê</title>
    <link rel="stylesheet" href="./main-admin.css">
    <link rel="stylesheet" href="./style-admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="./main-admin.css">


</head>

<body>
    <div class="main">
        <?php
        include './include.header.php';
        ?>
        <div class="containers">
            <?php
            include './include.menu.php';
            ?>

            <!-- ===== BẮT ĐẦU NỘI DUNG THỐNG KÊ ===== -->
            <h1 class="dashboard-title">Bảng Thống Kê</h1>
            <div class="dashboard-content list__table--container">
                

                <?php
                // 1. Kết nối cơ sở dữ liệu
                require_once '../../Model/database/connectDataBase.php';
                if (!class_exists('ConnectDataBase')) {
                    die("<p style='color: red;'>Lỗi nghiêm trọng: Không tìm thấy lớp ConnectDataBase.</p>");
                }
                $ConnectDataBase = new ConnectDataBase();
                $conn = $ConnectDataBase->connectDB();

                // Khởi tạo các biến thống kê
                $totalRevenue = 0;
                $completedOrdersCount = 0;
                $pendingOrdersCount = 0;
                $processedOrdersCount = 0; // Thêm biến đếm đơn hàng đã xử lý
                $cancelledOrdersCount = 0;
                $customerCount = 0;
                $activeProductCount = 0;

                // --- CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG THEO DATABASE CỦA BẠN ---
                define('ORDER_STATUS_COMPLETED', 2);   // Hoàn thành
                define('ORDER_STATUS_PENDING', 4);     // Đang xử lý (Tính là đang chờ)
                define('ORDER_STATUS_PROCESSED', 3);   // Đã xử lý (MỚI)
                define('ORDER_STATUS_CANCELLED', 1);   // Đã hủy
                // --- KẾT THÚC CẬP NHẬT TRẠNG THÁI ---

                try {
                    // --- Truy vấn Tổng Doanh Thu (Hoàn thành - status = 2) ---
                    $sqlRevenue = "
                        SELECT
                            COALESCE(SUM(bd.CTDH_SLSanPham * (CAST(p.SP_GiaBanSanPham AS DECIMAL(15,2)) * (1 - CAST(p.SP_GiamGiaSanPham AS DECIMAL(5,2)) / 100))), 0) as totalRevenue
                        FROM
                            bill AS b
                        JOIN
                            billdetails AS bd ON b.DH_IDDonHang = CAST(bd.CTDH_IDDonHang AS UNSIGNED)
                        JOIN
                            product AS p ON CAST(bd.CTDH_IDSanPham AS UNSIGNED) = p.SP_IDSanPham
                        WHERE
                            b.DH_TrangThaiDonHang = :status_completed;
                    ";
                    $stmtRevenue = $conn->prepare($sqlRevenue);
                    $status_completed_val = ORDER_STATUS_COMPLETED;
                    $stmtRevenue->bindParam(':status_completed', $status_completed_val, PDO::PARAM_INT);
                    $stmtRevenue->execute();
                    $resultRevenue = $stmtRevenue->fetch(PDO::FETCH_ASSOC);
                    if ($resultRevenue && isset($resultRevenue['totalRevenue'])) {
                        $totalRevenue = (float)$resultRevenue['totalRevenue'];
                    } else {
                        $totalRevenue = 0;
                    }

                    // --- Truy vấn Số lượng đơn hàng hoàn thành (status = 2) ---
                    $sqlCompletedOrders = "SELECT COUNT(*) as completedOrders FROM bill WHERE DH_TrangThaiDonHang = :status_completed";
                    $stmtCompletedOrders = $conn->prepare($sqlCompletedOrders);
                    $stmtCompletedOrders->bindParam(':status_completed', $status_completed_val, PDO::PARAM_INT); // Dùng lại biến $status_completed_val
                    $stmtCompletedOrders->execute();
                    $resultCompletedOrders = $stmtCompletedOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultCompletedOrders) {
                        $completedOrdersCount = $resultCompletedOrders['completedOrders'];
                    }

                    // --- Truy vấn Số lượng đơn hàng đang chờ (Đang xử lý - status = 4) ---
                    $sqlPendingOrders = "SELECT COUNT(*) as pendingOrders FROM bill WHERE DH_TrangThaiDonHang = :status_pending";
                    $stmtPendingOrders = $conn->prepare($sqlPendingOrders);
                    $status_pending_val = ORDER_STATUS_PENDING;
                    $stmtPendingOrders->bindParam(':status_pending', $status_pending_val, PDO::PARAM_INT);
                    $stmtPendingOrders->execute();
                    $resultPendingOrders = $stmtPendingOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultPendingOrders) {
                        $pendingOrdersCount = $resultPendingOrders['pendingOrders'];
                    }

                    // --- Truy vấn Số lượng đơn hàng đã xử lý (status = 3) - MỚI THÊM ---
                    $sqlProcessedOrders = "SELECT COUNT(*) as processedOrders FROM bill WHERE DH_TrangThaiDonHang = :status_processed";
                    $stmtProcessedOrders = $conn->prepare($sqlProcessedOrders);
                    $status_processed_val = ORDER_STATUS_PROCESSED;
                    $stmtProcessedOrders->bindParam(':status_processed', $status_processed_val, PDO::PARAM_INT);
                    $stmtProcessedOrders->execute();
                    $resultProcessedOrders = $stmtProcessedOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultProcessedOrders) {
                        $processedOrdersCount = $resultProcessedOrders['processedOrders'];
                    }
                    // --- KẾT THÚC TRUY VẤN ĐƠN ĐÃ XỬ LÝ ---

                    // --- Truy vấn Số lượng đơn hàng đã hủy (status = 1) ---
                    $sqlCancelledOrders = "SELECT COUNT(*) as cancelledOrders FROM bill WHERE DH_TrangThaiDonHang = :status_cancelled";
                    $stmtCancelledOrders = $conn->prepare($sqlCancelledOrders);
                    $status_cancelled_val = ORDER_STATUS_CANCELLED;
                    $stmtCancelledOrders->bindParam(':status_cancelled', $status_cancelled_val, PDO::PARAM_INT);
                    $stmtCancelledOrders->execute();
                    $resultCancelledOrders = $stmtCancelledOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultCancelledOrders) {
                        $cancelledOrdersCount = $resultCancelledOrders['cancelledOrders'];
                    }

                    // --- Truy vấn Tổng số khách hàng ---
                    $sqlCustomers = "SELECT COUNT(*) as totalCustomers FROM customer WHERE KH_XoaKhachHang = :status_not_deleted";
                    $stmtCustomers = $conn->prepare($sqlCustomers);
                    $status_not_deleted = 'No';
                    $stmtCustomers->bindParam(':status_not_deleted', $status_not_deleted, PDO::PARAM_STR);
                    $stmtCustomers->execute();
                    $resultCustomers = $stmtCustomers->fetch(PDO::FETCH_ASSOC);
                    if ($resultCustomers) {
                        $customerCount = $resultCustomers['totalCustomers'];
                    }

                    // --- Truy vấn Tổng số sản phẩm ---
                    $sqlProducts = "SELECT COUNT(*) as totalProducts FROM product WHERE SP_XoaSanPham = :status_not_deleted";
                    $stmtProducts = $conn->prepare($sqlProducts);
                    $stmtProducts->bindParam(':status_not_deleted', $status_not_deleted, PDO::PARAM_STR); // Dùng lại biến $status_not_deleted
                    $stmtProducts->execute();
                    $resultProducts = $stmtProducts->fetch(PDO::FETCH_ASSOC);
                    if ($resultProducts) {
                        $activeProductCount = $resultProducts['totalProducts'];
                    }
                } catch (PDOException $e) {
                    error_log("Lỗi CSDL trong main-admin.php: " . $e->getMessage());
                    echo "<p style='color: red;'>Đã xảy ra lỗi khi truy vấn dữ liệu. Vui lòng thử lại sau.</p>";
                    // In ra lỗi SQL nếu có để debug
                    if (isset($stmtRevenue)) {
                        echo "<pre>Lỗi SQL Doanh thu: ";
                        print_r($stmtRevenue->errorInfo());
                        echo "</pre>";
                    }
                    if (isset($stmtCompletedOrders)) {
                        echo "<pre>Lỗi SQL Đơn hoàn thành: ";
                        print_r($stmtCompletedOrders->errorInfo());
                        echo "</pre>";
                    }
                    if (isset($stmtPendingOrders)) {
                        echo "<pre>Lỗi SQL Đơn chờ: ";
                        print_r($stmtPendingOrders->errorInfo());
                        echo "</pre>";
                    }
                    if (isset($stmtProcessedOrders)) {
                        echo "<pre>Lỗi SQL Đơn đã xử lý: ";
                        print_r($stmtProcessedOrders->errorInfo());
                        echo "</pre>";
                    } // Debug lỗi mới
                    if (isset($stmtCancelledOrders)) {
                        echo "<pre>Lỗi SQL Đơn hủy: ";
                        print_r($stmtCancelledOrders->errorInfo());
                        echo "</pre>";
                    }
                    if (isset($stmtCustomers)) {
                        echo "<pre>Lỗi SQL Khách hàng: ";
                        print_r($stmtCustomers->errorInfo());
                        echo "</pre>";
                    }
                    if (isset($stmtProducts)) {
                        echo "<pre>Lỗi SQL Sản phẩm: ";
                        print_r($stmtProducts->errorInfo());
                        echo "</pre>";
                    }
                } finally {
                    $conn = null;
                }

                ?>

                <!-- Hiển thị các thẻ thống kê -->
                <div class="stats-container">
                    <!-- Thẻ Doanh thu -->
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class='bx bx-dollar-circle'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Tổng Doanh Thu (Hoàn thành)</h4>
                            <p><?php echo htmlspecialchars(number_format($totalRevenue, 0, ',', '.')); ?> VNĐ</p>
                        </div>
                    </div>

                    <!-- Thẻ Đơn hàng hoàn thành -->
                    <div class="stat-card">
                        <div class="stat-icon orders-completed">
                            <i class='bx bx-check-circle'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Đơn Hàng Hoàn Thành</h4>
                            <p><?php echo htmlspecialchars($completedOrdersCount); ?></p>
                        </div>
                    </div>

                    <!-- Thẻ Đơn hàng đang chờ (Đang xử lý) -->
                    <div class="stat-card">
                        <div class="stat-icon orders-pending">
                            <i class='bx bx-loader-circle'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Đơn Hàng Đang Chờ (Đang xử lý)</h4>
                            <p><?php echo htmlspecialchars($pendingOrdersCount); ?></p>
                        </div>
                    </div>

                    <!-- Thẻ Đơn hàng đã xử lý - MỚI THÊM -->
                    <div class="stat-card">
                        <div class="stat-icon orders-processed">
                            <i class='bx bx-cog'></i> <!-- Icon bánh răng -->
                        </div>
                        <div class="stat-info">
                            <h4>Đơn Hàng Đã Xử Lý</h4>
                            <p><?php echo htmlspecialchars($processedOrdersCount); ?></p>
                        </div>
                    </div>
                    <!-- KẾT THÚC THẺ ĐƠN ĐÃ XỬ LÝ -->

                    <!-- Thẻ Đơn hàng đã hủy -->
                    <div class="stat-card">
                        <div class="stat-icon orders-cancelled">
                            <i class='bx bx-x-circle'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Đơn Hàng Đã Hủy</h4>
                            <p><?php echo htmlspecialchars($cancelledOrdersCount); ?></p>
                        </div>
                    </div>

                    <!-- Thẻ Tổng khách hàng -->
                    <div class="stat-card">
                        <div class="stat-icon customers">
                            <i class='bx bx-user'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Tổng Khách Hàng</h4>
                            <p><?php echo htmlspecialchars($customerCount); ?></p>
                        </div>
                    </div>

                    <!-- Thẻ Tổng sản phẩm -->
                    <div class="stat-card">
                        <div class="stat-icon products">
                            <i class='bx bx-package'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Tổng Sản Phẩm (Hoạt động)</h4>
                            <p><?php echo htmlspecialchars($activeProductCount); ?></p>
                        </div>
                    </div>
                </div> <!-- Kết thúc .stats-container -->

                <!-- Khu vực cho biểu đồ hoặc bảng chi tiết (Tùy chọn) -->
                <!-- Phần comment giữ nguyên -->

            </div>
            <!-- ===== KẾT THÚC NỘI DUNG THỐNG KÊ ===== -->

        </div> <!-- Kết thúc .containers -->
    </div> <!-- Kết thúc .main -->

    <!-- Các script hiện có -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="./chart-admin.js"></script>
    <script src="./script-admin.js"></script>
    <!-- <script src="./test-admin.js"></script> -->

    <!-- Script kiểm tra quyền và các script khác của trang -->
    <script>
        $(function() {
            if (typeof HandlingFunctions !== 'function') {
                console.error("Lỗi: Lớp HandlingFunctions không được tìm thấy.");
                return;
            }

            const Fuction = new HandlingFunctions(); // Sửa tên biến

            function checkPower() {
                if (typeof Fuction.getAjaxPost !== 'function') { // Sửa tên biến
                    console.error("Lỗi: Phương thức getAjaxPost không tồn tại.");
                    return;
                }

                Fuction.getAjaxPost('../../Controller/admin/controller-admin.checkpower.php', { // Sửa tên biến
                    checkPower: 'check-power'
                }).done(function(response) {
                    const trimmedResponse = response.trim();
                    if (trimmedResponse === '-1' || trimmedResponse === '0') {
                        console.log("Quyền không hợp lệ, đang đăng xuất...");
                        if (typeof Fuction.getAjaxPost !== 'function') { // Sửa tên biến
                            console.error("Lỗi: Phương thức getAjaxPost không tồn tại (logout).");
                            return;
                        }
                        Fuction.getAjaxPost('../../Controller/admin/controller-admin.logout.php', { // Sửa tên biến
                            logoutAdmin: 'log-out-admin'
                        }).done(function(logoutResponse) {
                            if (logoutResponse.trim() === 'logout-success') {
                                console.log("Đăng xuất thành công.");
                                window.location.href = './login-admin.php';
                            } else {
                                console.warn("Phản hồi đăng xuất không mong đợi:", logoutResponse);
                            }
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            console.error("Lỗi AJAX khi đăng xuất:", textStatus, errorThrown, jqXHR.responseText);
                            alert("Lỗi đăng xuất.");
                        });
                    } else {
                        // console.log("Kiểm tra quyền thành công:", trimmedResponse); // Bỏ comment nếu cần debug
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Lỗi AJAX khi kiểm tra quyền:", textStatus, errorThrown, jqXHR.responseText);
                    alert("Không thể kiểm tra quyền người dùng.");
                });
            }

            checkPower();

            // === JAVASCRIPT CHO TRANG THỐNG KÊ (NẾU CẦN) ===
            // Phần comment giữ nguyên
        });
    </script>
</body>

</html>