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

</head>

<body class="d-flex flex-column h-100">
  <!-- header -->
  <?php include_once(__DIR__ . '/../frontend/layouts/partials/header.php'); ?>
  <!-- end header -->

  <main role="main" class="mb-2">
    <!-- Block content -->
    <div class="container mt-2">
      <h1 class="text-center">Nền tảng - Hành trang tới Tương lai</h1>
      <h1 class="tieu-de">Web tổng hợp Truyện tranh và Tiểu thuyết Online 24/7</h1>

      <?php
      // Hiển thị tất cả lỗi trong PHP
      // Chỉ nên hiển thị lỗi khi đang trong môi trường Phát triển (Development)
      // Không nên hiển thị lỗi trên môi trường Triển khai (Production)
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      // Truy vấn database để lấy danh sách
      // 1. Include file cấu hình kết nối đến database, khởi tạo kết nối $conn
      include_once(__DIR__ . '/../dbconnect.php');

      /* --- 
        --- 2.Truy vấn dữ liệu Truyện
        --- Lấy giá trị khóa chính được truyền theo dạng QueryString Parameter key1=value1&key2=value2...
        --- 
        */
      // 2.1. Chuẩn bị câu truy vấn $sql
      $truyen_id = $_GET['truyen_id'];

      $sqlDanhSachTruyen = <<<EOT
      SELECT truyen_id, truyen_ma, truyen_ten, truyen_hinhdaidien, truyen_loai, truyen_theloai, truyen_tacgia, truyen_mota_ngan, truyen_ngaydang
      FROM truyen
      WHERE truyen_id = $truyen_id;
EOT;

      // 2.2. Thực thi câu truy vấn SQL để lấy về dữ liệu
      $resultTruyen = mysqli_query($conn, $sqlDanhSachTruyen);

      // 2.3. Khi thực thi các truy vấn dạng SELECT, dữ liệu lấy về cần phải phân tích để sử dụng
      // Thông thường, chúng ta sẽ sử dụng vòng lặp while để duyệt danh sách các dòng dữ liệu được SELECT
      // Nhưng hiện tại chúng ta đã chắn chắn SELECT theo Khóa chính (Primary key) => nên không cần phải sử dụng vòng lặp
      $dataTruyenRow = [];
      $dataTruyenRow = mysqli_fetch_array($resultTruyen, MYSQLI_ASSOC); // 1 record
      /* --- End Truy vấn dữ liệu Truyện --- */

      /* --- 
        --- 3.Truy vấn dữ liệu các Chương/Tập của Truyện
        --- Lấy giá trị khóa chính được truyền theo dạng QueryString Parameter key1=value1&key2=value2...
        --- 
        */
      // 3.1. Chuẩn bị câu truy vấn $sql
      $sqlDanhSachChuong = <<<EOT
      SELECT chuong_id, chuong_so, chuong_ten, chuong_noidung, truyen_id
      FROM chuong
      WHERE truyen_id = $truyen_id
      ORDER BY chuong_so ASC;
EOT;

      // 3.2. Thực thi câu truy vấn SQL để lấy về dữ liệu
      $resultChuong = mysqli_query($conn, $sqlDanhSachChuong);

      // 3.3. Khi thực thi các truy vấn dạng SELECT, dữ liệu lấy về cần phải phân tích để sử dụng
      // Thông thường, chúng ta sẽ sử dụng vòng lặp while để duyệt danh sách các dòng dữ liệu được SELECT
      // Ta sẽ tạo 1 mảng array để chứa các dữ liệu được trả về
      $dataChuong = [];
      while ($row = mysqli_fetch_array($resultChuong, MYSQLI_ASSOC)) {
        $dataChuong[] = array(
          'chuong_id' => $row['chuong_id'],
          'chuong_so' => $row['chuong_so'],
          'chuong_ten' => $row['chuong_ten'],
          'chuong_noidung' => $row['chuong_noidung'],
          'truyen_id' => $row['truyen_id'],
        );
      }
      /* --- End Truy vấn dữ liệu các Chương/Tập của Truyện --- */
      ?>
    </div>

    <div class="container">
      <div class="row">
        <div class="col">
          <h3>Thông tin</h3>
        </div>
      </div>

      <!-- THÔNG TIN TIỂU THUYẾT START -->
      <div class="row">
        <div class="col col-md-4">
          <img src="/project-nentang/assets/uploads/tieu-thuyet/<?= $dataTruyenRow['truyen_hinhdaidien'] ?>" class="card-img-top img-fluid" alt="">
          <p>
            <ul>
              <li>Tác giả: <?= $dataTruyenRow['truyen_tacgia'] ?></li>
              <li>Thể loại: <?= $dataTruyenRow['truyen_theloai'] ?></li>
              <li>Ngày đăng: <?= $dataTruyenRow['truyen_ngaydang'] ?></li>
            </ul>
          </p>
        </div>
        <div class="col col-md-8">
          <h2><?= $dataTruyenRow['truyen_ten'] ?></h2>
          <p>
            <?= $dataTruyenRow['truyen_mota_ngan'] ?>
          </p>
        </div>
      </div>
      <!-- THÔNG TIN TIỂU THUYẾT END -->

      <!-- DANH SÁCH CÁC CHƯƠNG/TẬP START -->
      <div class="row">
        <div class="col">
          <h3>Danh sách Chương truyện <?= $dataTruyenRow['truyen_ten'] ?></h3>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <ul>
            <?php foreach($dataChuong as $chuong): ?>
              <li>
                <a href="noi-dung.php?truyen_id=<?= $truyen_id ?>&chuong_so=<?= $chuong['chuong_so'] ?>&tong_so_chuong=<?= count($dataChuong) ?>">
                  Chương <?= $chuong['chuong_so'] ?> - <?= $chuong['chuong_ten'] ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <!-- DANH SÁCH CÁC CHƯƠNG/TẬP END -->
      
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