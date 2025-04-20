$(document).ready(function() {
    const ClassFuction = new HandlingFunctions();
    const ClassValidate = new ValidateData();
    $('.loading__box').hide();

    // --- Biến lưu trữ trạng thái hiện tại ---
    let currentLimit = $('.select__order__option__selected .select__order__option').attr('value') || 5;
    let currentPage = 1;
    let currentQuery = '';
    let currentSortID = $('#sort__order__id').attr('value') || 'DESC';
    let currentSortDate = $('#sort__order__date').attr('value') || 'ASC';
    let currentSortStatusOrder = $('#sort__order__status').attr('value') || 'ASC';
    let currentFilterStatus = $('#select__status__order').val() || '';
    let currentStartDate = $('#filter__start__date').val() || ''; // Lấy ngày bắt đầu ban đầu
    let currentEndDate = $('#filter__end__date').val() || '';   // Lấy ngày kết thúc ban đầu

    // --- Hàm lấy và hiển thị dữ liệu đơn hàng ---
    // Thêm tham số startDate, endDate
    function fetchOrder (fetchOrderAction, limit, page, query, sortID, sortDate, sortStatus, filterStatus, startDate, endDate) {
        // Hiển thị loading (nếu có)
        // $('.loading__box').show();

        ClassFuction.getAjaxPost('../../Controller/admin/controller-admin.order.php',
        {
            action: fetchOrderAction,
            limitOrder: limit,
            pageOrder: page,
            queryOrder: query,
            sortIDOrder: sortID,
            sortDateOrder: sortDate,
            sortStatusOrder: sortStatus,
            filterStatus: filterStatus,
            startDate: startDate, // Gửi ngày bắt đầu
            endDate: endDate     // Gửi ngày kết thúc
        }
        ).done(function(dataResponse){
            // ... (phần xử lý response như cũ) ...
             if (dataResponse && typeof dataResponse === 'object') {
                try {
                    // Cập nhật bảng
                    if (dataResponse.tableContent !== undefined && dataResponse.tableContent !== null) {
                        if (dataResponse.tableContent.trim() !== '') {
                             $('.table__order__tbody').html(dataResponse.tableContent);
                        } else {
                             $('.table__order__tbody').html(`<div class="no-data">Không tìm thấy đơn hàng nào.</div>`);
                        }
                    } else {
                         $('.table__order__tbody').html(`<div class="no-data">Dữ liệu bảng không hợp lệ.</div>`);
                    }
                    // Cập nhật phân trang
                    if (dataResponse.pagination !== undefined && dataResponse.pagination !== null) {
                        if (dataResponse.pagination.trim() !== '') {
                            $('.pagination__order').html(dataResponse.pagination);
                            currentPage = parseInt($('.pagination__order .page-item.active .page-link').text()) || 1;
                        } else {
                             $('.pagination__order').html('');
                        }
                    } else {
                         $('.pagination__order').html('');
                    }
                } catch (e) { /* ... xử lý lỗi ... */ }
            } else { /* ... xử lý lỗi ... */ }
        }).fail(function(xhr, status, error) { /* ... xử lý lỗi ... */ });
    }

   // --- THÊM CODE NÀY ---
    // Xử lý hiển thị/ẩn dropdown chọn số lượng item
    $(document).on('click', '.select__order__option__selected', function(e) {
        // Ngăn chặn việc click lan ra document nếu đang mở dropdown
        e.stopPropagation();
        // Tìm box option tương ứng và toggle class 'active'
        $(this).closest('.select__order__box').find('.select__order__option__box').toggleClass('active');
    });

    // (Tùy chọn - nên có) Xử lý ẩn dropdown khi click ra ngoài
    $(document).on('click', function(e) {
        // Nếu click không phải vào bên trong select box thì ẩn tất cả các box option đang mở
        if (!$(e.target).closest('.select__order__box').length) {
            $('.select__order__option__box').removeClass('active');
        }
    });
    // --- KẾT THÚC CODE THÊM ---

    /* --- Gọi hàm fetchOrder lần đầu khi tải trang --- */
    // Truyền thêm ngày tháng ban đầu
    fetchOrder ('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate);

    /* --- Xử lý chọn số item hiển thị một trang --- */
    $(document).off('click', '.select__order__option__box .select__order__option').on('click', '.select__order__option__box .select__order__option', function() {
        currentLimit = $(this).attr('value');
        currentPage = 1;
        // Lấy lại giá trị ngày tháng hiện tại từ input
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();
        fetchOrder ('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate); // Truyền ngày tháng
        $('.select__order__option__selected .select__order__option').text($(this).text()).attr('value', currentLimit);
        $('.select__order__option__box').removeClass('active');
    });


     /* --- Xử lý bấm nút phân trang --- */
    // THAY THẾ ĐOẠN NÀY
    $(document).on('click', '.pagination__order .pagination__order__item', function(e) {
        e.preventDefault(); // Ngăn hành vi mặc định (nếu có)

        // Kiểm tra nếu nút bị disabled hoặc đang active thì không làm gì cả
        if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
            return;
        }

        // Lấy số trang từ thuộc tính value của div được click
        let targetPage = parseInt($(this).attr('value'));

        // Kiểm tra xem giá trị có hợp lệ không (phòng trường hợp value không phải số)
        if (isNaN(targetPage) || targetPage < 1) {
            console.error("Invalid page value:", $(this).attr('value'));
            return;
        }

        currentPage = targetPage; // Cập nhật trang hiện tại

        // Lấy lại giá trị ngày tháng hiện tại từ input (nếu cần)
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();

        // Gọi hàm fetchOrder với trang mới và các tham số hiện tại
        fetchOrder ('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate);
    });
    // KẾT THÚC THAY THẾ


    /* --- Xử lý tìm kiếm --- */
    let debounceTimer; // Biến để lưu trữ timer cho debounce

    //Live Search với Debounce)
    $('#search__order__input').on('input', function() {
        // Xóa timer cũ nếu có
        clearTimeout(debounceTimer);

        // Lấy giá trị tìm kiếm hiện tại
        const query = $(this).val().trim();

        // Đặt timer mới
        debounceTimer = setTimeout(function() {
            // Chỉ thực hiện tìm kiếm nếu query đã thay đổi so với query hiện tại
            // (hoặc nếu bạn muốn tìm kiếm ngay cả khi xóa hết chữ)
            // if (query !== currentQuery) { // Bạn có thể thêm điều kiện này nếu muốn
                currentQuery = query;
                currentPage = 1; // Luôn reset về trang 1 khi tìm kiếm mới
                // Lấy lại giá trị ngày tháng hiện tại từ input
                currentStartDate = $('#filter__start__date').val();
                currentEndDate = $('#filter__end__date').val();
                fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate);
            // }
        }, 100); // Chờ 500ms (nửa giây) sau khi người dùng ngừng gõ mới tìm kiếm
    });
    //Tìm kiếm bấm nútnút
    $('.search__product__button').off('click').on('click', function() {
        currentQuery = $('#search__order__input').val().trim();
        currentPage = 1;
        // Lấy lại giá trị ngày tháng hiện tại từ input
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();
        fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate); // Truyền ngày tháng
    });
    //tìm kiếm bằng phím enter
    $('#search__order__input').off('keypress').on('keypress', function(e) {
        if (e.which == 13) {
            currentQuery = $(this).val().trim();
            currentPage = 1;
            // Lấy lại giá trị ngày tháng hiện tại từ input
            currentStartDate = $('#filter__start__date').val();
            currentEndDate = $('#filter__end__date').val();
            fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate); // Truyền ngày tháng
        }
    });


    /* --- Xử lý sắp xếp --- */
    $('#sort__order__id, #sort__order__date, #sort__order__status').off('click').on('click', function() {
        // ... (logic reset và cập nhật sort như cũ) ...
        let sortType = $(this).attr('id');
        let currentSortValue = $(this).attr('value');
        let newSortValue = (currentSortValue === 'ASC') ? 'DESC' : 'ASC';
        currentSortID = ''; currentSortDate = ''; currentSortStatusOrder = '';
        $('#sort__order__id').attr('value', '').find('i').attr('class', 'fa-solid fa-arrows-up-down');
        $('#sort__order__date').attr('value', '').find('i').attr('class', 'fa-solid fa-arrows-up-down');
        $('#sort__order__status').attr('value', '').find('i').attr('class', 'fa-solid fa-arrows-up-down');
        if (sortType === 'sort__order__id') currentSortID = newSortValue;
        else if (sortType === 'sort__order__date') currentSortDate = newSortValue;
        else if (sortType === 'sort__order__status') currentSortStatusOrder = newSortValue;
        $(this).attr('value', newSortValue);
        $(this).find('i').removeClass('fa-arrows-up-down fa-sort-up fa-sort-down').addClass(newSortValue === 'ASC' ? 'fa-sort-up' : 'fa-sort-down');
        // ---

        currentPage = 1;
        // Lấy lại giá trị ngày tháng hiện tại từ input
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();
        fetchOrder ('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate); // Truyền ngày tháng
    });


    /* --- Xử lý lọc theo trạng thái --- */
    $('#select__status__order').off('change').on('change', function() {
        currentFilterStatus = $(this).val();
        currentPage = 1;
        // Lấy lại giá trị ngày tháng hiện tại từ input
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();
        fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate); // Truyền ngày tháng
    });

    /* --- THÊM: Xử lý lọc theo ngày tháng --- */
    $('#filter__date__button').off('click').on('click', function() {
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();

        // Optional: Validate dates (e.g., start date <= end date)
        if (currentStartDate && currentEndDate && currentStartDate > currentEndDate) {
            alertFailed('Ngày bắt đầu không được lớn hơn ngày kết thúc.');
            return; // Ngăn không gọi fetchOrder
        }

        currentPage = 1; // Reset về trang 1 khi lọc ngày
        // Lấy các giá trị filter/sort/query hiện tại
        currentQuery = $('#search__order__input').val().trim();
        currentFilterStatus = $('#select__status__order').val();
        // currentSortID, currentSortDate, currentSortStatusOrder giữ nguyên giá trị hiện tại

        fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate);
    });

    // Optional: Tự động lọc khi thay đổi ngày (thay cho nút bấm)
    $('#filter__start__date, #filter__end__date').off('change').on('change', function() {
        currentStartDate = $('#filter__start__date').val();
        currentEndDate = $('#filter__end__date').val();
        if (currentStartDate && currentEndDate && currentStartDate > currentEndDate) {
             // Có thể không báo lỗi mà chỉ không lọc
            return;
        }
        currentPage = 1;
        currentQuery = $('#search__order__input').val().trim();
        currentFilterStatus = $('#select__status__order').val();
        fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate);
    });
    /* --- THÊM: Xử lý nút Reset ngày tháng --- */
