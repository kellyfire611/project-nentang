<?php
// hàm `session_id()` sẽ trả về giá trị SESSION_ID (tên file session do Web Server tự động tạo)
// - Nếu trả về Rỗng hoặc NULL => chưa có file Session tồn tại
if (session_id() === '') {
    // Yêu cầu Web Server tạo file Session để lưu trữ giá trị tương ứng với CLIENT (Web Browser đang gởi Request)
    session_start();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NenTang.vn</title>

    <!-- Nhúng file Quản lý các Liên kết CSS dùng chung cho toàn bộ trang web -->
    <?php include_once(__DIR__ . '/frontend/layouts/styles.php'); ?>

    <link href="/project-nentang/assets/frontend/css/style.css" type="text/css" rel="stylesheet" />
</head>

<body class="d-flex flex-column h-100">
    <!-- header -->
    <?php include_once(__DIR__ . '/frontend/layouts/partials/header.php'); ?>
    <!-- end header -->

    <main role="main" class="mb-2">
        <!-- Block content -->
        <div class="container mt-2">
          <h1 class="text-center">Nền tảng - Hành trang tới Tương lai</h1>
          <h1 class="tieu-de">Quản trị Hệ thống</h1>
        </div>
        <!-- End block content -->
    </main>

    <!-- footer -->
    <?php include_once(__DIR__ . '/frontend/layouts/partials/footer.php'); ?>
    <!-- end footer -->

    <!-- Nhúng file quản lý phần SCRIPT JAVASCRIPT -->
    <?php include_once(__DIR__ . '/frontend/layouts/scripts.php'); ?>

    <!-- Các file Javascript sử dụng riêng cho trang này, liên kết tại đây -->

</body>

</html>