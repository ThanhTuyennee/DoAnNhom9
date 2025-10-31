<?php
include 'include/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // 
    $sql = "DELETE FROM LoaiBanh WHERE MaLoaiBanh = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Đã xóa loại bánh thành công!');
                window.location.href = 'QuanLyLoaiBanh.php';
              </script>";
    } else {
        echo "<script>
                alert(' Lỗi khi xóa: " . addslashes($conn->error) . "');
                window.location.href = 'QuanLyLoaiBanh.php';
              </script>";
    }
} else {
    // Nếu không có id -> quay lại danh sách
    header("Location: QuanLyLoaiBanh.php");
    exit();
}
?>
