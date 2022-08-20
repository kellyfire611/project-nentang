<?php
// var_dump($_FILES);
// Đường dẫn để chứa thư mục upload trên ứng dụng web của chúng ta. Các bạn có thể tùy chỉnh theo ý các bạn.
// Ví dụ: các file upload sẽ được lưu vào thư mục "assets/uploads/..."
$upload_dir = __DIR__ . "/../../assets/uploads/truyen-tranh/";

$tentaptin = date('YmdHis') . '_' . $_FILES['file']['name'];

move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $tentaptin);

// 1. Mở kết nối
include_once(__DIR__ . '/../../dbconnect.php');
// 2. Thu thập dữ liệu
$chuong_id = $_POST['chuong_id'];
// 3. Chuẩn bị câu lệnh
$tentaptin = 'truyen-tranh/' . $tentaptin;
$sql = "
INSERT INTO chuong_hinhanh
(chuong_id, chuong_hinhanh_tenhinh)
VALUES ($chuong_id, '$tentaptin');
";
// 4. Thực thi
mysqli_query($conn, $sql);
?>
