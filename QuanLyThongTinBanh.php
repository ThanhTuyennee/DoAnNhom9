<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>


<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Đăng xuất
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- End of Topbar -->

       <!-- Begin Page Content -->
<div class="container-fluid">

    <h2 class="text-center mb-4 text-primary">Quản lý thông tin bánh</h2>

    <!-- 🔘 Nút mở popup -->
   

<!-- 💬 Modal Thêm bánh -->
<div class="modal fade" id="modalThemLoai" tabindex="-1" aria-labelledby="modalThemBanhLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemBanhLabel">Thêm bánh mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">

                    <!-- Chọn loại bánh -->
                    <div class="mb-3">
                        <label for="loaiBanh" class="form-label">Loại bánh</label>
                        <select class="form-select form-control" id="loaiBanh" name="loaiBanh" required>
                            <option value="">-- Chọn loại bánh --</option>
                            <?php
                            $sqlLoai = "SELECT * FROM LoaiBanh";
                            $resLoai = $conn->query($sqlLoai);
                            if ($resLoai && $resLoai->num_rows > 0) {
                                while ($row = $resLoai->fetch_assoc()) {
                                    echo '<option value="' . $row['MaLoaiBanh'] . '">' . htmlspecialchars($row['TenLoaiBanh']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Tên bánh -->
                    <div class="mb-3">
                        <label for="tenBanh" class="form-label">Tên bánh</label>
                        <input type="text" class="form-control" id="tenBanh" name="tenBanh" placeholder="Nhập tên bánh..." required>
                    </div>

                    <!-- Giá bánh -->
                    <div class="mb-3">
                        <label for="gia" class="form-label">Giá (VNĐ)</label>
                        <input type="number" class="form-control" id="gia" name="gia" min="0" placeholder="Nhập giá bánh..." required>
                    </div>

                    <!-- Số lượng -->
                    <div class="mb-3">
                        <label for="soLuong" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" id="soLuong" name="soLuong" min="1" placeholder="Nhập số lượng bánh..." required>
                    </div>

                    <!-- Ảnh bánh -->
                    <div class="mb-3">
                        <label for="hinhAnh" class="form-label">Hình ảnh bánh</label>
                        <input type="file" class="form-control" id="hinhAnh" name="hinhAnh" accept="image/*" onchange="previewImage(event)">
                        <div class="mt-3 text-center">
                            <img id="preview" src="#" alt="Xem trước hình ảnh" style="display:none; max-width: 100%; height: 150px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd; padding: 4px;">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="themBanh" class="btn btn-success">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🧩 JavaScript xem trước ảnh -->
<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.style.display = 'block';
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        preview.src = '';
    }
}
</script>

 <?php
if (isset($_POST['themBanh'])) {
    $maLoai = $_POST['loaiBanh'];
    $tenBanh = trim($_POST['tenBanh']);
    $gia = $_POST['gia'];
    $soLuong = $_POST['soLuong'];

    // Kiểm tra có ảnh không
    $hinhAnh = "";
    if (isset($_FILES['hinhAnh']) && $_FILES['hinhAnh']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $fileName = time() . "_" . basename($_FILES['hinhAnh']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['hinhAnh']['tmp_name'], $targetFile)) {
            $hinhAnh = $targetFile;
        }
    }

    $sql = "INSERT INTO Banh (MaLoaiBanh, TenBanh, Gia, SoLuong, HinhAnh) 
            VALUES ('$maLoai', '$tenBanh', '$gia', '$soLuong', '$hinhAnh')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success mt-3'>🎉 Thêm bánh mới thành công!</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Lỗi: " . $conn->error . "</div>";
    }
}
?>


    <!-- 📋 Danh sách loại bánh -->
    <!-- 📋 Danh sách bánh -->
<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-primary mb-0">Danh sách bánh</h5>
 <div class="mb-3 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalThemLoai">
        Thêm bánh
        </button>
    </div>
    </div>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-primary">
            <tr>
                <th>Mã bánh</th>
                <th>Tên bánh</th>
                <th>Loại bánh</th>
                <th>Giá (VNĐ)</th>
                <th>Số lượng</th>
                <th>Hình ảnh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Truy vấn kết hợp bảng Banh và LoaiBanh để lấy tên loại
            $sql = "
                SELECT b.MaBanh, b.TenBanh, b.Gia, b.SoLuong, b.HinhAnh, l.TenLoaiBanh
                FROM Banh b
                JOIN LoaiBanh l ON b.MaLoaiBanh = l.MaLoaiBanh
                ORDER BY b.MaBanh ASC
            ";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($banh = $result->fetch_assoc()) {
                    $ma = htmlspecialchars($banh['MaBanh']);
                    $ten = htmlspecialchars($banh['TenBanh']);
                    $loai = htmlspecialchars($banh['TenLoaiBanh']);
                    $gia = number_format($banh['Gia'], 0, ',', '.');
                    $soluong = htmlspecialchars($banh['SoLuong']);
                    $hinh = !empty($banh['HinhAnh']) ? htmlspecialchars($banh['HinhAnh']) : 'img/no-image.png';

                    echo "
                    <tr>
                        <td>$ma</td>
                        <td>$ten</td>
                        <td>$loai</td>
                        <td>$gia</td>
                        <td>$soluong</td>
                        <td><img src='$hinh' alt='Ảnh bánh' style='width:60px; height:60px; object-fit:cover; border-radius:8px;'></td>
                        <td>
                            <a href='suaBanh.php?id=$ma' class='btn btn-warning btn-sm'>Sửa</a>
                            <a href='xoaBanh.php?id=$ma' class='btn btn-danger btn-sm' 
                               onclick=\"return confirm('Bạn có chắc muốn xóa bánh này không?')\">Xóa</a>
                        </td>
                    </tr>";
                }
            } else {
                echo '<tr><td colspan="7">Chưa có bánh nào trong danh sách.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS (bắt buộc để modal hoạt động) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->
</div>
<!-- End of Content Wrapper -->
</div>
        </div>
<?php include 'include/footer.php'; ?>