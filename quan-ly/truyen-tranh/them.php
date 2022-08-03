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
      <h1 class="tieu-de">Quản lý Thêm mới Truyện tranh</h1>
    </div>

    <div class="container">
      <!-- Form TRUYỆN TRANH START -->
      <div class="row">
        <div class="col">
          <h3 class="text-truyen-tranh">Thêm mới Truyện Tranh</h3>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <!-- Form cho phép người dùng upload file lên Server bắt buộc phải có thuộc tính enctype="multipart/form-data" -->
          <form name="frmThemMoi" id="frmThemMoi" method="post" action="" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group col">
                <label for="truyen_ma">Mã truyện tranh</label>
                <input type="text" class="form-control" id="truyen_ma" name="truyen_ma" placeholder="Mã truyện tranh" value="">
              </div>
              <div class="form-group col">
                <label for="truyen_ten">Tên truyện tranh</label>
                <input type="text" class="form-control" id="truyen_ten" name="truyen_ten" placeholder="Tên truyện tranh" value="">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="truyen_loai">Loại truyện tranh</label>
                <input type="text" class="form-control" id="truyen_loai" name="truyen_loai" placeholder="Loại truyện tranh" value="2" readonly>
              </div>
              <div class="form-group col">
                <label for="truyen_theloai">Thể loại truyện tranh</label>
                <input type="text" class="form-control" id="truyen_theloai" name="truyen_theloai" placeholder="Thể loại truyện tranh">
              </div>
              <div class="form-group col">
                <label for="truyen_tacgia">Tác giả truyện tranh</label>
                <input type="text" class="form-control" id="truyen_tacgia" name="truyen_tacgia" placeholder="Tác giả truyện tranh">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col">
                <label for="truyen_hinhdaidien">Hình đại diện</label>

                <!-- Tạo khung div hiển thị ảnh cho người dùng Xem trước khi upload file lên Server -->
                <div class="preview-img-container text-center">
                  <img src="/project-nentang/assets/shared/img/default-image_600.png" id="preview-img" width="200px" />
                </div>

                <!-- Input cho phép người dùng chọn FILE -->
                <input type="file" class="form-control" id="truyen_hinhdaidien" name="truyen_hinhdaidien">
              </div>
              <div class="form-group col">
                <label for="truyen_mota_ngan">Mô tả</label>
                <textarea class="form-control" id="truyen_mota_ngan" name="truyen_mota_ngan" placeholder="Mô tả ngắn về truyện tranh" rows="10"></textarea>
              </div>
            </div>
            <button class="btn btn-primary" name="btnLuu">Lưu dữ liệu</button>
          </form>
        </div>
      </div>
      <!-- Form TRUYỆN TRANH END -->

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

      // 2. Nếu người dùng có bấm nút "Lưu dữ liệu" -> thì tiến hành xử lý
      // Kiểm tra xem dữ liệu từ Client truyền đến có tồn tại KEY nào có tên là "btnLuu" hay không? => nếu có tồn tại thì xem như người dùng đã bấm nút
      if (isset($_POST['btnLuu'])) {
        // 3. Thu thập các thông tin do Client truyền đến
        $truyen_ma = $_POST['truyen_ma'];
        $truyen_ten = $_POST['truyen_ten'];
        $truyen_loai = $_POST['truyen_loai'];
        $truyen_theloai = $_POST['truyen_theloai'];
        $truyen_tacgia = $_POST['truyen_tacgia'];
        $truyen_mota_ngan = $_POST['truyen_mota_ngan'];
        $truyen_ngaydang = date('Y-m-d H:i:s');
        $truyen_hinhdaidien = 'NULL';

        // 3.1. Thu thập thông tin về FILES
        // Nếu người dùng có chọn file để upload
        if (isset($_FILES['truyen_hinhdaidien'])) {
          // Đường dẫn để chứa thư mục upload trên ứng dụng web của chúng ta. Các bạn có thể tùy chỉnh theo ý các bạn.
          // Ví dụ: các file upload sẽ được lưu vào thư mục "assets/uploads/..."
          $upload_dir = __DIR__ . "/../../assets/uploads/truyen-tranh";

          // Đối với mỗi file, sẽ có các thuộc tính như sau:
          // $_FILES['truyen_hinhdaidien']['name']     : Tên của file chúng ta upload
          // $_FILES['truyen_hinhdaidien']['type']     : Kiểu file mà chúng ta upload (hình ảnh, word, excel, pdf, txt, ...)
          // $_FILES['truyen_hinhdaidien']['tmp_name'] : Đường dẫn đến file tạm trên web server
          // $_FILES['truyen_hinhdaidien']['error']    : Trạng thái của file chúng ta upload, 0 => không có lỗi
          // $_FILES['truyen_hinhdaidien']['size']     : Kích thước của file chúng ta upload

          // 3.1. Chuyển file từ thư mục tạm vào thư mục Uploads
          // Nếu file upload bị lỗi, tức là thuộc tính error > 0
          if ($_FILES['truyen_hinhdaidien']['error'] > 0) {
            echo 'File Upload Bị Lỗi';
            die;
          } else {
            // Để tránh trường hợp có 2 người dùng cùng lúc upload tập tin trùng tên nhau
            // Ví dụ:
            // - Người 1: upload tập tin hình ảnh tên `hoahong.jpg`
            // - Người 2: cũng upload tập tin hình ảnh tên `hoahong.jpg`
            // => dẫn đến tên tin trong thư mục chỉ còn lại tập tin người dùng upload cuối cùng
            // Cách giải quyết đơn giản là chúng ta sẽ ghép thêm ngày giờ vào tên file
            $truyen_hinhdaidien = $_FILES['truyen_hinhdaidien']['name'];
            $tentaptin = date('YmdHis') . '_' . $truyen_hinhdaidien; //20200530154922_hoahong.jpg

            // Tiến hành di chuyển file từ thư mục tạm trên server vào thư mục chúng ta muốn chứa các file uploads
            // Ví dụ: move file từ C:\xampp\tmp\php6091.tmp -> C:/xampp/htdocs/learning.nentang.vn/php/twig/assets/uploads/hoahong.jpg
            // var_dump($_FILES['truyen_hinhdaidien']['tmp_name']);
            // var_dump($upload_dir . $subdir . $tentaptin);

            move_uploaded_file($_FILES['truyen_hinhdaidien']['tmp_name'], $upload_dir . $subdir . $tentaptin);
          }
        }

        // 4. Chuẩn bị câu lệnh SQL
        $sqlThemMoiTruyen = <<<EOT
        INSERT INTO truyen(truyen_ma, truyen_ten, truyen_hinhdaidien, truyen_loai, truyen_theloai, truyen_tacgia, truyen_mota_ngan, truyen_ngaydang)
        VALUES ('$truyen_ma', '$truyen_ten', '$truyen_hinhdaidien', $truyen_loai, '$truyen_theloai', '$truyen_tacgia', '$truyen_mota_ngan', '$truyen_ngaydang');
EOT;

        print_r($sqlThemMoiTruyen);
        die;

        // 5. Thực thi câu truy vấn SQL để lấy về dữ liệu
        mysqli_query($conn, $sqlThemMoiTruyen) or die("<b>Có lỗi khi thực thi câu lệnh SQL: </b>" . mysqli_error($conn) . "<br /><b>Câu lệnh vừa thực thi:</b></br>$sql");

        // 6. Sau khi cập nhật dữ liệu, tự động điều hướng về trang Danh sách
        // Điều hướng bằng Javascript
        //echo '<script>location.href = "danh-sach.php";</script>';
      }

      ?>
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
    // Hiển thị ảnh preview (xem trước) khi người dùng chọn Ảnh
    const reader = new FileReader();
    const fileInput = document.getElementById("truyen_hinhdaidien");
    const img = document.getElementById("preview-img");
    reader.onload = e => {
      img.src = e.target.result;
    }
    fileInput.addEventListener('change', e => {
      const f = e.target.files[0];
      reader.readAsDataURL(f);
    })
  </script>

</body>

</html>