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

  /* ================================== Tạo đơn hàng ================================== */
  $(document).on("click", "#createBill", function () {
      ClassFuction.getAjaxPost(
          "../../Controller/product/controller-product.product.php",
          {
              createBill: "create-bill",
          }
      ).done(function (response) {
          response = response.trim();
          var dataReponse = JSON.parse(response);
          $(".loading__box, .loading__bg").show();
          setTimeout(function () {
              $(".loading__box, .loading__bg").hide();
              $(".order-details").html(dataReponse[0]);
              $("#priceBill").html(dataReponse[1] + " VND");
              $("#priceSale").html(dataReponse[2] + " VND");
              $("#numberItem").html(dataReponse[3]);
              $("#numberProduct").html(dataReponse[4]);
              $("#totalPriceBill").html(dataReponse[5] + " VND");
              $("#fetchCheckout").html(dataReponse[6]);
              $(".summary-save").append(
                  `<div id="saveBill">XÁC NHẬN ĐẶT HÀNG</div>`
              );
              $("#createBill").hide();
              $(".order-tittle h4").text("Danh Sách Sản Phẩm Đơn Hàng");
              alertSuccess("Đã tạo thành công đơn hàng");
              $("#saveBill").click(function () {
                  $(".loading__box, .loading__bg").show();
                  ClassFuction.getAjaxPost(
                      "../../Controller/admin/controller-admin.order.php",
                      {
                          saveBill: "save-bill",
                      }
                  ).done(function (response) {
                      response = response.trim();
                      if (response !== "failed") {
                          $(".loading__box, .loading__bg").hide();
                          alertSuccess("Đặt hàng thành công, đang chuyển hướng về trang chủ");
                          setTimeout(function () {
                              fetchCart();
                              fetchQuantityCart();
                              $(".order-details").html('');
                              $("#priceBill").html("0 VND");
                              $("#priceSale").html("0 VND");
                              $("#numberItem").html(0);
                              $("#numberProduct").html(0);
                              $("#totalPriceBill").html("0 VND");
                              $("#saveBill").remove();
                              window.location.href = "../../View/product/product.view.php";
                          }, 2000); // Chuyển hướng sau 2 giây
                      } else {
                          $(".loading__box, .loading__bg").hide();
                          alertFailed("Đặt hàng thất bại");

                      }
                  });
              });
          },);
      });
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
