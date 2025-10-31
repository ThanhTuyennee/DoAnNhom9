<?php
include 'db/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin loại bánh cần sửa
    $sql = "SELECT * FROM LoaiBanh WHERE MaLoaiBanh = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $loaiBanh = $result->fetch_assoc();
    } else {
        echo "<script>alert('Không tìm thấy loại bánh!'); window.location.href='QuanLyLoaiBanh.php';</script>";
        exit;
    }
} else {
    header("Location: QuanLyLoaiBanh.php");
    exit;
}

// Nếu nhấn nút lưu thay đổi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenMoi = trim($_POST['tenMoi']);

    // Nếu không nhập gì → giữ nguyên tên cũ
    if (empty($tenMoi)) {
        $tenMoi = $loaiBanh['TenLoaiBanh'];
    }

    // Nếu tên mới trùng với tên cũ → không cần update, nhưng vẫn coi như lưu thành công
    if ($tenMoi === $loaiBanh['TenLoaiBanh']) {
        echo "<script>
                alert('Không có thay đổi nào — dữ liệu đã được giữ nguyên.');
                window.location.href = 'QuanLyLoaiBanh.php';
              </script>";
        exit;
    }

    // Cập nhật nếu có thay đổi thực sự
    $sqlUpdate = "UPDATE LoaiBanh SET TenLoaiBanh = ? WHERE MaLoaiBanh = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("si", $tenMoi, $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Cập nhật loại bánh thành công!');
                window.location.href = 'QuanLyLoaiBanh.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Lỗi khi cập nhật: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa loại bánh</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h4>Sửa loại bánh</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Mã loại bánh:</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($loaiBanh['MaLoaiBanh']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tên loại bánh hiện tại:</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($loaiBanh['TenLoaiBanh']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tên loại bánh mới:</label>
                    <input type="text" class="form-control" name="tenMoi" placeholder="Nhập tên loại bánh mới (hoặc để trống để giữ nguyên)">
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">Lưu thay đổi</button>
                    <a href="QuanLyLoaiBanh.php" class="btn btn-secondary px-4">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
