function alertSuccess(notify) {
  $(".alert__notify__box__success__right__content").text(notify);
  $(".alert__notify__box__success").addClass("active");
  setTimeout(function () {
    $(".alert__notify__box__success__progress").addClass("active");
  }, 500);
  setTimeout(function () {
    $(".alert__notify__box__success").removeClass("active");
    $(".alert__notify__box__success__progress").removeClass("active");
  }, 5000);
  $(".alert__notify__box__success__close").click(function () {
    $(".alert__notify__box__success").removeClass("active");
    setTimeout(function () {
      $(".alert__notify__box__success__progress").removeClass("active");
    }, 300);
  });
}

function alertFailed(notify) {
  $(".alert__notify__box__failed__right__content").text(notify);
  $(".alert__notify__box__failed").addClass("active");
  setTimeout(function () {
    $(".alert__notify__box__failed__progress").addClass("active");
  }, 500);
  setTimeout(function () {
    $(".alert__notify__box__failed").removeClass("active");
    $(".alert__notify__box__failed__progress").removeClass("active");
  }, 5000);
  $(".alert__notify__box__failed__close").click(function () {
    $(".alert__notify__box__failed").removeClass("active");
    setTimeout(function () {
      $(".alert__notify__box__failed__progress").removeClass("active");
    }, 300);
  });
}
$(document).ready(function () {
  $(".Account__Register__Right__Error, .loading__box, .loading__bg").hide();

  $("#changePasswordForm").on("submit", function (e) {
    e.preventDefault();
    const password = $("#KH_MatKhauKhachHang").val(); // Lấy giá trị từ input

    if (password == "") {
      alertFailed("Mật khẩu không được để trống");
    }
    $.ajax({
      method: "POST",
      url: "../../controller/admin/controller-admin.info-customer.php",
      data: {
        KH_MatKhauKhachHang: password,
        //   updatePassWord: true
      },
      dataType: "text",
      success: function (response) {
        if (response !== "") {
          $("#KH_MatKhauKhachHang").val(response);
          alertSuccess("Mật khẩu đã được đổi thành công!");
        }
      },
      error: function (xhr, status, error) {
        alertFailed("Cập nhật mật khẩu thất bại. Try Again !");
      },
    });
  });



//////////////Đây là cập nhật có reload trang, ko có ajax
  // $("#updateInfoCustomerForm").on("submit", function(e) {
  //   e.preventDefault();
  //   $.ajax({
  //     method: "POST",
  //     url: "../../controller/admin/controller-admin.info-customer.php",
  //     data: $(this).serialize() + "&updateInfoCustomer=true", //Gửi toàn bộ dữ liệu form
  //     dataType: "json",
  //     success: function(response) {
  //       if (response.success) {
  //         alertSuccess(response.message);
  //         setTimeout(function() {
  //           location.reload();
  //         }, 2000);
  //       } else {
  //         alertFailed(response.message);
  //       }
  //     },
  //     error: function(xhr, status, error) {
  //       alertFailed("Đã xảy ra lỗi khi cập nhật thông tin khách hàng!");
  //     }
  //   });
  // });



  //////////Xử lý ajax cập nhật thống tin khách hàng
  $("#updateInfoCustomerForm").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
        method: "POST",
        url: "../../controller/admin/controller-admin.info-customer.php",
        data: $(this).serialize() + "&updateInfoCustomer=true",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                alertSuccess(response.message);
                // Cập nhật thông tin trên trang
                $("#KH_TenKhachHang").val(response.data.KH_TenKhachHang); //Ví dụ cập nhật tên
                $("#KH_SDTKhachHang").val(response.data.KH_SDTKhachHang); //Ví dụ cập nhật số điện thoại
                $("#KH_DiaChiKhachHang").val(response.data.KH_DiaChiKhachHang); //Ví dụ cập nhật địa chỉ
                $("#KH_EmailKhachHang").val(response.data.KH_EmailKhachHang); //Ví dụ cập nhật email
                //Thêm cập nhật cho các trường khác nếu cần thiết
            } else {
                alertFailed(response.message);
            }
        },
        error: function(xhr, status, error) {
            alertFailed("Đã xảy ra lỗi khi cập nhật thông tin khách hàng!");
        }
    });
});

});
