<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <link rel="stylesheet" href="./main-admin.css">
    <link rel="stylesheet" href="./style-admin.css">
    <link rel="stylesheet" href="./product-admin.css">
    <link rel="stylesheet" href="./order-admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<style>
    .date__filter {
        color: black;
        display: flex;
        align-items: center;
        width: 50%;
        gap: 15px;
        margin-top: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .date__filter__group {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .date__filter__input {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .date__filter__button {
        display: flex;
        gap: 10px;
    }

    .date__filter__button button {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        transition: all 0.3s;
    }

    #filter_date_btn {
        background-color: #4e73df;
        color: white;
    }

    #filter_date_btn:hover {
        background-color: #2e59d9;
    }

    #reset_date_btn {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
    }

    #reset_date_btn:hover {
        background-color: #e9ecef;
    }
</style>

<body>
    <div class="main">
        <?php include './include.header.php'; ?>
        <div class="containers">
            <?php include './include.menu.php'; ?>
            <div class="list__table--container">
                <h1>QUẢN LÝ ĐƠN HÀNG</h1>
                <div class="check__table">
                    <div class="select__product">
                        <h4>Hiển thị</h4>
                        <div class="select__order__box">
                            <div class="select__order__option__selected">
                                <div class="select__order__option" value="5">5</div>
                            </div>
                            <div class="select__order__option__box">
                                <div class="select__order__option" value="5">5</div>
                                <div class="select__order__option" value="10">10</div>
                                <div class="select__order__option" value="15">15</div>
                                <div class="select__order__option" value="20">20</div>
                            </div>

                        </div>
                    </div>
                    <div class="search__product">

                        <input type="text" placeholder="Tìm kiếm..." id="search__order__input">
                        <div class="search__product__button">
                            <i class='bx bx-search-alt-2'></i>
                        </div>

                    </div>


                </div>
                Lọc theo ngày tháng năm
                <div class="date__filter">
                    <div class="date__filter__group">
                        <label for="from_date">Từ ngày:</label>
                        <input type="date" id="from_date" class="date__filter__input">
                    </div>
                    <div class="date__filter__group">
                        <label for="to_date">Đến ngày:</label>
                        <input type="date" id="to_date" class="date__filter__input">
                    </div>
                    <div class="date__filter__button">
                        <button id="filter_date_btn"><i class='bx bx-filter-alt'></i> Lọc</button>
                        <button id="reset_date_btn"><i class='bx bx-reset'></i> Đặt lại</button>
                    </div>
                </div>


                <div class="table__order--container">
                    <div class="table__order__thead">
                        <div class="table__order__thead__tr">
                            <div class="table__order__thead__tr__th" id="sort__order__id" value="DESC">ID<i class="fa-solid fa-arrows-up-down"></i></div>
                            <div class="table__order__thead__tr__th">Tên Khách Hàng</div>
                            <div class="table__order__thead__tr__th">Số Điện Thoại</div>
                            <div class="table__order__thead__tr__th">Tổng Tiền</div>
                            <div class="table__order__thead__tr__th">Số Mặt Hàng</div>
                            <div class="table__order__thead__tr__th">Số Sản Phẩm</div>
                            <div class="table__order__thead__tr__th" id="sort__order__status" value="ASC">Trạng Thái<i class="fa-solid fa-arrows-up-down"></i></div>
                            <div class="table__order__thead__tr__th" id="sort__order__date" value="ASC">Ngày Đặt<i class="fa-solid fa-arrows-up-down"></i></div>
                        </div>
                    </div>
                    <div class="table__order__tbody"></div>
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

                <!-- PAGINATION -->
                <div class="pagination__order"></div>
            </div>
        </div>
    </div>
    </div>

    <script src="./script-admin.js"></script>
    <script src="./test-admin.js"></script>
    <script src="./customer-admin.js"></script>
</body>

</html>
<script src="../../Controller/admin/controller-admin.order.js"></script>