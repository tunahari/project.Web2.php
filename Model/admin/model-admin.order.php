<?php
class Order {
    private $DH_IDDonHang, $DH_IDKhachHang, $DH_NgayDatDonHang, $DH_TrangThaiDonHang;

    private $CTDH_IDChiTietDH, $CTDH_IDDonHang, $CTDH_IDSanPham, $CTDH_SLSanPham;

    
    public function setDH_IDDonHang($DH_IDDonHang) {$this->DH_IDDonHang  = $DH_IDDonHang;}
    public function setDH_IDKhachHang($DH_IDKhachHang) {$this->DH_IDKhachHang  = $DH_IDKhachHang;}
    public function setDH_NgayDatDonHang($DH_NgayDatDonHang) {$this->DH_NgayDatDonHang  = $DH_NgayDatDonHang;}
    public function setDH_TrangThaiDonHang($DH_TrangThaiDonHang) {$this->DH_TrangThaiDonHang  = $DH_TrangThaiDonHang;}
    
    public function setCTDH_IDChiTietDH($CTDH_IDChiTietDH) {$this->CTDH_IDChiTietDH  = $CTDH_IDChiTietDH;}
    public function setCTDH_IDDonHang($CTDH_IDDonHang) {$this->CTDH_IDDonHang  = $CTDH_IDDonHang;}
    public function setCTDH_IDSanPham($CTDH_IDSanPham) {$this->CTDH_IDSanPham  = $CTDH_IDSanPham;}
    public function setCTDH_SLSanPham($CTDH_SLSanPham) {$this->CTDH_SLSanPham  = $CTDH_SLSanPham;}

    public function insertBill($diaChiGiaoHang) {
        $ConnectDataBase = new ConnectDataBase();
        $sql = "INSERT INTO bill (DH_IDKhachHang, DH_NgayDatDonHang, DH_TrangThaiDonHang, DH_DiaChiGiaoHang) 
                VALUES (:DH_IDKhachHang, :DH_NgayDatDonHang, :DH_TrangThaiDonHang, :DH_DiaChiGiaoHang)";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->bindParam(':DH_NgayDatDonHang', $this->DH_NgayDatDonHang);
        $stmt->bindParam(':DH_TrangThaiDonHang', $this->DH_TrangThaiDonHang);
        $stmt->bindParam(':DH_DiaChiGiaoHang', $diaChiGiaoHang); // Đảm bảo bạn đã truyền giá trị này vào
        $stmt->execute();
    }
    

