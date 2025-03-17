<?php
@session_start(); // Make sure sessions are started

function checkAccountStatusAndRedirect(){
    // Database connection (Get this from your existing connection)
    global $conn; // You need to make your database connection available globally

    if (isset($_SESSION['email'])) {
        //  var_dump($_SESSION);
        // Retrieve the user's ID (How do you get the user ID from email?  You'll need a query for that).
        $sql = "SELECT KH_IDKhachHang, KH_XoaKhachHang FROM customer WHERE KH_EmailKhachHang = ?";
        $stmt = $conn->prepare($sql); // Use prepared statements to prevent SQL injection!
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $kh_xoaKhachHang = $row['KH_XoaKhachHang'];
            if ($kh_xoaKhachHang === '1') {
                session_destroy(); // Destroy the session after redirecting for security.
                header("Location: 404.php");
                exit;
            }
        } else {
            //Handle the case where the user is not found in the database.  This is unusual but should be addressed.
             session_destroy(); // Destroy the session
             header("Location: 404.php");
             exit;
        }
        $stmt->close();
    }

}
?>