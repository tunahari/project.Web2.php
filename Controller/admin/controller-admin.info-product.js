$(document).ready(function () {
    const ClassFuction = new HandlingFunctions()
    const ClassValidate = new ValidateData()
    var Product__Info__Save__Btn = $('.Product__Info__Save')
    var Admin__Input__SP = $('.Admin__Input__SP')
    $(`.Product__Info__Content__Image__Update__Button__Group, .loading__box`).hide()

    /* ======================================== Chỉnh sửa thông tin sản phẩm ======================================== */
    Product__Info__Save__Btn.click(function () {
        var Array__Value__Input__SP = []
        Admin__Input__SP.map(function () {
            Array__Value__Input__SP.push($(this).val());
        })
        Array__Value__Input__SP.push($('#Admin__Input__SP_IDSanPham').val().trim().substring(2))
        var Object__Array__Value__Input__SP = {
            ArraySP: Array__Value__Input__SP
        }

        var flagNumber = 0
        var flagDate = 0
        var flagBH = 0
        var flagBrand = 0
        
        if ($('#Admin__Input__SP_MaChiNhanhSanPham').val().trim() === "") {
            $($('#Admin__Input__SP_MaChiNhanhSanPham').parent().children().get(2)).addClass('active')
        } else {
            flagBrand++
            $($('#Admin__Input__SP_MaChiNhanhSanPham').parent().children().get(2)).removeClass('active')
        }

        Admin__Input__SP.map(function () {
            if ($(this).attr('type') === 'number') {
                if (ClassValidate.checkQuantity($(this).val())) {
                    flagNumber++
                    $($(this).parent().children().get(2)).removeClass('active')
                } else {
                    $($(this).parent().children().get(2)).addClass('active')
                }
            }
        })

        Admin__Input__SP.map(function () {
            if ($(this).attr('type') === 'date') {
                var temp = $(this).val().split('-').reverse()
                var tempDate = []
                tempDate.push(temp[0] + "-", temp[1] + "-", temp[2])
                if (ClassValidate.validateDate(tempDate.join().replace(',','').replace(',',''))) {
                    flagDate++
                    $($(this).parent().children().get(2)).removeClass('active')
                } else {
                    $($(this).parent().children().get(2)).addClass('active')
                }
            }
        })

        var value__SP_BaoHanhSanPham = $('#Admin__Input__SP_BaoHanhSanPham').val()
        var value__SP_ThoiHanBaoHanhSanPham = $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val()
        if (value__SP_BaoHanhSanPham === '0') {
            if (value__SP_ThoiHanBaoHanhSanPham === '') {
                flagBH++
            } else {
                flagBH = 0
            }
        } else if (value__SP_BaoHanhSanPham === '1') {
            if (value__SP_ThoiHanBaoHanhSanPham !== '') {
                flagBH++
            } else {
                flagBH = 0
            }
        } else {
            flagBH = 0
        }

        if (value__SP_BaoHanhSanPham.trim() === '0' || value__SP_BaoHanhSanPham.trim() === '1') {
            flagBH++
            $($('#Admin__Input__SP_BaoHanhSanPham').parent().children().get(2)).removeClass('active')
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).removeClass('active')
        } else {
            $($('#Admin__Input__SP_BaoHanhSanPham').parent().children().get(2)).addClass('active')
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).addClass('active')
        }

        if ($('#Admin__Input__SP_BaoHanhSanPham').val().trim() === '0' && $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val().trim() === '') {
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).removeClass('active')
        } else if ($('#Admin__Input__SP_BaoHanhSanPham').val().trim() === '1' && $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val().trim() === '') {
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).addClass('active')
        } else if ($('#Admin__Input__SP_BaoHanhSanPham').val().trim() === '1' && $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val().trim() !== '') {
            if (ClassValidate.checkQuantity($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val())) {
                $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).removeClass('active')
            } else {
                $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).addClass('active')
            }
        }else {
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).removeClass('active')
        }

        if (flagNumber === 7 && flagDate === 1 && flagBH === 2 && flagBrand === 1) {
            ClassFuction.getAjaxPost('../../Controller/admin/controller-admin.info-product.php', Object__Array__Value__Input__SP).done(function(response){
                response = response.trim()
                if (response !== '' && response === 'update-success') {
                    $('.loading__box').show()
                    setTimeout(function() {
                        $('.loading__box').hide()
                        alertSuccess ('Lên là lên là lên là lên 👌!')
                    },2500)
                } else {
                    $('.loading__box').show()
                    if (response === 'update-failed') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('Cập nhật thông tin sản phẩm thất bại!')
                        },2500)
                    } else if (response === 'errorBH') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('Bảo hành không hợp lệ, vui lòng kiểm tra lại!')
                        },2500)
                    } else if (response === 'ErrorDate') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('Ngày ra mắt không hợp lệ, vui lòng kiểm tra lại!')
                        },2500)
                    } else if (response === 'ErrorBranch') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('Mã chi nhánh không đúng, vui lòng kiểm tra lại!')
                        },2500)
                    } 
                }
            })
                        // Thêm xử lý upload ảnh vào đây
                        let uploadPromises = [];
                        for (let i = 1; i <= 3; i++) {
                            let fileInput = $('#inputUpdateImage-' + i)[0].files[0];
                            if (fileInput) {
                                let promise = ClassFuction.getAjaxFile(`../../Controller/admin/controller-admin.info-product.php?id-update=${$('#Admin__Input__SP_IDSanPham').val().trim()}`,
                                    $('#inputUpdateImage-' + i), 'uploadImage-' + i);
                                uploadPromises.push(promise);
                            }
                        }
                        Promise.all(uploadPromises).then(results => {
                            // Xử lý kết quả upload ảnh sau khi tất cả ảnh đã được upload
                            results.forEach((result, index) => {
                                result = result.trim();
                                if (result !== 'update-file-failed') {
                                    $('#previewImage-' + (index + 1) + ' img').attr('src', '../../Controller/admin/' + result);
                                } else {
                                    // Xử lý lỗi upload ảnh
                                    alertFailed('Cập nhật ảnh ' + (index + 1) + ' thất bại!');
                                }
                            });
                            // alertSuccess('Cập nhật thông tin và ảnh sản phẩm thành công!');
                        }).catch(error => {
                            // Xử lý lỗi chung khi upload ảnh
                            alertFailed('Có lỗi xảy ra khi cập nhật ảnh!');
                        });
                        // $('.Product__Info__Content__Image__Update__Button__Group').remove();
        }
    })

    $('#Admin__Input__SP_ThoiDiemRaMatSanPham').change(function() {
        if ($(this).val() !== '') {
            $($(this).parent().children().get(2)).removeClass('active')
        } else {
            $($(this).parent().children().get(2)).addClass('active')
        }
    })

    Admin__Input__SP.map(function () {
        $(this).keyup(function() {
            $($(this).parent().children().get(2)).removeClass('active')
        })
    })

    $('#Admin__Input__SP_BaoHanhSanPham').keyup(function() {
        if ($(this).val().trim() === '0') {
            $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').attr('readonly', true);
            $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val('')
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).removeClass('active')
        } else if ($(this).val().trim() === '1') {
            $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').attr('readonly', false)
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).addClass('active')
        } else {
            $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').attr('readonly', true);
            $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').val('')
            $($('#Admin__Input__SP_ThoiHanBaoHanhSanPham').parent().children().get(2)).removeClass('active')
        }
    })

    $('#Admin__Input__SP_ThoiHanBaoHanhSanPham').keyup(function() {
        if ($('#Admin__Input__SP_BaoHanhSanPham').val().trim() === '0' && $(this).val().trim() === '') {
            $($(this).parent().children().get(2)).removeClass('active')
        } else if ($('#Admin__Input__SP_BaoHanhSanPham').val().trim() === '1' && $(this).val().trim() === '') {
            $($(this).parent().children().get(2)).addClass('active')
        } else if ($('#Admin__Input__SP_BaoHanhSanPham').val().trim() === '1' && $(this).val().trim() !== '') {
            if (ClassValidate.checkQuantity($(this).val())) {
                $($(this).parent().children().get(2)).removeClass('active')
            } else {
                $($(this).parent().children().get(2)).addClass('active')
            }
        } else {
            $($(this).parent().children().get(2)).removeClass('active')
        }
    })

    $('#Admin__Input__SP_MaChiNhanhSanPham').keyup(function() {
        if ($(this).val().trim() === '') {
            $($(this).parent().children().get(2)).addClass('active')
        } else {
            $($(this).parent().children().get(2)).removeClass('active')
        }
    })

    /* ======================================== Cập nhật ảnh của sản phẩm ======================================== */
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


    $('#submitUpdateAvatarEmployee').hide()
    $('#image-preview').hide()
    function previewImage (event, imageID, submitClass, inputFileID, previewID) {
        $('#' + imageID).hide()
        $('.' + submitClass).show()
        $('#image-preview').show()
        ClassFuction.previewFile (event, inputFileID, previewID)
    }

    $('#inputUpdateImage-1').change(function(event) {
        previewImage (event, 'Preview__Image__1', 'Product__Info__Content__Image__Update__Button__Group-1', 'inputUpdateImage-1', 'previewImage-1')
        $('.Product__Info__Content__1__Image__1__Update label').hide()
    })
    $('#inputUpdateImage-2').change(function(event) {
        previewImage (event, 'Preview__Image__2', 'Product__Info__Content__Image__Update__Button__Group-2', 'inputUpdateImage-2', 'previewImage-2')
        $('.Product__Info__Content__1__Image__2__Update label').hide()
    })
    $('#inputUpdateImage-3').change(function(event) {
        previewImage (event, 'Preview__Image__3', 'Product__Info__Content__Image__Update__Button__Group-3', 'inputUpdateImage-3', 'previewImage-3')
        $('.Product__Info__Content__1__Image__3__Update label').hide()
    })

    function resetImage (numberImage) {
        $('.Product__Info__Content__1__Image__' + numberImage + '__Update label').show()
        $('.Product__Info__Content__Image__Update__Button__Group-' + numberImage).hide()
        $('#Preview__Image__' + numberImage).show()
        $($('#previewImage-' + numberImage).children().get(1)).attr('src','')
        $('#previewImage-' + numberImage).children().get(1).remove()   
        $('#inputUpdateImage-' + numberImage).val('') 
    }

    $('#cancelUpdateImage-1').click(function() {resetImage ('1') })
    $('#cancelUpdateImage-2').click(function() {resetImage ('2') })
    $('#cancelUpdateImage-3').click(function() {resetImage ('3') })

    function sendAjaxUpdateImage (numberImage, notifySuccess, notifyFailed) {
        var dataResponse = ClassFuction.getAjaxFile (`../../Controller/admin/controller-admin.info-product.php?id-update=${$('#Admin__Input__SP_IDSanPham').val().trim()}`, 
        $('#inputUpdateImage-' + numberImage), 'uploadImage-' + numberImage)
        dataResponse = dataResponse.trim()
        if (dataResponse !== 'update-file-failed') {
            $('.loading__box').show()
            setTimeout(function() {
                $('.loading__box').hide()
                alertSuccess (notifySuccess)
                resetImage (numberImage)
                $('#previewImage-' + numberImage + ' img').attr('src','../../Controller/admin/' + dataResponse)
            },2500)
        } else {
            $('.loading__box').show()
            setTimeout(function() {
                $('.loading__box').hide()
                alertFailed (notifyFailed)
                resetImage (numberImage)
            },2500)  
        }
    }

    $('#submitUpdateImage-1').click(function() {
        sendAjaxUpdateImage ('1', 'Cập nhật Ảnh Chính sản phẩm thành công!', 'Cập nhật Ảnh Chính sản phẩm thất bại!')
    })

    $('#submitUpdateImage-2').click(function() {
        sendAjaxUpdateImage ('2', 'Cập nhật Ảnh Slide 1 sản phẩm thành công!', 'Cập nhật Ảnh Slide 1 sản phẩm thất bại!')
    })

    $('#submitUpdateImage-3').click(function() {
        sendAjaxUpdateImage ('3', 'Cập nhật Ảnh Slide 2 sản phẩm thành công!', 'Cập nhật Ảnh Slide 2 sản phẩm thất bại!')
    })


    /* ======================================== Cập nhật số lượng sản phẩm ======================================== */
    $('.Update__Quantity__Product__Save').click(function() {
        if (ClassValidate.checkQuantity($('#Update__Input__SLSanPham').val().trim())) {
            ClassFuction.getAjaxPost('../../Controller/admin/controller-admin.info-product.php', {
                updateQuantity: 'update-quantity',
                idUpdate: $('#Admin__Input__SP_IDSanPham').val().trim().substring(2),
                quantityUpdate: $('#Update__Input__SLSanPham').val().trim()
            }).done(function(response){
                response = response.trim()
                if (response !== '' && response === 'update-success') {
                    $('.loading__box').show()
                    setTimeout(function() {
                        $('.loading__box').hide()
                        alertSuccess ('Cập nhật số lượng sản phẩm thành công!')
                        $('#Update__Input__SLSanPham').val(0)
                    },2500)
                } else {
                    $('.loading__box').show()
                    if (response === 'update-failed') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('Cập nhật số lượng sản phẩm thất bại!')
                            $('#Update__Input__SLSanPham').val(0)
                        },2500)
                    } else if (response === 'errorID') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('ID sản phẩm không hợp lệ, vui lòng kiểm tra lại!')
                            $('#Update__Input__SLSanPham').val(0)
                        },2500)
                    } else if (response === 'errorQuantity') {
                        setTimeout(function() {
                            $('.loading__box').hide()
                            alertFailed ('Số lượng sản phẩm không hợp lệ, vui lòng kiểm tra lại!')
                            $('#Update__Input__SLSanPham').val(0)
                        },2500)
                    }
                }
            })
            $('#Update__Input__SanPham__Error').removeClass('active')
        } else {
            $('#Update__Input__SanPham__Error').addClass('active')
        }
    })

})