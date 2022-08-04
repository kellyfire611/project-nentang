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
                <input type="text" class="form-control" id="truyen_loai" name="truyen_loai" placeholder="Loại truyện tranh" value="#2-Truyện tranh" readonly>
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

                <!-- Input cho phép người dùng chọn FILE 
                Chỉ cho phép người dùng chọn các file Ảnh (*.jpg, *.jpeg, *.png, *.gif)
                -->
                <input type="file" class="form-control" id="truyen_hinhdaidien" name="truyen_hinhdaidien"
                  accept=".jpg, .jpeg, .png, .gif">
              </div>
              <div class="form-group col">
                <label for="truyen_mota_ngan">Mô tả ngắn</label>
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
        // 3. Thu thập các thông tin do người dùng từ Client truyền đến
        $truyen_ma = $_POST['truyen_ma'];
        $truyen_ten = $_POST['truyen_ten'];
        $truyen_loai = 2; // #2-Truyện tranh
        $truyen_theloai = $_POST['truyen_theloai'];
        $truyen_tacgia = $_POST['truyen_tacgia'];
        $truyen_mota_ngan = $_POST['truyen_mota_ngan'];
        $truyen_ngaydang = date('Y-m-d H:i:s');

        // 4. Kiểm tra ràng buộc dữ liệu (Validation)
        // Tạo biến lỗi để chứa thông báo lỗi
        $errors = [];

        // -- Validate Mã truyện tranh
        // required
        if (empty($truyen_ma)) {
          $errors['truyen_ma'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $truyen_ma,
            'msg' => 'Vui lòng nhập mã Truyện'
          ];
        }
        // minlength 3
        else if (!empty($truyen_ma) && strlen($truyen_ma) < 3) {
          $errors['truyen_ma'][] = [
            'rule' => 'minlength',
            'rule_value' => 3,
            'value' => $truyen_ma,
            'msg' => 'Mã truyện phải có ít nhất 3 ký tự'
          ];
        }
        // maxlength 50
        else if (!empty($truyen_ma) && strlen($truyen_ma) > 50) {
          $errors['truyen_ma'][] = [
            'rule' => 'maxlength',
            'rule_value' => 50,
            'value' => $truyen_ma,
            'msg' => 'Mã truyện không được vượt quá 50 ký tự'
          ];
        }

        // -- Validate Tên Truyện tranh
        // required
        if (empty($truyen_ten)) {
          $errors['truyen_ten'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $truyen_ten,
            'msg' => 'Vui lòng nhập tên Truyện'
          ];
        }
        // minlength 3
        else if (!empty($truyen_ten) && strlen($truyen_ten) < 3) {
          $errors['truyen_ten'][] = [
            'rule' => 'minlength',
            'rule_value' => 3,
            'value' => $truyen_ten,
            'msg' => 'Tên Truyện phải có ít nhất 3 ký tự'
          ];
        }
        // maxlength 50
        else if (!empty($truyen_ten) && strlen($truyen_ten) > 50) {
          $errors['truyen_ten'][] = [
            'rule' => 'maxlength',
            'rule_value' => 50,
            'value' => $truyen_ten,
            'msg' => 'Tên Truyện không được vượt quá 50 ký tự'
          ];
        }

        // -- Validate Thể loại Truyện tranh
        // required
        if (empty($truyen_theloai)) {
          $errors['truyen_theloai'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $truyen_theloai,
            'msg' => 'Vui lòng nhập thể loại Truyện'
          ];
        }
        // minlength 3
        else if (!empty($truyen_theloai) && strlen($truyen_theloai) < 3) {
          $errors['truyen_theloai'][] = [
            'rule' => 'minlength',
            'rule_value' => 3,
            'value' => $truyen_theloai,
            'msg' => 'Thể loại Truyện phải có ít nhất 3 ký tự'
          ];
        }
        // maxlength 50
        else if (!empty($truyen_theloai) && strlen($truyen_theloai) > 50) {
          $errors['truyen_theloai'][] = [
            'rule' => 'maxlength',
            'rule_value' => 50,
            'value' => $truyen_theloai,
            'msg' => 'Thể loại Truyện không được vượt quá 50 ký tự'
          ];
        }

        // -- Validate Tác giả Truyện tranh
        // required
        if (empty($truyen_tacgia)) {
          $errors['truyen_tacgia'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $truyen_tacgia,
            'msg' => 'Vui lòng nhập tác giả Truyện'
          ];
        }
        // minlength 3
        else if (!empty($truyen_tacgia) && strlen($truyen_tacgia) < 3) {
          $errors['truyen_tacgia'][] = [
            'rule' => 'minlength',
            'rule_value' => 3,
            'value' => $truyen_tacgia,
            'msg' => 'Tác giả Truyện phải có ít nhất 3 ký tự'
          ];
        }
        // maxlength 50
        else if (!empty($truyen_tacgia) && strlen($truyen_tacgia) > 50) {
          $errors['truyen_tacgia'][] = [
            'rule' => 'maxlength',
            'rule_value' => 50,
            'value' => $truyen_tacgia,
            'msg' => 'Tác giả Truyện không được vượt quá 50 ký tự'
          ];
        }

        // -- Validate Mô tả ngắn
        // required
        if (empty($truyen_mota_ngan)) {
          $errors['truyen_mota_ngan'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $truyen_mota_ngan,
            'msg' => 'Vui lòng nhập mô tả ngắn Truyện'
          ];
        }
        // minlength 3
        else if (!empty($truyen_mota_ngan) && strlen($truyen_mota_ngan) < 3) {
          $errors['truyen_mota_ngan'][] = [
            'rule' => 'minlength',
            'rule_value' => 3,
            'value' => $truyen_mota_ngan,
            'msg' => 'Mô tả Truyện phải có ít nhất 3 ký tự'
          ];
        }
        // maxlength 255
        else if (!empty($truyen_mota_ngan) && strlen($truyen_mota_ngan) > 255) {
          $errors['truyen_mota_ngan'][] = [
            'rule' => 'maxlength',
            'rule_value' => 255,
            'value' => $truyen_mota_ngan,
            'msg' => 'Mô tả Truyện không được vượt quá 255 ký tự'
          ];
        }

        // -- Validate File hình ảnh đại diện
        // Thu thập thông tin về FILES
        // Nếu người dùng có chọn file để upload
        if (isset($_FILES['truyen_hinhdaidien'])) {
          // Đối với mỗi file, sẽ có các thuộc tính như sau:
          // $_FILES['truyen_hinhdaidien']['name']     : Tên của file chúng ta upload
          // $_FILES['truyen_hinhdaidien']['type']     : Kiểu file mà chúng ta upload (hình ảnh, word, excel, pdf, txt, ...)
          // $_FILES['truyen_hinhdaidien']['tmp_name'] : Đường dẫn đến file tạm trên web server
          // $_FILES['truyen_hinhdaidien']['error']    : Trạng thái của file chúng ta upload, 0 => không có lỗi
          // $_FILES['truyen_hinhdaidien']['size']     : Kích thước của file chúng ta upload

          // -- Validate trường hợp file Upload lên Server bị lỗi
          // Nếu file upload bị lỗi, tức là thuộc tính error > 0
          if ($_FILES['truyen_hinhdaidien']['error'] > 0) {
            // File Upload Bị Lỗi
            $fileError = $_FILES["truyen_hinhdaidien"]["error"];

            // Tùy thuộc vào giá trị lỗi mà chúng ta sẽ trả về câu thông báo lỗi thân thiện cho người dùng...
            switch($fileError) {
              case UPLOAD_ERR_OK: // 0
                break;
              case UPLOAD_ERR_INI_SIZE:
                // Exceeds max size in php.ini
                $errors['truyen_hinhdaidien'][] = [
                  'rule' => 'max_size',
                  'rule_value' => '5Mb',
                  'value' => $_FILES["truyen_hinhdaidien"]["tmp_name"],
                  'msg' => 'File bạn upload có dung lượng quá lớn. Vui lòng upload file không vượt quá 5Mb...'
                ];
                break;
              case UPLOAD_ERR_PARTIAL:
                // Exceeds max size in html form
                $errors['truyen_hinhdaidien'][] = [
                  'rule' => 'max_size',
                  'rule_value' => '5Mb',
                  'value' => $_FILES["truyen_hinhdaidien"]["tmp_name"],
                  'msg' => 'File bạn upload có dung lượng quá lớn. Vui lòng upload file không vượt quá 5Mb...'
                ];
                break;
              case UPLOAD_ERR_NO_FILE:
                // No file was uploaded
                $errors['truyen_hinhdaidien'][] = [
                  'rule' => 'no_file',
                  'rule_value' => true,
                  'value' => $_FILES["truyen_hinhdaidien"]["tmp_name"],
                  'msg' => 'Bạn chưa chọn file để upload...'
                ];
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
            $file_extension = pathinfo($_FILES['truyen_hinhdaidien']["name"], PATHINFO_EXTENSION); // Lấy đuôi file (file extension) để so sánh
            if( !($file_extension == 'jpg' || $file_extension == 'jpeg'
              || $file_extension == 'png' || $file_extension == 'gif'
            )) {
              $errors['truyen_hinhdaidien'][] = [
                'rule' => 'file_extension',
                'rule_value' => '.jpg, .jpeg, .png, .gif',
                'value' => $_FILES['truyen_hinhdaidien']["name"],
                'msg' => 'Chỉ cho phép upload các định dạng (*.jpg, *.jpeg, *.png, *.gif)...'
              ];
            }

            // -- Validate trường hợp file Upload lên Server thành công mà kích thước file quá lớn
            $file_size = $_FILES['truyen_hinhdaidien']["size"];
            if( $file_size > (1024 * 1024 * 10)) { // 1 Mb = 1024 Kb = 1024 * 1024 Byte
              $errors['truyen_hinhdaidien'][] = [
                'rule' => 'file_size',
                'rule_value' => (1024 * 1024 * 10),
                'value' => $_FILES['truyen_hinhdaidien']["name"],
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
        
        // Đường dẫn để chứa thư mục upload trên ứng dụng web của chúng ta. Các bạn có thể tùy chỉnh theo ý các bạn.
        // Ví dụ: các file upload sẽ được lưu vào thư mục "assets/uploads/..."
        $upload_dir = __DIR__ . "/../../assets/uploads/truyen-tranh/";

        // Để tránh trường hợp có 2 người dùng cùng lúc upload tập tin trùng tên nhau
        // Ví dụ:
        // - Người 1: upload tập tin hình ảnh tên `hoahong.jpg`
        // - Người 2: cũng upload tập tin hình ảnh tên `hoahong.jpg`
        // => dẫn đến tên tin trong thư mục chỉ còn lại tập tin người dùng upload cuối cùng
        // Cách giải quyết đơn giản là chúng ta sẽ ghép thêm ngày giờ vào tên file
        $tentaptin = date('YmdHis') . '_' . $_FILES['truyen_hinhdaidien']['name']; //20200530154922_hoahong.jpg

        // Tiến hành di chuyển file từ thư mục tạm trên server vào thư mục chúng ta muốn chứa các file uploads
        // Ví dụ: move file từ C:\xampp\tmp\php6091.tmp -> C:/xampp/htdocs/project-nentang/assets/uploads/hoahong.jpg
        // var_dump($_FILES['truyen_hinhdaidien']['tmp_name']);
        // var_dump($upload_dir . $tentaptin);
        move_uploaded_file($_FILES['truyen_hinhdaidien']['tmp_name'], $upload_dir . $tentaptin);

        // 4. Chuẩn bị câu lệnh SQL
        $sqlThemMoiTruyen = <<<EOT
        INSERT INTO truyen(truyen_ma, truyen_ten, truyen_hinhdaidien, truyen_loai, truyen_theloai, truyen_tacgia, truyen_mota_ngan, truyen_ngaydang)
        VALUES ('$truyen_ma', '$truyen_ten', '$tentaptin', $truyen_loai, '$truyen_theloai', '$truyen_tacgia', '$truyen_mota_ngan', '$truyen_ngaydang');
EOT;
        // print_r($sqlThemMoiTruyen);
        // die;

        // 5. Thực thi câu truy vấn SQL để lấy về dữ liệu
        mysqli_query($conn, $sqlThemMoiTruyen) or die("<b>Có lỗi khi thực thi câu lệnh SQL: </b>" . mysqli_error($conn) . "<br /><b>Câu lệnh vừa thực thi:</b></br>$sqlThemMoiTruyen");

        // 6. Sau khi cập nhật dữ liệu, tự động điều hướng về trang Danh sách
        // Điều hướng bằng Javascript
        echo '<script>location.href = "danh-sach.php";</script>';
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