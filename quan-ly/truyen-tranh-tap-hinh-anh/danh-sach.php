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
  <style>
    .hinh-truyen-tranh {
      width: 150px;
    }
  </style>
</head>

<body class="d-flex flex-column h-100">
  <!-- header -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/partials/header.php'); ?>
  <!-- end header -->

  <main role="main" class="mb-2">
    <!-- Block content -->
    <div class="container mt-2">
      <h1 class="text-center">Nền tảng - Hành trang tới Tương lai</h1>
      <h1 class="tieu-de">Quản lý Danh sách Hình ảnh Tập Truyện tranh</h1>

      <?php
      // Hiển thị tất cả lỗi trong PHP
      // Chỉ nên hiển thị lỗi khi đang trong môi trường Phát triển (Development)
      // Không nên hiển thị lỗi trên môi trường Triển khai (Production)
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      // Thu thập thông tin từ Request gởi đến
      $chuong_id = $_GET['chuong_id'];
      $chuong_ten = $_GET['chuong_ten'];

      // Truy vấn database để lấy danh sách
      // 1. Include file cấu hình kết nối đến database, khởi tạo kết nối $conn
      include_once(__DIR__ . '/../../dbconnect.php');

      // 2. Chuẩn bị câu truy vấn $sql
      $sqlDanhSachHinhAnhTruyenTranh = <<<EOT
      SELECT chuong_hinhanh_id, chuong_id, chuong_hinhanh_tenhinh
      FROM chuong_hinhanh
      WHERE chuong_id = $chuong_id
      ORDER BY chuong_hinhanh_id ASC
EOT;

      // 3. Thực thi câu truy vấn SQL để lấy về dữ liệu
      $result = mysqli_query($conn, $sqlDanhSachHinhAnhTruyenTranh);

      // 4. Khi thực thi các truy vấn dạng SELECT, dữ liệu lấy về cần phải phân tích để sử dụng
      // Thông thường, chúng ta sẽ sử dụng vòng lặp while để duyệt danh sách các dòng dữ liệu được SELECT
      // Ta sẽ tạo 1 mảng array để chứa các dữ liệu được trả về
      $dataDanhSachHinhAnhTruyenTranh = [];
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $dataDanhSachHinhAnhTruyenTranh[] = array(
          'chuong_hinhanh_id' => $row['chuong_hinhanh_id'],
          'chuong_id' => $row['chuong_id'],
          'chuong_hinhanh_tenhinh' => $row['chuong_hinhanh_tenhinh'],
        );
      }
      ?>
    </div>

    <div class="container">
      <!-- Danh sách TRUYỆN TRANH START -->
      <div class="row">
        <div class="col">
          <h3 class="text-truyen-tranh">Danh sách Hình ảnh Tập Truyện Tranh</h3>
          <h3><?= $chuong_ten ?></h3>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <!-- Đường link chuyển sang trang Thêm mới -->
          <a href="them.php?chuong_id=<?= $chuong_id ?>&chuong_ten=<?= $chuong_ten ?>" class="btn btn-primary mb-2">Thêm mới</a>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Hình</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dataDanhSachHinhAnhTruyenTranh as $ha) : ?>
                <tr>
                  <td><?= $ha['chuong_hinhanh_id'] ?></td>
                  <td>
                    <img src="/project-nentang/assets/uploads/<?= $ha['chuong_hinhanh_tenhinh'] ?>" class="hinh-truyen-tranh" />
                  </td>
                  <td>
                    <!-- Nút sửa, bấm vào sẽ hiển thị form hiệu chỉnh thông tin dựa vào khóa chính `chuong_hinhanh_id` -->
                    <a href="sua.php?chuong_hinhanh_id=<?= $ha['chuong_hinhanh_id'] ?>&chuong_id=<?= $chuong_id ?>&chuong_ten=<?= $chuong_ten ?>" class="btn btn-warning">
                      Sửa
                    </a>
                    <!-- Nút xóa, bấm vào sẽ xóa thông tin dựa vào khóa chính `chuong_hinhanh_id` -->
                    <button type="button" class="btn btn-danger btnDelete" data-chuong_hinhanh_id="<?= $ha['chuong_hinhanh_id'] ?>" data-chuong_id="<?= $chuong_id ?>" data-chuong_ten="<?= $chuong_ten ?>">
                      Xóa
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Danh sách TRUYỆN TRANH END -->
    </div>
    <!-- End block content -->
  </main>

  <!-- footer -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/partials/footer.php'); ?>
  <!-- end footer -->

  <!-- Nhúng file quản lý phần SCRIPT JAVASCRIPT -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/scripts.php'); ?>

  <!-- Các file Javascript sử dụng riêng cho trang này, liên kết tại đây -->
  <script>
    $('.btnDelete').on('click', function(e) {
      // Lấy giá trị của thuộc tính "data-chuong_hinhanh_id" của nút mà người dùng đang click
      var chuong_hinhanh_id = $(this).attr('data-chuong_hinhanh_id');
      var chuong_id = $(this).attr('data-chuong_id');
      var chuong_ten = $(this).attr('data-chuong_ten');

      // Hiển thị cảnh báo
      var xacNhanXoa = confirm('Bạn có chắc chắn muốn xóa?');
      if (xacNhanXoa == true) { // Người dùng đã chọn Yes
        // Điều hướng đến trang xoa.php với tham số chuong_hinhanh_id được truyền theo request GET
        location.href = 'xoa.php?chuong_hinhanh_id=' + chuong_hinhanh_id + '&chuong_id=' + chuong_id + '&chuong_ten=' + chuong_ten;
      }
    });
  </script>
</body>

</html>