$('#reset__date__button').off('click').on('click', function() {
    // 1. Xóa giá trị trong input
    $('#filter__start__date').val('');
    $('#filter__end__date').val('');

    // 2. Cập nhật biến trạng thái ngày tháng về rỗng
    currentStartDate = '';
    currentEndDate = '';

    // 3. Reset về trang đầu tiên
    currentPage = 1;

    // 4. Lấy lại các giá trị lọc/tìm kiếm/sắp xếp hiện tại khác (nếu cần)
    currentQuery = $('#search__order__input').val().trim();
    currentFilterStatus = $('#select__status__order').val();
    // currentSortID, currentSortDate, currentSortStatusOrder giữ nguyên

    // 5. Gọi lại hàm fetchOrder với ngày tháng đã được reset (rỗng)
    fetchOrder('load_order', currentLimit, currentPage, currentQuery, currentSortID, currentSortDate, currentSortStatusOrder, currentFilterStatus, currentStartDate, currentEndDate);
});
/* --- KẾT THÚC XỬ LÝ NÚT RESET --- */



    /* --- Các hàm thông báo (giữ nguyên) --- */
   

    /* --- Các hàm thông báo (giữ nguyên) --- */
    function alertSuccess (notify) {
        $('.alert__notify__box__success__right__content').text(notify);
        $('.alert__notify__box__success').addClass('active');
        // Thêm class active cho progress bar sau một khoảng trễ nhỏ
        setTimeout(function() {
            $('.alert__notify__box__success__progress').addClass('active');
        }, 10); // 10ms delay

        const timerSuccess = setTimeout(function() {
            $('.alert__notify__box__success').removeClass('active');
            $('.alert__notify__box__success__progress').removeClass('active');
        }, 5000); // Tự động ẩn sau 5 giây

        // Xử lý nút đóng
        $('.alert__notify__box__success__close').off('click').on('click', function() {
            clearTimeout(timerSuccess); // Hủy timer tự động ẩn
            $('.alert__notify__box__success').removeClass('active');
            $('.alert__notify__box__success__progress').removeClass('active');
        });
    }

    function alertFailed (notify) {
        $('.alert__notify__box__failed__right__content').text(notify);
        $('.alert__notify__box__failed').addClass('active');
         // Thêm class active cho progress bar sau một khoảng trễ nhỏ
        setTimeout(function() {
            $('.alert__notify__box__failed__progress').addClass('active');
        }, 10); // 10ms delay

        const timerFailed = setTimeout(function() {
            $('.alert__notify__box__failed').removeClass('active');
            $('.alert__notify__box__failed__progress').removeClass('active');
        }, 5000); // Tự động ẩn sau 5 giây

        // Xử lý nút đóng
        $('.alert__notify__box__failed__close').off('click').on('click', function() {
            clearTimeout(timerFailed); // Hủy timer tự động ẩn
            $('.alert__notify__box__failed').removeClass('active');
            $('.alert__notify__box__failed__progress').removeClass('active');
        });
    }
    
});
