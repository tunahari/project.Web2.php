$(document).ready(function () {
  const ClassFuction = new HandlingFunctions();
  const ClassValidate = new ValidateData();
  $(".loading__box, .loading__bg ").hide();

  /* Hiển thị sản phẩm trong giỏ hàng */
  function fetchCart() {
      ClassFuction.getAjaxPost(
          "../../Controller/product/controller-product.product.php",
          {
              fetchCart: "fetch-cart",
          }
      ).done(function (response) {
          response = response.trim();
          $(".order-details").html(response);
          calculateAndDisplayTotals(); // Tính toán và hiển thị sau khi tải dữ liệu
      });
  }

  fetchCart();

  /* Hiển thị số lượng sản phẩm trong giỏ hàng */
  function fetchQuantityCart() {
      ClassFuction.getAjaxPost(
          "../../Controller/product/controller-product.product.php",
          {
              fetchQuantityCart: "fetch-quantity-cart",
          }
      ).done(function (response) {
          response = response.trim();
          $(".Product__Page__Header__Bottom__Cart__Quantity").html(response);
          if (response > 0) {
              $(".createBillBox").html('<div id="createBill">Đặt Hàng</div>');
          } else {
              $(".createBillBox").html("");
          }
      });
  }

  fetchQuantityCart();
  /* ================================== Tính toán Tổng Giá và Tổng Số Lượng ================================== */
  function calculateAndDisplayTotals() {
      let totalQuantity = 0;
      let totalPrice = 0;

      // Duyệt qua từng hàng sản phẩm trong .order-details
      $(".order-row").each(function () {
          const quantity = parseInt($(this).find("#quantityCartRight").val());
          const priceText = $(this).find(".sale-price").text().replace(/[^0-9]/g, ''); // Loại bỏ các ký tự không phải số
          const price = parseInt(priceText);


          totalQuantity += quantity;
          totalPrice += (quantity * price);
      });
      //Cập nhật cho phần summary-more
      $("#totalQuantity").text(totalQuantity);
      $("#totalPrice").text(totalPrice.toLocaleString() + ' VND');
      //giá trị mặc định của tổng cộng bằng 0
      $("#totalPriceBill").text('0 VND');
  }
  /* ================================== Tính toán Tổng Giá và Tổng Số Lượng ================================== */

// Hàm xóa giỏ hàng
function deleteCartAfterOrder() {
    $.ajax({
        url: '../../Controller/product/controller-product.product.php', // Đường dẫn đến file PHP xử lý xóa giỏ hàng
        type: 'POST',
        data: {
            deleteCart: 'delete-cart' // Gửi yêu cầu xóa giỏ hàng
        },
        success: function(response) {
            console.log(response); // In ra phản hồi từ server (có thể dùng để debug)
            response = response.trim();
            if (response === 'success') {
                // Xóa giỏ hàng thành công
                console.log('Xóa giỏ hàng thành công!');
                // reload lại trang
                // location.reload();
            } else {
                // Xóa giỏ hàng thất bại
                console.error('Xóa giỏ hàng thất bại!');
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            console.error('Lỗi khi xóa giỏ hàng!');
        }
    });
}

  /* ================================== Tạo đơn hàng ================================== */
  $(document).on("click", "#createBill", function () {
    // Hiển thị loading ngay khi nhấn nút
    $(".loading__box, .loading__bg").show();

    ClassFuction.getAjaxPost(
        "../../Controller/product/controller-product.product.php",
        {
            createBill: "create-bill",
        }
    ).done(function (response) {
        response = response.trim();
        var dataReponse;

        try {
            dataReponse = JSON.parse(response);
        } catch (e) {
            $(".loading__box, .loading__bg").hide(); // Ẩn loading nếu lỗi parse
            console.error("Lỗi phân tích JSON từ createBill:", e);
            console.error("Dữ liệu nhận được:", response);
            alertFailed("Có lỗi xảy ra khi lấy thông tin đơn hàng. Vui lòng thử lại.");
            return; // Dừng thực thi
        }

        // ----> BẮT ĐẦU KIỂM TRA KẾT QUẢ fetchPhoneAddress <----
        // dataReponse[6] chứa kết quả của fetchPhoneAddress()
        if (dataReponse[6].trim() === '') {
            // Trường hợp 1: Thông tin địa chỉ/SĐT ĐÃ ĐẦY ĐỦ (fetchPhoneAddress trả về chuỗi rỗng)
            alertSuccess("Tạo đơn hàng thành công, kiểm tra thông tin dưới để XÁC NHẬN"); // Thông báo thành công

            // Tiếp tục hiển thị chi tiết đơn hàng và nút Xác nhận
            // (Có thể đặt trong setTimeout nếu muốn có độ trễ nhỏ)
            // $(".loading__box, .loading__bg").show(); // Loading đã hiển thị rồi
            setTimeout(function () {
                $(".loading__box, .loading__bg").hide(); // Ẩn loading
                $(".order-details").html(dataReponse[0]);
                $("#priceBill").html(dataReponse[1] + " VND");
                // $("#priceSale").html(dataReponse[2] + " VND"); // Dòng này đã bị comment ở cart.view.php
                $("#numberItem").html(dataReponse[3]);
                $("#numberProduct").html(dataReponse[4]);
                $("#totalPriceBill").html(dataReponse[5] + " VND");
                $("#fetchCheckout").html(dataReponse[6]); // Vẫn cập nhật (sẽ là chuỗi rỗng)

                // Xóa nút cũ (nếu có) và thêm nút mới
                $("#saveBill").remove();
                $(".summary-save").append(
                    `<div id="saveBill">XÁC NHẬN ĐẶT HÀNG</div>`
                );
                $("#createBill").hide();
                $(".order-tittle h4").text("Danh Sách Sản Phẩm Đơn Hàng");

                // Gắn sự kiện click cho nút #saveBill (logic kiểm tra bên trong giữ nguyên như cũ)
                $('#saveBill').off('click').on('click', function () {
                    // ----> KIỂM TRA THÔNG TIN CẬP NHẬT (NẾU CÓ FORM HIỂN THỊ - logic này giờ không cần thiết ở đây nữa vì đã kiểm tra ở bước trước)
                    // if ($('#updateAddressCart').length > 0) { ... } // Có thể bỏ đoạn kiểm tra này trong #saveBill

                    // Chỉ cần lấy địa chỉ tạm (nếu có) và gửi đi
                    $(".loading__box, .loading__bg").show();
                    let diaChiTam = $("#diachi_tam").val().trim();

                    ClassFuction.getAjaxPost(
                        "../../Controller/admin/controller-admin.order.php",
                        {
                            saveBill: "save-bill",
                            diachi_tam: diaChiTam,
                        }
                    ).done(function (responseSave) { // Đổi tên biến response để tránh trùng
                        responseSave = responseSave.trim();
                        if (responseSave !== "failed") {
                            $(".loading__box, .loading__bg").hide();
                            alertSuccess("Đặt hàng thành công, đang chuyển hướng về trang chủ");
                            deleteCartAfterOrder();

                            setTimeout(function () {
                                fetchCart();
                                fetchQuantityCart();
                                $(".order-details").html('');
                                $("#priceBill").html("0 VND");
                                // $("#priceSale").html("0 VND");
                                $("#numberItem").html(0);
                                $("#numberProduct").html(0);
                                $("#totalPriceBill").html("0 VND");
                                $("#saveBill").remove();
                                $("#fetchCheckout").html('');
                                window.location.href = "../../View/product/product.view.php";
                            }, 2000);
                        } else {
                            $(".loading__box, .loading__bg").hide();
                            alertFailed("Đặt hàng thất bại");
                        }
                    }).fail(function() {
                        $(".loading__box, .loading__bg").hide();
                        alertFailed("Lỗi kết nối khi lưu đơn hàng.");
                    });
                }); // Kết thúc click #saveBill
            }, 100); // Độ trễ nhỏ để hiển thị alert trước khi cập nhật UI

        } else {
            // Trường hợp 2: Thông tin địa chỉ/SĐT CHƯA ĐẦY ĐỦ (fetchPhoneAddress trả về HTML form)
            $(".loading__box, .loading__bg").hide(); // Ẩn loading
            alertFailed("Thất bại, Vui lòng cập nhật đủ thông tin"); // Thông báo lỗi

            // Hiển thị form yêu cầu cập nhật thông tin
            $("#fetchCheckout").html(dataReponse[6]);

            // KHÔNG hiển thị chi tiết đơn hàng và nút "Xác nhận đặt hàng"
            // Không làm gì thêm ở đây
        }
        // ----> KẾT THÚC KIỂM TRA KẾT QUẢ fetchPhoneAddress <----

    }).fail(function() {
        // Xử lý lỗi nếu AJAX createBill thất bại
        $(".loading__box, .loading__bg").hide();
        alertFailed("Không thể lấy thông tin đơn hàng. Vui lòng thử lại.");
    }); // Kết thúc done của createBill
});
/* ================================== Tạo đơn hàng ================================== */


  /* Xóa sản phẩm trong giỏ hàng */
  $(document).on("click", "#deleteCart", function (e) {
      if (confirm("Xóa sản phẩm khỏi giỏ hàng")) {
          e.preventDefault();
          var idProduct = $(this).attr("value");
          ClassFuction.getAjaxPost(
              "../../Controller/product/controller-product.product.php",
              {
                  deleteCart: "delete-cart",
                  idProduct: idProduct,
              }
          ).done(function (response) {
              response = response.trim();
              if (response === "success") {
                  $(".loading__box, .loading__bg ").show();
                  setTimeout(function () {
                      $(".loading__box, .loading__bg ").hide();
                      fetchCart();
                      fetchQuantityCart();
                      alertSuccess("Xóa sản phẩm khỏi giỏ hàng thành công");
                  }, 1000);
              }
          });
      } else {
          return false;
      }
  });

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

  /* ================================== Tăng giảm số lượng và cập nhật ================================== */
  // Gom chung sự kiện change và click vào một hàm
  function updateQuantityCart(quantityCart, idProduct) {
      $.ajax({
          url: "../../Controller/product/controller-product.product.php",
          method: "POST",
          data: { updateItem: "update-item", productID: idProduct, newQuantity: quantityCart },
          dataType: "json",
          success: function (response) {
              if (response.status !== 'error') {
                  $(`.total-price[data-product-id="${idProduct}"]`).text(response.newTotalPrice.toLocaleString('vi-VN'));
                  calculateAndDisplayTotals();
              } else {
                  console.error("Lỗi cập nhật:", response.message);
              }
          },
          error: function (error) {
              console.error("Lỗi AJAX:", error);
          }
      });
  }

  // Sự kiện khi thay đổi số lượng bằng cách nhập trực tiếp
  $(document).on("keyup", "#quantityCartRight, #quantityCartLeft", function () {
      let quantityCart = $(this).val();
      let idProduct = $(this).attr("data-productID");
      if (quantityCart < 1) {
          $(this).val(1);
          quantityCart = 1;
      }
      updateQuantityCart(quantityCart, idProduct);
  });

  // Sự kiện khi tăng số lượng
  $(document).on("click", "#increaseQuantityRight, #increaseQuantityLeft", function () {
      let quantityCart = parseInt($($(this).parent().parent().children().get(0)).val());
      let idProduct = $($(this).parent().parent().children().get(0)).attr("data-productID");
      updateQuantityCart(quantityCart + 1, idProduct);
      $($(this).parent().parent().children().get(0)).val(quantityCart + 1); //cập nhật giao diện
  });
  

  // Sự kiện khi giảm số lượng
  $(document).on("click", "#decreaseQuantityRight, #decreaseQuantityLeft", function () {
    let quantityCart = parseInt($($(this).parent().parent().children().get(0)).val());
    let idProduct = $($(this).parent().parent().children().get(0)).attr("data-productID");
    if (quantityCart > 1) {
        updateQuantityCart(quantityCart - 1, idProduct); // Call updateQuantityCart to send AJAX request
        $($(this).parent().parent().children().get(0)).val(quantityCart - 1); // Cập nhật giao diện
    }
});

  /* ================================== Tăng giảm số lượng và cập nhật ================================== */
});
