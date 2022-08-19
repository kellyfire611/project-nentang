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

</head>

<body class="d-flex flex-column h-100">
  <!-- header -->
  <?php include_once(__DIR__ . '/../../frontend/layouts/partials/header.php'); ?>
  <!-- end header -->

  <main role="main" class="mb-2">
    <!-- Block content -->
    <div class="container mt-2">
      <h1 class="text-center">Nền tảng - Hành trang tới Tương lai</h1>
      <h1 class="tieu-de">Quản lý Danh sách Tập Truyện tranh</h1>

      <?php
      // Hiển thị tất cả lỗi trong PHP
      // Chỉ nên hiển thị lỗi khi đang trong môi trường Phát triển (Development)
      // Không nên hiển thị lỗi trên môi trường Triển khai (Production)
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      // Truy vấn database để lấy danh sách
      // 1. Include file cấu hình kết nối đến database, khởi tạo kết nối $conn
      include_once(__DIR__ . '/../../dbconnect.php');

      // 2. Chuẩn bị câu truy vấn $sql
      $sqlDanhSachTapTruyen = <<<EOT
      SELECT c.chuong_id, c.chuong_so, c.chuong_ten, c.chuong_id,
        t.truyen_ten
      FROM chuong c
      JOIN truyen t ON c.truyen_id = t.truyen_id
      WHERE t.truyen_loai = 2
      ORDER BY t.truyen_ten, c.chuong_so;
EOT;

      // 3. Thực thi câu truy vấn SQL để lấy về dữ liệu
      $result = mysqli_query($conn, $sqlDanhSachTapTruyen);

      // 4. Khi thực thi các truy vấn dạng SELECT, dữ liệu lấy về cần phải phân tích để sử dụng
      // Thông thường, chúng ta sẽ sử dụng vòng lặp while để duyệt danh sách các dòng dữ liệu được SELECT
      // Ta sẽ tạo 1 mảng array để chứa các dữ liệu được trả về
      $dataDanhSachTapTruyenTranh = [];
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $dataDanhSachTapTruyenTranh[] = array(
          'chuong_id' => $row['chuong_id'],
          'chuong_so' => $row['chuong_so'],
          'chuong_ten' => $row['chuong_ten'],
          'chuong_id' => $row['chuong_id'],
          'truyen_ten' => $row['truyen_ten'],
        );
      }
      // print_r($dataDanhSachTapTruyenTranh);die;
      ?>
    </div>

    <div class="container">
      <!-- Danh sách TRUYỆN TRANH START -->
      <div class="row">
        <div class="col">
          <h3 class="text-truyen-tranh">Danh sách Tập Truyện Tranh</h3>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <!-- Đường link chuyển sang trang Thêm mới -->
          <a href="them.php" class="btn btn-primary mb-2">Thêm mới</a>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tập số</th>
                <th>Tên tập</th>
                <th>Tên Truyện</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dataDanhSachTapTruyenTranh as $tap) : ?>
                <tr>
                  <td><?= $tap['chuong_id'] ?></td>
                  <td><?= $tap['chuong_so'] ?></td>
                  <td><?= $tap['chuong_ten'] ?></td>
                  <td><?= $tap['truyen_ten'] ?></td>
                  <td>
                    <!-- Nút sửa, bấm vào sẽ hiển thị form hiệu chỉnh thông tin dựa vào khóa chính `chuong_id` -->
                    <a href="sua.php?chuong_id=<?= $tap['chuong_id'] ?>" class="btn btn-warning">
                      Sửa
                    </a>
                    <!-- Nút xóa, bấm vào sẽ xóa thông tin dựa vào khóa chính `chuong_id` -->
                    <button type="button" class="btn btn-danger btnDelete" data-chuong_id="<?= $tap['chuong_id'] ?>">
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
    // Lấy giá trị của thuộc tính "data-chuong_id" của nút mà người dùng đang click
    var chuong_id = $(this).attr('data-chuong_id');

    // Hiển thị cảnh báo
    var xacNhanXoa = confirm('Bạn có chắc chắn muốn xóa?');
    if(xacNhanXoa == true) { // Người dùng đã chọn Yes
      // Điều hướng đến trang xoa.php với tham số chuong_id được truyền theo request GET
      location.href = 'xoa.php?chuong_id=' + chuong_id;
    }
  });
  </script>
</body>

</html>