<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    Here are HOME
    null
</body>
</html>
<script>
    setInterval(checkAccountStatus, 5000); // Kiểm tra trạng thái mỗi 5 giây

    function checkAccountStatus() {
        $.ajax({
            url: 'lock-account.php', // Đường dẫn đến file lock-account.php
            type: 'POST',
            data: {
                checkStatus: true
            }, // gửi tín hiệu để server chỉ kiểm tra trạng thái
            dataType: 'json',
            success: function(response) {
                if (response.locked === true) { // Kiểm tra xem tài khoản có bị khóa không
                    window.location.href = '404.php'; // Chuyển hướng đến 404.php
                }
            },
            error: function(error) {
                console.error('Lỗi kiểm tra trạng thái tài khoản:', error);
            }
        });
    }
</script>