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
    .ck-editor__editable[role="textbox"] {
      /* editing area */
      min-height: 400px;
    }

    .ck-content .image {
      /* block images */
      max-width: 80%;
      margin: 20px auto;
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
      <h1 class="tieu-de">Quản lý Sửa Chương Tiểu thuyết</h1>
    </div>

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

    // 2. Chuẩn bị câu lệnh
    $sqlSelectTruyen = "
    SELECT truyen_id, truyen_ten 
    FROM truyen
    WHERE truyen_loai = 1
    ";

    // 3. Thực thi câu lệnh
    $resultTruyen = mysqli_query($conn, $sqlSelectTruyen);

    // 4. Phân tách dữ liệu thành mảng array
    $dataTruyen = [];
    while ($row = mysqli_fetch_array($resultTruyen, MYSQLI_ASSOC)) {
      $dataTruyen[] = array(
        'truyen_id' => $row['truyen_id'],
        'truyen_ten' => $row['truyen_ten']
      );
    }

    // -------- Tím dữ liệu cũ
    // Chuẩn bị câu truy vấn $sqlSelect, lấy dữ liệu ban đầu của record cần update
    // Lấy giá trị khóa chính được truyền theo dạng QueryString Parameter key1=value1&key2=value2...
    $chuong_id = $_GET['chuong_id'];
    $sqlSelectTap = "SELECT * FROM `chuong` WHERE chuong_id=$chuong_id;";

    // Thực thi câu truy vấn SQL để lấy về dữ liệu ban đầu của record cần update
    $resultSelectTap = mysqli_query($conn, $sqlSelectTap);
    $chuongRow = mysqli_fetch_array($resultSelectTap, MYSQLI_ASSOC); // 1 record
    ?>
    <div class="container">
      <!-- Form TRUYỆN TRANH START -->
      <div class="row">
        <div class="col">
          <h3 class="text-truyen-tranh">Sửa Chương Tiểu thuyết</h3>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <!-- Form cho phép người dùng upload file lên Server bắt buộc phải có thuộc tính enctype="multipart/form-data" -->
          <form name="frmThemMoi" id="frmThemMoi" method="post" action="" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group col">
                <label for="truyen_id">Truyện</label>
                <select name="truyen_id" id="truyen_id" class="form-control">
                  <option value="">Vui lòng chọn Truyện</option>
                  <?php foreach ($dataTruyen as $truyen) : ?>
                    <?php if ($truyen['truyen_id'] == $chuongRow['truyen_id']) : ?>
                      <option value="<?= $truyen['truyen_id'] ?>" selected><?= $truyen['truyen_ten'] ?></option>
                    <?php else : ?>
                      <option value="<?= $truyen['truyen_id'] ?>"><?= $truyen['truyen_ten'] ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-3">
                <label for="chuong_so">Số Chương</label>
                <input type="number" class="form-control" id="chuong_so" name="chuong_so" placeholder="Số tập" value="<?= $chuongRow['chuong_so'] ?>">
              </div>
              <div class="form-group col-9">
                <label for="chuong_ten">Tên chương tiểu thuyết</label>
                <input type="text" class="form-control" id="chuong_ten" name="chuong_ten" placeholder="Tên tập tiểu thuyết" value="<?= $chuongRow['chuong_ten'] ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="col">
                <textarea name="chuong_noidung" id="chuong_noidung"><?= $chuongRow['chuong_noidung'] ?></textarea>
              </div>
            </div>

            <a href="danh-sach.php" class="btn btn-secondary">Quay về</a>
            <button class="btn btn-primary" name="btnLuu">Lưu dữ liệu</button>
          </form>
        </div>
      </div>
      <!-- Form TRUYỆN TRANH END -->

      <?php
      // 2. Nếu người dùng có bấm nút "Lưu dữ liệu" -> thì tiến hành xử lý
      // Kiểm tra xem dữ liệu từ Client truyền đến có tồn tại KEY nào có tên là "btnLuu" hay không? => nếu có tồn tại thì xem như người dùng đã bấm nút
      if (isset($_POST['btnLuu'])) {
        // 3. Thu thập các thông tin do người dùng từ Client truyền đến
        $truyen_id = $_POST['truyen_id'];
        $chuong_so = $_POST['chuong_so'];
        $chuong_ten = $_POST['chuong_ten'];
        $chuong_noidung = $_POST['chuong_noidung'];

        // 4. Kiểm tra ràng buộc dữ liệu (Validation)
        // Tạo biến lỗi để chứa thông báo lỗi
        $errors = [];

        // -- Validate Mã tiểu thuyết
        // required
        if (empty($truyen_id)) {
          $errors['truyen_id'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $truyen_id,
            'msg' => 'Vui lòng chọn Truyện'
          ];
        }

        // -- Validate số Chương
        // required
        if (empty($chuong_so)) {
          $errors['chuong_so'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $chuong_so,
            'msg' => 'Vui lòng nhập số Chương'
          ];
        }

        // -- Validate Tên tập
        // required
        if (empty($chuong_ten)) {
          $errors['chuong_ten'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $chuong_ten,
            'msg' => 'Vui lòng nhập tên Chương'
          ];
        }
        // minlength 3
        else if (!empty($chuong_ten) && strlen($chuong_ten) < 3) {
          $errors['chuong_ten'][] = [
            'rule' => 'minlength',
            'rule_value' => 3,
            'value' => $chuong_ten,
            'msg' => 'Tên tập Truyện phải có ít nhất 3 ký tự'
          ];
        }
        // maxlength 500
        else if (!empty($chuong_ten) && strlen($chuong_ten) > 50) {
          $errors['chuong_ten'][] = [
            'rule' => 'maxlength',
            'rule_value' => 500,
            'value' => $chuong_ten,
            'msg' => 'Tên tập Truyện không được vượt quá 50 ký tự'
          ];
        }

        // -- Validate Nội dung tập
        // required
        if (empty($chuong_noidung)) {
          $errors['chuong_noidung'][] = [
            'rule' => 'required',
            'rule_value' => true,
            'value' => $chuong_noidung,
            'msg' => 'Vui lòng nhập nội dung Chương'
          ];
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

        // 4. Chuẩn bị câu lệnh SQL
        $sqlUpdate = <<<EOT
        UPDATE chuong
        SET
          chuong_so='$chuong_so',
          chuong_ten='$chuong_ten',
          chuong_noidung='$chuong_noidung',
          truyen_id=$truyen_id
        WHERE chuong_id=$chuong_id
EOT;
        // print_r($sqlUpdate);
        // die;

        // 5. Thực thi câu truy vấn SQL để lấy về dữ liệu
        mysqli_query($conn, $sqlUpdate) or die("<b>Có lỗi khi thực thi câu lệnh SQL: </b>" . mysqli_error($conn) . "<br /><b>Câu lệnh vừa thực thi:</b></br>$sqlInsert");

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
  <script src="/project-nentang/assets/vendor/ckeditor/ckeditor.js"></script>
  <script>
    ClassicEditor
      .create(document.querySelector('#chuong_noidung'))
      .catch(error => {
        console.error(error);
      });
  </script>
</body>

</html>