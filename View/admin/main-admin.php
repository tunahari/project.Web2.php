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
    <!-- Link CSS gốc của bạn -->
    <link rel="stylesheet" href="./main-admin.css">
    <link rel="stylesheet" href="./style-admin.css">

    <link href='../include/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../include/fontawesome/css/all.min.css" />
    <script src="../include/jquery.min.js"></script>
    <script src="../include/fontawesome/js/all.min.js "></script>

    <link rel="stylesheet" href="../include/swiper-bundle.min.css" />

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <!-- Không cần link main-admin.css lần nữa nếu đã link ở trên -->
    <!-- <link rel="stylesheet" href="./main-admin.css"> -->

    <style>
        /* CSS cho form lọc và danh sách top khách hàng (Phần mới) */
        .filter-section {
            /* Bọc form và kết quả lại */
            margin-top: 20px;
            margin-bottom: 50px;
            /* Tạo khoảng cách với .stats-container ở trên */
            padding: 0 15px;
            /* Thêm padding nếu cần */
            width: 100%;
            /* Đảm bảo chiếm đủ chiều rộng */
            display: flex;
            /* Sắp xếp các phần tử con */
            flex-direction: column;
            /* Sắp xếp theo chiều dọc */
            align-items: center;
            /* Căn giữa nội dung nếu muốn */
        }

        .filter-form {
            color:#333;
            background-color: #f9f9f9;
            padding: 15px 20px;
            /* Tăng padding ngang */
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
            /* Khoảng cách giữa các element */
            flex-wrap: wrap;
            /* Cho phép xuống dòng nếu không đủ chỗ */
            justify-content: center;
        }

        .filter-form label {
            font-weight: bold;
            margin-right: 5px;
            white-space: nowrap;
            /* Không xuống dòng label */
        }

        .filter-form input[type="date"] {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .filter-form button,
        .filter-form .reset-button {
            /* Style chung cho nút */
            padding: 9px 15px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            /* Cho thẻ <a> */
            display: inline-block;
            /* Cho thẻ <a> */
            text-align: center;
        }

        .filter-form button {
            /* Nút Lọc */
            background-color: #007bff;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        .filter-form .reset-button {
            /* Nút Reset */
            background-color: #6c757d;
            /* Màu xám */
        }

        .filter-form .reset-button:hover {
            background-color: #5a6268;
        }


        .top-customers-section {
            margin-top: 20px;
            /* Giảm margin top vì đã có margin ở .filter-section */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 90%;

            /* margin-left: auto; */
            /* Căn giữa nếu cần */
            /* margin-right: auto; */
            min-height: 150px; /* Đảm bảo có chiều cao tối thiểu ngay cả khi rỗng */
            display: flex; /* Sử dụng flex để dễ căn chỉnh nội dung bên trong */
            flex-direction: column; /* Sắp xếp tiêu đề và danh sách theo chiều dọc */
            position: relative; /* Cần cho loading overlay */
        }

        .top-customers-section h3 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            text-align: center;
            /* Căn giữa tiêu đề */
        }

        .top-customers-list {
            list-style: none;
            padding: 0;
            flex-grow: 1; /* Cho phép danh sách chiếm không gian còn lại */
        }

        .top-customers-list li {
            padding: 10px 80px;
            border-bottom: 1px dashed #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-customers-list li:last-child {
            border-bottom: none;
        }

        .top-customers-list .customer-rank {
            min-width: 25px;
            /* Đảm bảo số thứ tự thẳng hàng */
            text-align: right;
            margin-right: 10px;
            color: #3e3a3a;
        }

        .top-customers-list .customer-name {
            padding-left: 15px;
            font-weight: 500;
            color: #3e3a3a;
            flex-grow: 1;
            /* Cho tên chiếm phần lớn không gian */
            margin-right: 15px;
        }

        .top-customers-list .customer-spent {
            font-weight: bold;
            color: #007bff;
            white-space: nowrap;
            /* Không xuống dòng tiền */
        }

        .no-data {
            color: #777;
            font-style: italic;
            text-align: center;
            /* Căn giữa thông báo */
            padding: 15px 0;
            margin: auto; /* Căn giữa thông báo khi list rỗng */
        }

        /* Thêm style cho trạng thái loading (tùy chọn) */
        .top-customers-section.loading .top-customers-list {
            opacity: 0.5; /* Làm mờ danh sách cũ */
        }
        .top-customers-section .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-style: italic;
            color: #555;
            z-index: 10;
            border-radius: 8px; /* Bo góc giống section */
        }

    </style>

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

            <div class="dashboard-content list__table--container">
                <h1 class="dashboard-title">Bảng Thống Kê</h1>

                <?php
                // ============================================================
                // ===== PHẦN CODE PHP HIỆN TẠI CỦA BẠN (GIỮ NGUYÊN) =====
                // ============================================================
                require_once '../../Model/database/connectDataBase.php';
                if (!class_exists('ConnectDataBase')) {
                    die("<p style='color: red;'>Lỗi nghiêm trọng: Không tìm thấy lớp ConnectDataBase.</p>");
                }
                $ConnectDataBase_Original = new ConnectDataBase();
                $conn_original = $ConnectDataBase_Original->connectDB();

                $totalRevenue = 0;
                $completedOrdersCount = 0;
                $pendingOrdersCount = 0;
                $processedOrdersCount = 0;
                $cancelledOrdersCount = 0;
                $customerCount = 0;
                $activeProductCount = 0;

                if (!defined('ORDER_STATUS_COMPLETED')) define('ORDER_STATUS_COMPLETED', 2);
                if (!defined('ORDER_STATUS_PENDING')) define('ORDER_STATUS_PENDING', 4);
                if (!defined('ORDER_STATUS_PROCESSED')) define('ORDER_STATUS_PROCESSED', 3);
                if (!defined('ORDER_STATUS_CANCELLED')) define('ORDER_STATUS_CANCELLED', 1);

                try {
                    // --- Các truy vấn thống kê gốc của bạn ---
                    // (Giữ nguyên các truy vấn: $sqlRevenue, $sqlCompletedOrders, ...)
                    // --- Ví dụ một truy vấn ---
                     $sqlRevenue = "
                        SELECT
                           COALESCE(SUM(bd.CTDH_SLSanPham * CAST(p.SP_GiaBanSanPham AS DECIMAL(15,2))), 0) AS totalRevenue
                        FROM bill AS b
                        JOIN billdetails AS bd ON CAST(b.DH_IDDonHang AS UNSIGNED) = CAST(bd.CTDH_IDDonHang AS UNSIGNED)
                        JOIN product AS p ON CAST(bd.CTDH_IDSanPham AS UNSIGNED) = p.SP_IDSanPham
                        WHERE b.DH_TrangThaiDonHang = :status_completed;";
                    $stmtRevenue = $conn_original->prepare($sqlRevenue);
                    $status_completed_val = ORDER_STATUS_COMPLETED;
                    $stmtRevenue->bindParam(':status_completed', $status_completed_val, PDO::PARAM_INT);
                    $stmtRevenue->execute();
                    $resultRevenue = $stmtRevenue->fetch(PDO::FETCH_ASSOC);
                    if ($resultRevenue && isset($resultRevenue['totalRevenue'])) {
                        $totalRevenue = (float)$resultRevenue['totalRevenue'];
                    }
                    // ... (Các truy vấn khác giữ nguyên) ...
                     // --- Truy vấn Số lượng đơn hàng hoàn thành (status = 2) --- (Đã có)
                    $sqlCompletedOrders = "SELECT COUNT(*) as completedOrders FROM bill WHERE DH_TrangThaiDonHang = :status_completed";
                    $stmtCompletedOrders = $conn_original->prepare($sqlCompletedOrders);
                    $stmtCompletedOrders->bindParam(':status_completed', $status_completed_val, PDO::PARAM_INT); // Dùng lại biến $status_completed_val
                    $stmtCompletedOrders->execute();
                    $resultCompletedOrders = $stmtCompletedOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultCompletedOrders) {
                        $completedOrdersCount = $resultCompletedOrders['completedOrders'];
                    }

                    // --- Truy vấn Số lượng đơn hàng đang chờ (Đang xử lý - status = 4) --- (Đã có)
                    $sqlPendingOrders = "SELECT COUNT(*) as pendingOrders FROM bill WHERE DH_TrangThaiDonHang = :status_pending";
                    $stmtPendingOrders = $conn_original->prepare($sqlPendingOrders);
                    $status_pending_val = ORDER_STATUS_PENDING;
                    $stmtPendingOrders->bindParam(':status_pending', $status_pending_val, PDO::PARAM_INT);
                    $stmtPendingOrders->execute();
                    $resultPendingOrders = $stmtPendingOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultPendingOrders) {
                        $pendingOrdersCount = $resultPendingOrders['pendingOrders'];
                    }

                    // --- Truy vấn Số lượng đơn hàng đã xử lý (status = 3) - MỚI THÊM --- (Đã có)
                    $sqlProcessedOrders = "SELECT COUNT(*) as processedOrders FROM bill WHERE DH_TrangThaiDonHang = :status_processed";
                    $stmtProcessedOrders = $conn_original->prepare($sqlProcessedOrders);
                    $status_processed_val = ORDER_STATUS_PROCESSED;
                    $stmtProcessedOrders->bindParam(':status_processed', $status_processed_val, PDO::PARAM_INT);
                    $stmtProcessedOrders->execute();
                    $resultProcessedOrders = $stmtProcessedOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultProcessedOrders) {
                        $processedOrdersCount = $resultProcessedOrders['processedOrders'];
                    }
                    // --- KẾT THÚC TRUY VẤN ĐƠN ĐÃ XỬ LÝ ---

                    // --- Truy vấn Số lượng đơn hàng đã hủy (status = 1) --- (Đã có)
                    $sqlCancelledOrders = "SELECT COUNT(*) as cancelledOrders FROM bill WHERE DH_TrangThaiDonHang = :status_cancelled";
                    $stmtCancelledOrders = $conn_original->prepare($sqlCancelledOrders);
                    $status_cancelled_val = ORDER_STATUS_CANCELLED;
                    $stmtCancelledOrders->bindParam(':status_cancelled', $status_cancelled_val, PDO::PARAM_INT);
                    $stmtCancelledOrders->execute();
                    $resultCancelledOrders = $stmtCancelledOrders->fetch(PDO::FETCH_ASSOC);
                    if ($resultCancelledOrders) {
                        $cancelledOrdersCount = $resultCancelledOrders['cancelledOrders'];
                    }

                    // --- Truy vấn Tổng số khách hàng --- (Đã có)
                    $sqlCustomers = "SELECT COUNT(*) as totalCustomers FROM customer WHERE KH_XoaKhachHang = :status_not_deleted";
                    $stmtCustomers = $conn_original->prepare($sqlCustomers);
                    $status_not_deleted = 'No';
                    $stmtCustomers->bindParam(':status_not_deleted', $status_not_deleted, PDO::PARAM_STR);
                    $stmtCustomers->execute();
                    $resultCustomers = $stmtCustomers->fetch(PDO::FETCH_ASSOC);
                    if ($resultCustomers) {
                        $customerCount = $resultCustomers['totalCustomers'];
                    }

                    // --- Truy vấn Tổng số sản phẩm --- (Đã có)
                    $sqlProducts = "SELECT COUNT(*) as totalProducts FROM product WHERE SP_XoaSanPham = :status_not_deleted";
                    $stmtProducts = $conn_original->prepare($sqlProducts);
                    $stmtProducts->bindParam(':status_not_deleted', $status_not_deleted, PDO::PARAM_STR); // Dùng lại biến $status_not_deleted
                    $stmtProducts->execute();
                    $resultProducts = $stmtProducts->fetch(PDO::FETCH_ASSOC);
                    if ($resultProducts) {
                        $activeProductCount = $resultProducts['totalProducts'];
                    }

                } catch (PDOException $e) {
                    error_log("Lỗi CSDL trong phần thống kê gốc (main-admin.php): " . $e->getMessage());
                    echo "<p style='color: red;'>Đã xảy ra lỗi khi truy vấn dữ liệu thống kê gốc. Vui lòng thử lại sau.</p>";
                    // Phần debug lỗi gốc giữ nguyên
                    // ... (giữ nguyên các khối echo "<pre>Lỗi SQL...")
                } finally {
                     $conn_original = null; // Đóng kết nối sau khi lấy xong thống kê gốc
                }
                // ============================================================
                // ===== KẾT THÚC PHẦN CODE PHP HIỆN TẠI CỦA BẠN =====
                // ============================================================
                ?>

                <!-- Hiển thị các thẻ thống kê (Đã có - Phần này giữ nguyên giao diện của bạn) -->
                <div class="stats-container">
                    <!-- Thẻ Doanh thu -->
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class='bx bx-dollar-circle'></i>
                        </div>
                        <div class="stat-info">
                            <h4>Tổng Doanh Thu <br><span>(Hoàn thành)</span></h4>
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
                            <h4>Đơn Hàng Đang Chờ <br> <span>(Đang xử lý)</span></span></h4>
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
                    <!-- ... (Các thẻ stat-card khác giữ nguyên) ... -->
                </div> <!-- Kết thúc .stats-container -->


                <!-- ============================================================ -->
                <!-- ===== PHẦN LỌC VÀ HIỂN THỊ TOP KHÁCH HÀNG (AJAX) ===== -->
                <!-- ============================================================ -->

                <div class="filter-section">

                    <!-- Khu vực hiển thị Top 5 Khách Hàng (Luôn hiển thị) -->
                    <!-- Xóa bỏ các thẻ PHP điều kiện hiển thị -->
                    <div id="top-customers-section" class="top-customers-section">
                         <!-- Form lọc theo ngày (Không cần action vì xử lý bằng JS) -->
                    <form id="filter-top-customers-form" class="filter-form">
                        <label for="start_date">Lọc từ ngày:</label>
                        <input type="date" id="start_date" name="start_date" value=""> <!-- Bỏ value PHP ban đầu -->

                        <label for="end_date">Đến ngày:</label>
                        <input type="date" id="end_date" name="end_date" value=""> <!-- Bỏ value PHP ban đầu -->

                        <button type="submit" id="filter-button">Lọc Top Khách Hàng</button>
                        <!-- Nút Reset Lọc (Dùng button type="button" để JS xử lý) -->
                        <button type="button" id="reset-button" class="reset-button">Reset Lọc</button>
                    </form>
                        <h3 id="top-customers-title">Top 5 Khách Hàng Mua Nhiều Nhất</h3> <!-- Tiêu đề mặc định -->
                        <ol id="top-customers-list" class="top-customers-list">
                            <!-- Nội dung sẽ được cập nhật bằng AJAX -->
                            <p class="no-data">Chọn khoảng thời gian để lọc top khách hàng.</p> <!-- Thông báo ban đầu -->
                        </ol>
                         <!-- Loading Overlay (Ẩn ban đầu) -->
                        <div class="loading-overlay" style="display: none;">Đang tải...</div>
                    </div>

                </div> <!-- Kết thúc .filter-section -->

                <!-- ============================================================ -->
                <!-- ===== KẾT THÚC PHẦN CODE MỚI ===== -->
                <!-- ============================================================ -->


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

    <!-- Script kiểm tra quyền và các script khác của trang (Giữ nguyên) -->
    <script>
        $(function() {
            // Phần kiểm tra quyền của bạn giữ nguyên
            // ... (code checkPower giữ nguyên) ...
            if (typeof HandlingFunctions !== 'function') {
                console.error("Lỗi: Lớp HandlingFunctions không được tìm thấy.");
            }
            const Fuction = typeof HandlingFunctions === 'function' ? new HandlingFunctions() : null;

            function checkPower() { /* ... code checkPower giữ nguyên ... */ }
            if (Fuction) {
                checkPower();
            }


            // === JAVASCRIPT MỚI CHO LỌC TOP KHÁCH HÀNG BẰNG AJAX ===

            const topCustomersSection = $('#top-customers-section');
            const topCustomersList = $('#top-customers-list');
            const topCustomersTitle = $('#top-customers-title');
            const loadingOverlay = topCustomersSection.find('.loading-overlay');
            const filterForm = $('#filter-top-customers-form');
            const startDateInput = $('#start_date');
            const endDateInput = $('#end_date');
            const filterButton = $('#filter-button'); // Thêm ID cho nút lọc
            const resetButton = $('#reset-button');

            // Hàm để cập nhật danh sách top khách hàng
            function updateTopCustomersList(data, startDate, endDate, message) {
                topCustomersList.empty(); // Xóa nội dung cũ

                if (data && data.length > 0) {
                    // *** THAY ĐỔI: Cập nhật tiêu đề linh hoạt hơn ***
                    let titleText = 'Top 5 Khách Hàng Mua Nhiều Nhất';
                    if (startDate && endDate) {
                        titleText += ` (${startDate} - ${endDate})`;
                    } else if (startDate) {
                        titleText += ` (Từ ${startDate})`;
                    } else if (endDate) {
                        titleText += ` (Đến ${endDate})`;
                    }
                    topCustomersTitle.text(titleText);
                    // *** KẾT THÚC THAY ĐỔI TIÊU ĐỀ ***

                    // Thêm các mục mới
                    $.each(data, function(index, customer) {
                        const listItem = `
                            <li>
                                <span class="customer-rank">${index + 1}.</span>
                                <span class="customer-name">${escapeHtml(customer.KH_TenKhachHang)}</span>
                                <span class="customer-spent">${formatCurrency(customer.total_spent)} VNĐ</span>
                            </li>`;
                        topCustomersList.append(listItem);
                    });
                } else {
                    // Hiển thị thông báo không có dữ liệu hoặc thông báo từ server
                    let displayMessage = message || 'Không có dữ liệu khách hàng mua hàng.';
                    // *** THAY ĐỔI: Cập nhật tiêu đề và thông báo khi không có data ***
                    let titleText = 'Top 5 Khách Hàng Mua Nhiều Nhất';
                    if (startDate && endDate) {
                         titleText += ` (${startDate} - ${endDate})`;
                         displayMessage = message || 'Không có dữ liệu khách hàng mua hàng trong khoảng thời gian này.';
                    } else if (startDate) {
                         titleText += ` (Từ ${startDate})`;
                         displayMessage = message || `Không có dữ liệu khách hàng mua hàng từ ngày ${startDate}.`;
                    } else if (endDate) {
                         titleText += ` (Đến ${endDate})`;
                         displayMessage = message || `Không có dữ liệu khách hàng mua hàng đến ngày ${endDate}.`;
                    } else { // Trường hợp reset hoặc không nhập gì
                         displayMessage = 'Chọn khoảng thời gian để lọc top khách hàng.';
                    }
                    topCustomersTitle.text(titleText); // Cập nhật tiêu đề ngay cả khi không có data
                    // *** KẾT THÚC THAY ĐỔI TIÊU ĐỀ/THÔNG BÁO ***
                    topCustomersList.html(`<p class="no-data">${escapeHtml(displayMessage)}</p>`);
                }
            }

            // Hàm để thực hiện AJAX request
            function fetchTopCustomers(startDate = null, endDate = null) {
                // Hiển thị loading
                loadingOverlay.show();
                topCustomersSection.addClass('loading');

                // Đường dẫn tới file PHP xử lý AJAX
                // Điều chỉnh nếu bạn đặt file get_top_customers.php ở chỗ khác
                const ajaxUrl = 'get_top_customers.php';

                $.ajax({
                    url: ajaxUrl,
                    method: 'GET',
                    data: {
                        // Gửi giá trị ngày, có thể là null nếu không nhập
                        start_date: startDate || null, // Đảm bảo gửi null nếu rỗng
                        end_date: endDate || null     // Đảm bảo gửi null nếu rỗng
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            // Truyền cả ngày tháng nhận được từ response vào hàm update
                            updateTopCustomersList(response.data, response.startDate, response.endDate, response.message);
                        } else {
                            // Xử lý lỗi từ server (ví dụ: lỗi CSDL)
                            topCustomersList.html(`<p class="no-data" style="color: red;">Lỗi: ${escapeHtml(response.message || 'Không thể tải dữ liệu.')}</p>`);
                             topCustomersTitle.text('Lỗi Tải Dữ Liệu');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Xử lý lỗi AJAX (ví dụ: mạng, 404)
                        console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                        topCustomersList.html('<p class="no-data" style="color: red;">Lỗi kết nối hoặc máy chủ. Vui lòng thử lại.</p>');
                        topCustomersTitle.text('Lỗi Kết Nối');
                    },
                    complete: function() {
                        // Ẩn loading khi hoàn thành (dù thành công hay lỗi)
                        loadingOverlay.hide();
                        topCustomersSection.removeClass('loading');
                    }
                });
            }

            // Xử lý sự kiện submit form lọc
            filterForm.on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn submit form truyền thống
                const startDate = startDateInput.val();
                const endDate = endDateInput.val();

                // *** THAY ĐỔI: Kiểm tra nếu có ít nhất một ngày được nhập ***
                if (!startDate && !endDate) {
                    alert('Vui lòng chọn ngày bắt đầu và/hoặc ngày kết thúc.');
                    return; // Dừng lại nếu không có ngày nào
                }
                // *** KẾT THÚC THAY ĐỔI KIỂM TRA ***

                 // Kiểm tra nếu có cả hai ngày thì ngày bắt đầu không được lớn hơn ngày kết thúc
                 if (startDate && endDate && startDate > endDate) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc.');
                    return;
                 }

                fetchTopCustomers(startDate, endDate);
            });

            // Xử lý sự kiện click nút Reset
            resetButton.on('click', function() {
                // Xóa giá trị input date
                startDateInput.val('');
                endDateInput.val('');
                // Gọi hàm fetch mà không có ngày tháng (để reset)
                fetchTopCustomers();
            });

             // Hàm tiện ích để escape HTML (tránh XSS)
             function escapeHtml(unsafe) {
                 if (unsafe === null || unsafe === undefined) return '';
                 return String(unsafe) // Đảm bảo là string
                      .replace(/&/g, "&amp;")
                      .replace(/</g, "&lt;")
                      .replace(/>/g, "&gt;")
                      .replace(/"/g, "&quot;")
                      .replace(/'/g, "&#039;");
             }

            // Hàm tiện ích để định dạng tiền tệ
            function formatCurrency(number) {
                 if (isNaN(number)) return '0';
                 // Sử dụng toLocaleString để định dạng số theo chuẩn Việt Nam
                 return parseFloat(number).toLocaleString('vi-VN');
            }

            // Có thể gọi fetchTopCustomers() ngay khi tải trang nếu muốn hiển thị trạng thái mặc định
            // fetchTopCustomers(); // Bỏ comment dòng này nếu muốn tải dữ liệu mặc định (rỗng) khi vào trang

        });
    </script>
</body>

</html>
