$(document).ready(function() {
    const ClassFuction = new HandlingFunctions();
    const ClassValidate = new ValidateData();
    $('.loading__box').hide()

    // Lấy ra các đơn hàng ra
    function fetchOrder (fetchOrder, limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder) {
        ClassFuction.getAjaxPost('../../Controller/admin/controller-admin.order.php', 
        {
            fetchOrder: fetchOrder,
            limitOrder: limitOrder,
            pageOrder: pageOrder,
            queryOrder: queryOrder,
            sortIDOrder: sortIDOrder,
            sortDateOrder: sortDateOrder,
            sortStatusOrder: sortStatusOrder
        }
        ).done(function(response){
            response = response.trim()
            if (response !== '') {
                var dataResponse = JSON.parse(response)
                console.log(dataResponse[2])
                if (dataResponse[1] !== null) {
                    dataResponse[0].trim().length !== 449 ? $('.pagination__order').html(dataResponse[0]) : $('.pagination__order').html('')
                    $('.table__order__tbody').html(dataResponse[1])
                } else {
                    $('.table__order__tbody').html(
                        `<tr class="order__data__not__found__tr">
                             <td colspan="6" class="order__data__not__found__tr__td">
                                <div class="order__data__not__found">
                                    Không tìm thấy nhân viên phù hợp, vui lòng thử lại!
                                </div>
                             </td>
                        </tr>`
                    )
                    $('.pagination__order').html('')
                }
            } else {
                console.log('Empty!')
            }
        });
    }

    /* Mặc định khi hiển thị */
    fetchOrder ('fetch-order', '5', '1', '', 'DESC', 'ASC', 'ASC')

    /* Xử lý chọn số item hiển thị một trang */
    $('body').on('mouseup', function() {
        $('.select__order__option__box').removeClass('active')
    });
    $($('.select__order__option').get(0)).unbind('click')
    $('.select__order__option__selected').click(function() {
        $('.select__order__option__box').toggleClass('active')
    })
    $('.select__order__option').click(function() {
        var limitOrder = $(this).html().trim()
        var pageOrder = $('.pagination__order__item.active').attr('value')
        var queryOrder = $('#search__order__input').val().trim()
        var sortIDOrder = $('#sort__order__id').attr('value').trim()
        var sortDateOrder = $('#sort__order__date').attr('value').trim()
        var sortStatusOrder = $('#sort__order__status').attr('value').trim()
        fetchOrder ('fetch-order', limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder)
        $('.select__order__option__selected').html(limitOrder)
        $('.select__order__option__box').removeClass('active')
    })

    /* Xử lý bấm nút phân trang */
    $(document).on('click', '.pagination__order__item', function() {
        var limitOrder = $('.select__order__option__selected').text().trim()
        var pageOrder = $(this).attr('value')
        var queryOrder = $('#search__order__input').val().trim()
        var sortIDOrder = $('#sort__order__id').attr('value').trim()
        var sortDateOrder = $('#sort__order__date').attr('value').trim()
        var sortStatusOrder = $('#sort__order__status').attr('value').trim()
        fetchOrder ('fetch-order', limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder)
    })

    /* Xử lý tìm kiếm */
    $('#search__order__input').keyup(function () {
        var limitOrder = $('.select__order__option__selected').text().trim()
        var pageOrder = $('.pagination__order__item.active').attr('value')
        var queryOrder = $(this).val().trim()
        var sortIDOrder = $('#sort__order__id').attr('value').trim()
        var sortDateOrder = $('#sort__order__date').attr('value').trim()
        var sortStatusOrder = $('#sort__order__status').attr('value').trim()
        fetchOrder ('fetch-order', limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder)
    })

    /* Xử lý sắp xếp theo ID */
    $('#sort__order__id').click(function() {
        var limitOrder = $('.select__order__option__selected').text().trim()
        var pageOrder = $('.pagination__order__item.active').attr('value')
        var queryOrder = $('#search__order__input').val().trim()
        var sortDateOrder = ''
        var sortStatusOrder = ''
        $('#sort__order__date').attr('value','')
        $('#sort__order__status').attr('value','')
        if ($(this).attr('value') === '') {
            $(this).attr('value', 'ASC')
        } else {
            $(this).attr('value') === 'ASC' ? $('#sort__order__id').attr('value', 'DESC') : $('#sort__order__id').attr('value', 'ASC')
        }
        var sortIDOrder = $(this).attr('value')
        fetchOrder ('fetch-order', limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder)
    })

    /* Xử lý sắp xếp theo ngày tạo */
    $('#sort__order__date').click(function() {
        var limitOrder = $('.select__order__option__selected').text().trim()
        var pageOrder = $('.pagination__order__item.active').attr('value')
        var queryOrder = $('#search__order__input').val().trim()
        var sortIDOrder = ''
        var sortStatusOrder = ''
        $('#sort__order__id').attr('value','')
        $('#sort__order__status').attr('value','')
        if ($(this).attr('value') === '') {
            $(this).attr('value', 'ASC')
        } else {
            $(this).attr('value') === 'ASC' ? $('#sort__order__date').attr('value', 'DESC') : $('#sort__order__date').attr('value', 'ASC')
        }
        var sortDateOrder = $(this).attr('value')
        fetchOrder ('fetch-order', limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder)
    })

    /* Xử lý sắp xếp theo trạng thái */
    $('#sort__employee__position').click(function() {
        var limitOrder = $('.select__order__option__selected').text().trim()
        var pageOrder = $('.pagination__order__item.active').attr('value')
        var queryOrder = $('#search__order__input').val().trim()
        var sortIDOrder = ''
        var sortDateOrder = ''
        $('#sort__order__id').attr('value','')
        $('#sort__order__date').attr('value','')
        if ($(this).attr('value') === '') {
            $(this).attr('value', 'ASC')
        } else {
            $(this).attr('value') === 'ASC' ? $('#sort__order__status').attr('value', 'DESC') : $('#sort__order__status').attr('value', 'ASC')
        }
        var sortStatusOrder = $(this).attr('value')
        fetchOrder ('fetch-order', limitOrder, pageOrder, queryOrder, sortIDOrder, sortDateOrder, sortStatusOrder)
    })










// Thêm vào file JavaScript của bạn (ví dụ admin-order.js)

$(document).ready(function() {
    // Biến toàn cục để lưu trữ trạng thái lọc
    let fromDate = '';
    let toDate = '';
    
    // Hàm tải dữ liệu đơn hàng
    function loadOrderData(limit, page, query, sortID, sortDate, sortStatus) {
        $.ajax({
            url: '../../Controller/admin/controller-admin.order.php',
            method: 'POST',
            data: {
                fetchOrder: 'fetch-order',
                limitOrder: limit,
                pageOrder: page,
                queryOrder: query,
                sortIDOrder: sortID,
                sortDateOrder: sortDate,
                sortStatusOrder: sortStatus,
                fromDate: fromDate,
                toDate: toDate
            },
            success: function(data) {
                const dataResponse = JSON.parse(data);
                $('.pagination__order').html(dataResponse[0]);
                $('.table__order__tbody').html(dataResponse[1]);
            }
        });
    }
    
    // Xử lý sự kiện khi nhấn nút lọc
    $('#filter_date_btn').click(function() {
        fromDate = $('#from_date').val();
        toDate = $('#to_date').val();
        
        if (fromDate === '' && toDate === '') {
            alert('Vui lòng chọn ít nhất một ngày để lọc!');
            return;
        }
        
        // Kiểm tra ngày hợp lệ
        if (fromDate !== '' && toDate !== '' && fromDate > toDate) {
            alert('Ngày bắt đầu không thể lớn hơn ngày kết thúc!');
            return;
        }
        
        // Tải dữ liệu với bộ lọc ngày
        loadOrderData('5', '1', '', '', '', '');
    });
    
    // Xử lý sự kiện khi nhấn nút đặt lại
    $('#reset_date_btn').click(function() {
        $('#from_date').val('');
        $('#to_date').val('');
        fromDate = '';
        toDate = '';
        
        // Tải lại dữ liệu không có bộ lọc
        loadOrderData('5', '1', '', '', '', '');
    });
    
    // Thêm vào sự kiện phân trang hiện có
    $(document).on('click', '.pagination__order__item', function() {
        const page = $(this).attr('value');
        const limit = $('#select__limit__order').val();
        const query = $('#search__order').val();
        const sortID = $('#sort__id__order').val();
        const sortDate = $('#sort__date__order').val();
        const sortStatus = $('#sort__status__order').val();
        
        loadOrderData(limit, page, query, sortID, sortDate, sortStatus);
    });
    
    // Cập nhật các sự kiện tìm kiếm và sắp xếp khác
    // Đảm bảo tất cả sự kiện đều truyền tham số fromDate và toDate
});





    /* Tạo thông báo */
    function alertSuccess (notify) {
        $('.alert__notify__box__success__right__content').text(notify)
        $('.alert__notify__box__success').addClass('active')
        setTimeout(function() {
            $('.alert__notify__box__success__progress').addClass('active')
        },500)
        setTimeout(function() {
            $('.alert__notify__box__success').removeClass('active')
            $('.alert__notify__box__success__progress').removeClass('active')
        },5000)
        $('.alert__notify__box__success__close').click(function() {
            $('.alert__notify__box__success').removeClass('active')
            setTimeout(function() {
                $('.alert__notify__box__success__progress').removeClass('active')
            },300)
        })
    }

    function alertFailed (notify) {
        $('.alert__notify__box__failed__right__content').text(notify)
        $('.alert__notify__box__failed').addClass('active')
        setTimeout(function() {
            $('.alert__notify__box__failed__progress').addClass('active')
        },500)
        setTimeout(function() {
            $('.alert__notify__box__failed').removeClass('active')
            $('.alert__notify__box__failed__progress').removeClass('active')
        },5000)
        $('.alert__notify__box__failed__close').click(function() {
            $('.alert__notify__box__failed').removeClass('active')
            setTimeout(function() {
                $('.alert__notify__box__failed__progress').removeClass('active')
            },300)
        })
    }
})