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
  <?php include_once(__DIR__ . '/../../frontend/layouts/styles.php'); ?>

  <link href="/project-nentang/assets/vendor/dropzone/dropzone.min.css" type="text/css" rel="stylesheet" />
</head>

<body class="d-flex flex-column h-100">
  <!-- header -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/partials/header.php'); ?>
  <!-- end header -->

  <main role="main" class="mb-2">
    <!-- Block content -->
    <div class="container mt-2">
      <h1 class="text-center">Nền tảng - Hành trang tới Tương lai</h1>
      <h1 class="tieu-de">Quản lý Thêm mới Hình ảnh cho tập Truyện tranh</h1>
    </div>

    <div class="container">
      <!-- Form TRUYỆN TRANH START -->
      <div class="row">
        <div class="col">
          <h3 class="text-truyen-tranh">Thêm mới Hình ảnh Tập Truyện Tranh</h3>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <form name="frmThemMoi" id="frmThemMoi" method="post" action="xuly-upload.php" enctype="multipart/form-data" class="dropzone">
            <input type="hidden" name="chuong_id" id="chuong_id" value="<?= $_GET['chuong_id'] ?>" />
            <div class="form-group">
              <label for="">Tên tập truyện</label>
              <input type="text" name="chuong_ten" id="chuong_ten" value="<?= $_GET['chuong_ten'] ?>" class="form-control" disabled />
            </div>
          </form>
          <a href="/project-nentang/quan-ly/truyen-tranh-tap/danh-sach.php" class="btn btn-secondary">Quay về Danh sách Tập truyện tranh</a>
        </div>
      </div>
      <!-- End block content -->
  </main>

  <!-- footer -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/partials/footer.php'); ?>
  <!-- end footer -->

  <!-- Nhúng file quản lý phần SCRIPT JAVASCRIPT -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/scripts.php'); ?>

  <script src="/project-nentang/assets/vendor/dropzone/dropzone.min.js"></script>

  <!-- Các file Javascript sử dụng riêng cho trang này, liên kết tại đây -->
  <script>
    // Note that the name "frmThemMoi" is the camelized
    // id of the form.
    Dropzone.options.frmThemMoi = {
      paramName: "chuong_hinhanh_tenhinh", // The name that will be used to transfer the file
      maxFilesize: 2, // MB
      accept: function(file, done) {
        done();
      }
    };
  </script>

</body>

</html>