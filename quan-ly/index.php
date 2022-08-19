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
  <?php include_once(__DIR__ . '/../frontend/layouts/styles.php'); ?>

  <link href="/project-nentang/assets/frontend/css/style.css" type="text/css" rel="stylesheet" />
</head>

<body class="d-flex flex-column h-100">
  <!-- header -->
  <?php include_once(__DIR__ . '/../frontend/layouts/partials/header.php'); ?>
  <!-- end header -->

  <main role="main" class="mb-2">
    <!-- Block content -->
    <div class="container mt-2">
      <h1 class="text-center">Nền tảng - Hành trang tới Tương lai</h1>
      <h1 class="tieu-de">Quản lý Hệ thống Web Truyện tranh và Tiểu thuyết</h1>
    </div>

    <div class="container">
      <h2>Các chức năng Quản lý Truyện tranh</h2>
      <ul>
        <li>Truyện tranh
          <ol>
            <li><a href="/project-nentang/quan-ly/truyen-tranh/danh-sach.php">Danh sách Truyện tranh</a></li>
            <li><a href="/project-nentang/quan-ly/truyen-tranh/them.php">Thêm mới Truyện tranh</a></li>
          </ol>
        </li>
        <li>Tập Truyện tranh
          <ol>
            <li><a href="/project-nentang/quan-ly/truyen-tranh-tap/danh-sach.php">Danh sách Tập Truyện tranh</a></li>
            <li><a href="/project-nentang/quan-ly/truyen-tranh-tap/them.php">Thêm mới Tập Truyện tranh</a></li>
          </ol>
        </li>
        <li>Tập Hình ảnh Truyện tranh
          <ol>
            <li><a href="/project-nentang/quan-ly/truyen-tranh-tap-hinh-anh/danh-sach.php">Danh sách Tập Hình ảnh Truyện tranh</a></li>
            <li><a href="/project-nentang/quan-ly/truyen-tranh-tap-hinh-anh/them.php">Thêm mới Tập Hình ảnh Truyện tranh</a></li>
          </ol>
        </li>
        <li>Tiểu thuyết
          <ol>
            <li><a href="/project-nentang/quan-ly/tieu-thuyet/danh-sach.php">Danh sách Tiểu thuyết</a></li>
            <li><a href="/project-nentang/quan-ly/tieu-thuyet/them.php">Thêm mới Tiểu thuyết</a></li>
          </ol>
        </li>
        <li>Chương Tiểu thuyết
          <ol>
            <li><a href="/project-nentang/quan-ly/tieu-thuyet-chuong/danh-sach.php">Danh sách Chương Tiểu thuyết</a></li>
            <li><a href="/project-nentang/quan-ly/tieu-thuyet-chuong/them.php">Thêm mới Chương Tiểu thuyết</a></li>
          </ol>
        </li>
      </ul>
    </div>
    <!-- End block content -->
  </main>

  <!-- footer -->
  <?php include_once(__DIR__ . '/../frontend/layouts/partials/footer.php'); ?>
  <!-- end footer -->

  <!-- Nhúng file quản lý phần SCRIPT JAVASCRIPT -->
  <?php include_once(__DIR__ . '/../frontend/layouts/scripts.php'); ?>

  <!-- Các file Javascript sử dụng riêng cho trang này, liên kết tại đây -->

</body>

</html>