    function insertBillDetals () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = 'INSERT INTO billdetails (CTDH_IDDonHang, CTDH_IDSanPham, CTDH_SLSanPham) 
        VALUES (:CTDH_IDDonHang, :CTDH_IDSanPham, :CTDH_SLSanPham)';
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':CTDH_IDDonHang', $this->CTDH_IDDonHang, PDO::PARAM_STR);
        $stmt->bindParam(':CTDH_IDSanPham', $this->CTDH_IDSanPham, PDO::PARAM_STR);
        $stmt->bindParam(':CTDH_SLSanPham', $this->CTDH_SLSanPham, PDO::PARAM_INT);
        if ($stmt->execute()) {return true;} else {return false;}
    }

    function updateBillStatus () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = 'UPDATE bill SET DH_TrangThaiDonHang = :DH_TrangThaiDonHang WHERE DH_IDDonHang  = :DH_IDDonHang 
        AND DH_IDKhachHang = :DH_IDKhachHang ';
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_TrangThaiDonHang', $this->DH_TrangThaiDonHang);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);  
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);  
        if ($stmt->execute()) {return true;} else {return false;}
    }

    function selectBillStatus () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT DH_TrangThaiDonHang FROM bill WHERE DH_IDDonHang = :DH_IDDonHang AND DH_IDKhachHang = :DH_IDKhachHang";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);  
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result[0]['DH_TrangThaiDonHang'];} else {return [];}
    }

    function selectBillNewID () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT DH_IDDonHang FROM bill ORDER BY DH_IDDonHang DESC LIMIT 1 ";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result[0]['DH_IDDonHang'];} else {return null;}
    }

    function selectAllBillByCustomerID () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT * FROM bill WHERE DH_IDKhachHang = :DH_IDKhachHang ORDER BY DH_IDDonHang DESC";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result;} else {return [];}
    }

    // 10% VAT = (SP_GiaBanSanPham * 10 / 100)
    // Giảm Giá = SP_GiaBanSanPham - (SP_GiaBanSanPham * SP_GiamGiaSanPham / 100)
    // Giá sau khi giảm = SP_GiaBanSanPham - (SP_GiaBanSanPham * SP_GiamGiaSanPham / 100)
    // Giá cuối cùng 1 sản phẩm = SP_GiaBanSanPham - (SP_GiaBanSanPham * SP_GiamGiaSanPham / 100) + (SP_GiaBanSanPham * 10 / 100)
    // Giá cuối cùng của nhiều sản phẩm = (SP_GiaBanSanPham - (SP_GiaBanSanPham * SP_GiamGiaSanPham / 100) + (SP_GiaBanSanPham * 10 / 100)) * CTDH_SLSanPham
    // Giá cuối cùng của 1 đơn hàng = SUM((SP_GiaBanSanPham - (SP_GiaBanSanPham * SP_GiamGiaSanPham / 100) + (SP_GiaBanSanPham * 10 / 100)) * CTDH_SLSanPham)

    function priceBill () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT SUM((SP_GiaBanSanPham) * CTDH_SLSanPham) AS PriceBill
        FROM bill JOIN billdetails ON DH_IDDonHang = CTDH_IDDonHang JOIN product ON CTDH_IDSanPham = SP_IDSanPham 
        WHERE DH_IDKhachHang = :DH_IDKhachHang AND DH_IDDonHang = :DH_IDDonHang ";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result[0]['PriceBill'];} else {return null;}
        
    }

    function selectProductBill () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT (SP_GiaBanSanPham) AS GiamGia, SP_IDSanPham, SP_GiaBanSanPham,
        SP_TenSanPham, CTDH_SLSanPham, DH_NgayDatDonHang, SP_TonKhoSanPham,
        SP_Image1SanPham FROM billdetails JOIN product ON CTDH_IDSanPham = SP_IDSanPham JOIN bill ON CTDH_IDDonHang = DH_IDDonHang
        WHERE DH_IDKhachHang = :DH_IDKhachHang AND DH_IDDonHang = :DH_IDDonHang";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result;} else {return [];}
        
    }

    function countItemBill () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT COUNT(*) AS SLMatHang FROM billdetails JOIN bill ON CTDH_IDDonHang = DH_IDDonHang 
        WHERE CTDH_IDDonHang = :DH_IDDonHang AND DH_IDKhachHang = :DH_IDKhachHang";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result[0]['SLMatHang'];} else {return null;}
        
    }

    function countProductBill () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT SUM(CTDH_SLSanPham) AS SLSanPham FROM billdetails JOIN bill ON CTDH_IDDonHang = DH_IDDonHang 
        WHERE CTDH_IDDonHang = :DH_IDDonHang AND DH_IDKhachHang = :DH_IDKhachHang";
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {return $result[0]['SLSanPham'];} else {return [];}
    }

    function selectLimitOrder ($limitOrder, $startOrder, $queryOrder, $sortIDOrder, $sortDateOrder, $sortStatusOrder, $filterStatus = '', $startDate = '', $endDate = '') {
        $ConnectDataBase = new ConnectDataBase;
        $params = [];
        $sql = "SELECT b.*, c.KH_TenKhachHang, c.KH_SDTKhachHang
                FROM bill b
                JOIN customer c ON c.KH_IDKhachHang = b.DH_IDKhachHang ";
        $whereClauses = [];

        // Xử lý query tìm kiếm (như cũ)
        $arrayReplace = ['dh'];
        $queryOrder = trim(str_replace($arrayReplace, '', strtolower($queryOrder)));
        if ($queryOrder !== '') {
            $whereClauses[] = '(LOWER(c.KH_TenKhachHang) LIKE :query OR LOWER(c.KH_IDKhachHang) LIKE :query OR LOWER(b.DH_IDDonHang) LIKE :query OR LOWER(c.KH_SDTKhachHang) LIKE :query OR LOWER(c.KH_EmailKhachHang) LIKE :query)';
            $params[':query'] = '%' . $queryOrder . '%';
        }

        // Xử lý filterStatus (như cũ)
        if ($filterStatus !== '' && is_numeric($filterStatus)) {
             $whereClauses[] = 'b.DH_TrangThaiDonHang = :filterStatus';
             $params[':filterStatus'] = intval($filterStatus);
        }

        // --- SỬA: Xử lý lọc ngày tháng ---
        if ($startDate !== '' && $endDate !== '') {
            // Có cả ngày bắt đầu và kết thúc
            $whereClauses[] = 'b.DH_NgayDatDonHang BETWEEN :startDate AND :endDate';
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate;
        } else if ($startDate !== '' && $endDate === '') {
            // Chỉ có ngày bắt đầu
            $whereClauses[] = 'b.DH_NgayDatDonHang >= :startDate';
            $params[':startDate'] = $startDate;
        } else if ($startDate === '' && $endDate !== '') {
            // Chỉ có ngày kết thúc
            $whereClauses[] = 'b.DH_NgayDatDonHang <= :endDate';
            $params[':endDate'] = $endDate;
        }
        // --- KẾT THÚC SỬA ---

        // Nối các điều kiện WHERE nếu có
        if (!empty($whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        // Xử lý ORDER BY (như cũ)
        $orderBy = '';
        // ... (logic tạo $orderBy như cũ) ...
        if ($sortIDOrder !== '' && $sortDateOrder === '' && $sortStatusOrder === '') { $orderBy = 'ORDER BY b.DH_IDDonHang ' . ($sortIDOrder === 'DESC' ? 'DESC' : 'ASC'); }
        else if ($sortIDOrder === '' && $sortDateOrder !== '' && $sortStatusOrder === '') { $orderBy = 'ORDER BY b.DH_NgayDatDonHang ' . ($sortDateOrder === 'DESC' ? 'DESC' : 'ASC'); }
        else if ($sortIDOrder === '' && $sortDateOrder === '' && $sortStatusOrder !== '') { $orderBy = 'ORDER BY b.DH_TrangThaiDonHang ' . ($sortStatusOrder === 'DESC' ? 'DESC' : 'ASC'); }
        else { $orderBy = 'ORDER BY b.DH_IDDonHang DESC'; }
        $sql .= ' ' . $orderBy;

        // Thêm LIMIT (như cũ)
        $sql .= ' LIMIT :start, :limit';
        $params[':start'] = $startOrder;
        $params[':limit'] = $limitOrder;

        try {
            $conn = $ConnectDataBase->connectDB();
            $stmt = $conn->prepare($sql);

            // Bind các tham số động (như cũ)
            foreach ($params as $key => &$val) {
                if ($key === ':start' || $key === ':limit' || $key === ':filterStatus') {
                    $stmt->bindParam($key, $val, PDO::PARAM_INT);
                } else { // :query, :startDate, :endDate
                    $stmt->bindParam($key, $val, PDO::PARAM_STR);
                }
            }

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];

        } catch (PDOException $e) {
            error_log("SQL Error in selectLimitOrder: " . $e->getMessage() . " SQL: " . $sql . " Params: " . print_r($params, true));
            return [];
        }
    }




    // Sửa hàm countRecordOrder
    function countRecordOrder ($queryOrder, $filterStatus = '', $startDate = '', $endDate = '') {
        $ConnectDataBase = new ConnectDataBase;
        $params = [];
        $sql = "SELECT COUNT(b.DH_IDDonHang) as total
                FROM bill b
                JOIN customer c ON c.KH_IDKhachHang = b.DH_IDKhachHang ";
        $whereClauses = [];

        // Xử lý query tìm kiếm (như cũ)
        $arrayReplace = ['dh'];
        $queryOrder = trim(str_replace($arrayReplace, '', strtolower($queryOrder)));
        if ($queryOrder !== '') {
             $whereClauses[] = '(LOWER(c.KH_TenKhachHang) LIKE :query OR LOWER(c.KH_IDKhachHang) LIKE :query OR LOWER(b.DH_IDDonHang) LIKE :query OR LOWER(c.KH_SDTKhachHang) LIKE :query OR LOWER(c.KH_EmailKhachHang) LIKE :query)';
            $params[':query'] = '%' . $queryOrder . '%';
        }

        // Xử lý filterStatus (như cũ)
        if ($filterStatus !== '' && is_numeric($filterStatus)) {
            $whereClauses[] = 'b.DH_TrangThaiDonHang = :filterStatus';
            $params[':filterStatus'] = intval($filterStatus);
        }

        // --- SỬA: Xử lý lọc ngày tháng ---
        if ($startDate !== '' && $endDate !== '') {
            // Có cả ngày bắt đầu và kết thúc
            $whereClauses[] = 'b.DH_NgayDatDonHang BETWEEN :startDate AND :endDate';
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate;
        } else if ($startDate !== '' && $endDate === '') {
            // Chỉ có ngày bắt đầu
            $whereClauses[] = 'b.DH_NgayDatDonHang >= :startDate';
            $params[':startDate'] = $startDate;
        } else if ($startDate === '' && $endDate !== '') {
            // Chỉ có ngày kết thúc
            $whereClauses[] = 'b.DH_NgayDatDonHang <= :endDate';
            $params[':endDate'] = $endDate;
        }
        // --- KẾT THÚC SỬA ---

        // Nối các điều kiện WHERE nếu có
        if (!empty($whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        try {
            $conn = $ConnectDataBase->connectDB();
            $stmt = $conn->prepare($sql);

            // Bind các tham số động (như cũ)
            foreach ($params as $key => &$val) {
                 if ($key === ':filterStatus') {
                    $stmt->bindParam($key, $val, PDO::PARAM_INT);
                } else { // :query, :startDate, :endDate
                    $stmt->bindParam($key, $val, PDO::PARAM_STR);
                }
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? intval($result['total']) : 0;

        } catch (PDOException $e) {
            error_log("SQL Error in countRecordOrder: " . $e->getMessage() . " SQL: " . $sql . " Params: " . print_r($params, true));
            return 0;
        }
    }


    function selectOrderByCustomerID () {
        $ConnectDataBase = new ConnectDataBase;
        $sql = "SELECT bill.*, customer.* 
                FROM bill 
                JOIN customer ON customer.KH_IDKhachHang = bill.DH_IDKhachHang 
                WHERE bill.DH_IDKhachHang = :DH_IDKhachHang
                AND bill.DH_IDDonHang = :DH_IDDonHang";
        
        $stmt = $ConnectDataBase->connectDB()->prepare($sql);
        $stmt->bindParam(':DH_IDKhachHang', $this->DH_IDKhachHang);
        $stmt->bindParam(':DH_IDDonHang', $this->DH_IDDonHang);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($result) > 0) {
            return $result;
        } else {
            return [];
        }
    }
    

    
}
//aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
?>


