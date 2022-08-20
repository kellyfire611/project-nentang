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
      <h1 class="tieu-de">Quản lý Sửa Hình ảnh cho tập Truyện tranh</h1>
    </div>

    <div class="container">
      <!-- Form TRUYỆN TRANH START -->
      <div class="row">
        <div class="col">
          <h3 class="text-truyen-tranh">Sửa Hình ảnh Tập Truyện Tranh</h3>
        </div>
      </div>
      <?php
      // Truy vấn database để lấy danh sách
      // 1. Include file cấu hình kết nối đến database, khởi tạo kết nối $conn
      include_once(__DIR__ . '/../../dbconnect.php');

      // Chuẩn bị câu truy vấn $sqlSelect, lấy dữ liệu ban đầu của record cần update
      // Lấy giá trị khóa chính được truyền theo dạng QueryString Parameter key1=value1&key2=value2...
      $chuong_hinhanh_id = $_GET['chuong_hinhanh_id'];
      $chuong_id = $_GET['chuong_id'];
      $chuong_ten = $_GET['chuong_ten'];

      $sqlSelect = "SELECT * FROM `chuong_hinhanh` WHERE chuong_hinhanh_id=$chuong_hinhanh_id;";
      // var_dump($sqlSelect);die;

      // Thực thi câu truy vấn SQL để lấy về dữ liệu ban đầu của record cần update
      $resultSelect = mysqli_query($conn, $sqlSelect);
      $hinhanhRow = mysqli_fetch_array($resultSelect, MYSQLI_ASSOC); // 1 record
      ?>

      <form name="frmSua" id="frmSua" method="post" action="" enctype="multipart/form-data">
        <div class="form-row">
          <div class="col text-center">
            <label for="">Hình cũ</label>
            <img src="/project-nentang/assets/uploads/<?= $hinhanhRow['chuong_hinhanh_tenhinh'] ?>" class="img-fluid" />
          </div>
          <div class="col">
            <!-- Tạo khung div hiển thị ảnh cho người dùng Xem trước khi upload file lên Server -->
            <div class="preview-img-container text-center">
              <img src="/project-nentang/assets/shared/img/default-image_600.png" id="preview-img" width="200px" class="img-fluid" />
            </div>

            <!-- Input cho phép người dùng chọn FILE 
                Chỉ cho phép người dùng chọn các file Ảnh (*.jpg, *.jpeg, *.png, *.gif)
                -->
            <input type="file" class="form-control" id="chuong_hinhanh_tenhinh" name="chuong_hinhanh_tenhinh" accept=".jpg, .jpeg, .png, .gif">
          </div>
        </div>
        <div class="form-row">
          <div class="col text-right">
            <button name="btnLuu" id="btnLuu" class="btn btn-primary">Lưu</button>
          </div>
        </div>
      </form>
      <!-- End block content -->

      <?php
      if (isset($_POST['btnLuu'])) {
        // Kiểm tra ràng buộc dữ liệu (Validation)
        // Tạo biến lỗi để chứa thông báo lỗi
        $errors = [];

        // -- Validate File hình ảnh đại diện
        // Thu thập thông tin về FILES
        // Nếu người dùng có chọn file để upload
        if (isset($_FILES['chuong_hinhanh_tenhinh'])) {
          // Đối với mỗi file, sẽ có các thuộc tính như sau:
          // $_FILES['chuong_hinhanh_tenhinh']['name']     : Tên của file chúng ta upload
          // $_FILES['chuong_hinhanh_tenhinh']['type']     : Kiểu file mà chúng ta upload (hình ảnh, word, excel, pdf, txt, ...)
          // $_FILES['chuong_hinhanh_tenhinh']['tmp_name'] : Đường dẫn đến file tạm trên web server
          // $_FILES['chuong_hinhanh_tenhinh']['error']    : Trạng thái của file chúng ta upload, 0 => không có lỗi
          // $_FILES['chuong_hinhanh_tenhinh']['size']     : Kích thước của file chúng ta upload

          // -- Validate trường hợp file Upload lên Server bị lỗi
          // Nếu file upload bị lỗi, tức là thuộc tính error > 0
          if ($_FILES['chuong_hinhanh_tenhinh']['error'] > 0) {
            // File Upload Bị Lỗi
            $fileError = $_FILES["chuong_hinhanh_tenhinh"]["error"];

            // Tùy thuộc vào giá trị lỗi mà chúng ta sẽ trả về câu thông báo lỗi thân thiện cho người dùng...
            switch ($fileError) {
              case UPLOAD_ERR_OK: // 0
                break;
              case UPLOAD_ERR_INI_SIZE:
                // Exceeds max size in php.ini
                $errors['chuong_hinhanh_tenhinh'][] = [
                  'rule' => 'max_size',
                  'rule_value' => '5Mb',
                  'value' => $_FILES["chuong_hinhanh_tenhinh"]["tmp_name"],
                  'msg' => 'File bạn upload có dung lượng quá lớn. Vui lòng upload file không vượt quá 5Mb...'
                ];
                break;
              case UPLOAD_ERR_PARTIAL:
                // Exceeds max size in html form
                $errors['chuong_hinhanh_tenhinh'][] = [
                  'rule' => 'max_size',
                  'rule_value' => '5Mb',
                  'value' => $_FILES["chuong_hinhanh_tenhinh"]["tmp_name"],
                  'msg' => 'File bạn upload có dung lượng quá lớn. Vui lòng upload file không vượt quá 5Mb...'
                ];
                break;
              case UPLOAD_ERR_NO_FILE:
                // Không ràng buộc phải chọn file
                // No file was uploaded
                // $errors['chuong_hinhanh_tenhinh'][] = [
                //   'rule' => 'no_file',
                //   'rule_value' => true,
                //   'value' => $_FILES["chuong_hinhanh_tenhinh"]["tmp_name"],
                //   'msg' => 'Bạn chưa chọn file để upload...'
                // ];
                break;
              case UPLOAD_ERR_NO_TMP_DIR:
                // No /tmp dir to write to
                break;
              case UPLOAD_ERR_CANT_WRITE:
                // Error writing to disk
                break;
              case UPLOAD_ERR_EXTENSION:
                // A PHP extension stopped the file upload
                break;
              default:
                // No error was faced! Phew!
                break;
            }
          } else {
            // -- Validate trường hợp file Upload lên Server thành công mà bị sai về Loại file (file types)
            // Nếu người dùng upload file khác *.jpg, *.jpeg, *.png, *.gif
            // thì báo lỗi
            $file_extension = pathinfo($_FILES['chuong_hinhanh_tenhinh']["name"], PATHINFO_EXTENSION); // Lấy đuôi file (file extension) để so sánh
            if (!($file_extension == 'jpg' || $file_extension == 'jpeg'
              || $file_extension == 'png' || $file_extension == 'gif'
            )) {
              $errors['chuong_hinhanh_tenhinh'][] = [
                'rule' => 'file_extension',
                'rule_value' => '.jpg, .jpeg, .png, .gif',
                'value' => $_FILES['chuong_hinhanh_tenhinh']["name"],
                'msg' => 'Chỉ cho phép upload các định dạng (*.jpg, *.jpeg, *.png, *.gif)...'
              ];
            }

            // -- Validate trường hợp file Upload lên Server thành công mà kích thước file quá lớn
            $file_size = $_FILES['chuong_hinhanh_tenhinh']["size"];
            if ($file_size > (1024 * 1024 * 10)) { // 1 Mb = 1024 Kb = 1024 * 1024 Byte
              $errors['chuong_hinhanh_tenhinh'][] = [
                'rule' => 'file_size',
                'rule_value' => (1024 * 1024 * 10),
                'value' => $_FILES['chuong_hinhanh_tenhinh']["name"],
                'msg' => 'Chỉ cho phép upload file nhỏ hơn 10Mb...'
              ];
            }
          }
        }
      }
      ?>

      <!-- Vùng hiển thị thông báo lỗi khi người dùng nhập liệu có sai sót thông tin -->
      <!-- Nếu có lỗi VALIDATE dữ liệu thì hiển thị ra màn hình 
      - Sử dụng thành phần (component) Alert của Bootstrap
      - Mỗi một lỗi hiển thị sẽ in theo cấu trúc Danh sách không thứ tự UL > LI
      -->
      <?php if (
        isset($_POST['btnLuu'])   // Nếu người dùng có bấm nút "Lưu dữ liệu"
        && isset($errors)         // Nếu biến $errors có tồn tại
        && (!empty($errors))      // Nếu giá trị của biến $errors không rỗng
      ) : ?>
        <div id="errors-container" class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <ul>
            <?php foreach ($errors as $fields) : ?>
              <?php foreach ($fields as $field) : ?>
                <li><?php echo $field['msg']; ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php
      // Nếu không có lỗi VALIDATE dữ liệu (tức là dữ liệu đã hợp lệ)
      // Tiến hành thực thi câu lệnh SQL Query Database
      // => giá trị của biến $errors là rỗng
      if (
        isset($_POST['btnLuu'])  // Nếu người dùng có bấm nút "Lưu dữ liệu"
        && (!isset($errors) || (empty($errors))) // Nếu biến $errors không tồn tại Hoặc giá trị của biến $errors rỗng
      ) {
        // VALIDATE dữ liệu đã hợp lệ
        $tentaptin = $hinhanhRow['chuong_hinhanh_tenhinh'];

        // Nếu người dùng có chọn file hình mới -> và quá trình upload hình thành công
        if (isset($_FILES['chuong_hinhanh_tenhinh']) && $_FILES['chuong_hinhanh_tenhinh']['error'] == 0) {
          // Đường dẫn để chứa thư mục upload trên ứng dụng web của chúng ta. Các bạn có thể tùy chỉnh theo ý các bạn.
          // Ví dụ: các file upload sẽ được lưu vào thư mục "assets/uploads/..."
          $upload_dir = __DIR__ . "/../../assets/uploads/truyen-tranh/";

          // Xóa file cũ để tránh rác trong thư mục UPLOADS
          // Kiểm tra nếu file có tổn tại thì xóa file đi
          $old_file = $upload_dir . $hinhanhRow['chuong_hinhanh_tenhinh'];
          if (file_exists($old_file)) {
            // Hàm unlink(filepath) dùng để xóa file trong PHP
            unlink($old_file);
          }

          // Để tránh trường hợp có 2 người dùng cùng lúc upload tập tin trùng tên nhau
          // Ví dụ:
          // - Người 1: upload tập tin hình ảnh tên `hoahong.jpg`
          // - Người 2: cũng upload tập tin hình ảnh tên `hoahong.jpg`
          // => dẫn đến tên tin trong thư mục chỉ còn lại tập tin người dùng upload cuối cùng
          // Cách giải quyết đơn giản là chúng ta sẽ ghép thêm ngày giờ vào tên file
          $tentaptin = date('YmdHis') . '_' . $_FILES['chuong_hinhanh_tenhinh']['name']; //20200530154922_hoahong.jpg

          // Tiến hành di chuyển file từ thư mục tạm trên server vào thư mục chúng ta muốn chứa các file uploads
          // Ví dụ: move file từ C:\xampp\tmp\php6091.tmp -> C:/xampp/htdocs/project-nentang/assets/uploads/hoahong.jpg
          // var_dump($_FILES['chuong_hinhanh_tenhinh']['tmp_name']);
          // var_dump($upload_dir . $tentaptin);
          // die;
          move_uploaded_file($_FILES['chuong_hinhanh_tenhinh']['tmp_name'], $upload_dir . $tentaptin);

          $tentaptin = 'truyen-tranh/' . $tentaptin;
        }

        // 4. Chuẩn bị câu lệnh SQL
        $sqlUpdate = <<<EOT
        UPDATE chuong_hinhanh
        SET 
          chuong_hinhanh_tenhinh = '$tentaptin'
        WHERE	chuong_hinhanh_id = $chuong_hinhanh_id
EOT;

        // 5. Thực thi câu truy vấn SQL để lấy về dữ liệu
        mysqli_query($conn, $sqlUpdate) or die("<b>Có lỗi khi thực thi câu lệnh SQL: </b>" . mysqli_error($conn) . "<br /><b>Câu lệnh vừa thực thi:</b></br>$sqlUpdate");

        // 6. Sau khi cập nhật dữ liệu, tự động điều hướng về trang Danh sách
        // Điều hướng bằng Javascript
        echo "<script>location.href = 'danh-sach.php?chuong_id=$chuong_id&chuong_ten=$chuong_ten';</script>";
      }

      ?>
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
    const fileInput = document.getElementById("chuong_hinhanh_tenhinh");
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