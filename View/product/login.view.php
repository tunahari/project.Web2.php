<?php if (isset($_SESSION['login_error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['login_error'];
        unset($_SESSION['login_error']); ?>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="./login.view.css">

    <title>Login SB Mobile</title>
</head>
<style>
    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        width: 100%;
        padding-right: 40px;
        /* chừa chỗ cho icon */
    }

    .password-wrapper svg {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>

<body>
    <div class="Account__Login__Container">
        <div class="Account__Login__Left"></div>
        <div class="Account__Login__Right">
            <!-- Alert Failed -->
            <div class="Account__Login__Right__Alert__Failed">
                <div class="Account__Login__Right__Alert__Failed__Left">
                    <div class="Account__Login__Right__Alert__Failed__Left__Icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
                <div class="Account__Login__Right__Alert__Failed__Right">
                    <div class="Account__Login__Right__Alert__Failed__Notify">
                        Login Failed!
                    </div>
                    <div class="Account__Login__Right__Alert__Failed__Text">
                        Username or password incorrect
                    </div>
                </div>
                <div class="Account__Login__Right__Alert__Failed__Process__Box">
                    <div class="Account__Login__Right__Alert__Failed__Process"></div>
                </div>
            </div>
            <!-- Alert Success -->
            <div class="Account__Login__Right__Alert__Success">
                <div class="Account__Login__Right__Alert__Success__Left">
                    <div class="Account__Login__Right__Alert__Success__Left__Icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="Account__Login__Right__Alert__Success__Right">
                    <div class="Account__Login__Right__Alert__Success__Notify">
                        Đăng nhập thành công
                    </div>
                    <div class="Account__Login__Right__Alert__Success__Text">
                        Bây giờ bạn có thể mua hàng tại SB Mobile
                    </div>
                </div>
                <div class="Account__Login__Right__Alert__Success__Process__Box">
                    <div class="Account__Login__Right__Alert__Success__Process"></div>
                </div>
            </div>
            <div class="Account__Login__Right__Loading"><i class="fa-solid fa-spinner"></i></div>
            <div class="Account__Login__Right__Title__Box">
                <div class="Account__Login__Right__Title">
                    SB Moblie Xin Chào!
                </div>
                <div class="Account__Login__Right__Login__Text">
                    Đăng nhập để tiếp tục mua hàng
                </div>
            </div>
            <div class="Account__Login__Right__Input__Box">
                <div class="Account__Login__Right__Input__Item">
                    <label for="">Email</label>
                    <input type="text" name="" id="loginEmail" placeholder="Viết email của bạn vào đây...">
                </div>
                <div class="Account__Login__Right__Input__Item">
                    <label for="">Mật Khẩu</label>
                    <div class="password-wrapper Account__Login__Right__Input__Item">
                    <input type="password" name="" id="loginPass" placeholder="Viết mật khẩu của bạn vào đây...">

                        <!-- SVG mở mắt -->
                        <svg id="openEye" onclick="togglePassword()" xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                            <path
                                d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
                        </svg>

                        <!-- SVG gạch chéo mắt -->
                        <svg id="closeEye" onclick="togglePassword()" xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#1f1f1f" style="display: none;">
                            <path
                                d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z" />
                        </svg>
                    </div>

                </div>
            </div>
            <div class="Account__Login__Right__Remember__Box">
                <div class="Account__Login__Right__Remember__Box__Left">
                    <label for="RememberCheck"><i class="fa-solid fa-check"></i></label>
                    <span id="RememberCheck">Ghi nhớ đăng nhập</span>
                    <input type="checkbox" id="RememberCheck">
                </div>
                <div class="Account__Login__Right__Remember__Box__Right">
                    <a href="#">Quên mật khẩu?</a>
                </div>
            </div>
            <div class="Account__Login__Right__Login__Button" id="loginSubmit">Đăng Nhập</div>
            <div class="Account__Login__Right__Login__Or">
                <span>HOẶC</span>
            </div>
            <div class="Account__Login__Right__Login__Group">
                <div class="Account__Login__Right__Login__Google">
                    <img class="Account__Login__Right__Login__Google__Image" src="./image/login-gg-icon.png" alt="">
                    ĐĂNG NHẬP VỚI GOOGLE
                </div>
                <div class="Account__Login__Right__Login__Facebook">
                    <img class="Account__Login__Right__Login__Facebook__Image" src="./image/login-fb-icon.png" alt="">
                    ĐĂNG NHẬP VỚI FACEBOOK
                </div>
            </div>
            <div class="Account__Login__Right__Have__Account">
                Chưa có tài khoản? <a href="./register.view.php">Đăng Ký ngay</a>
            </div>
        </div>
    </div>

    <!-- LOADING -->
    <div class="loading__bg"></div>
    <div class="loading__box">
        <p>Loading...</p>
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
    <script>
        function togglePassword() {
    var passwordInput = document.getElementById("loginPass");
    var openEye = document.getElementById("openEye");
    var closeEye = document.getElementById("closeEye");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        openEye.style.display = "none";
        closeEye.style.display = "block";
    } else {
        passwordInput.type = "password";
        openEye.style.display = "block";
        closeEye.style.display = "none";
    }
}

    </script>
</body>

</html>
<script src="./login.view.js"></script>
<script src="../../Controller/class/controller.function.js"></script>
<script src="../../Controller/class/controller.validate.js"></script>
<script src="../../Controller/product/controller-product.login.js"></script>