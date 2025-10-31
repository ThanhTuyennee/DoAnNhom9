
<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>

<!-- Content Wrapper -->
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

            <h2 class="text-center mb-4 text-primary">Quản lý loại bánh</h2>

            <div class="card mb-4 shadow-sm p-4">
                <h5 class="mb-3 text-primary" >Thêm loại bánh</h5>
                <form method="POST" action="">
    <div class="row mb-3 align-items-end">
        <div class="col-md-8">
            <label for="tenLoai" class="form-label">Tên loại bánh</label>
            <input type="text" class="form-control" id="tenLoai" name="tenLoai" placeholder="Nhập tên loại bánh..." required>
        </div>
        <div class="col-md- d-flex justify-content-end">
            <button type="submit" name="them" class="btn btn-success mt-2">Thêm</button>
        </div>
    </div>
</form>

            </div>

            <?php
            // ✅ Xử lý thêm loại bánh mới
            if (isset($_POST['them'])) {
                $tenLoai = $_POST['tenLoai'];

                $sqlThem = "INSERT INTO LoaiBanh (TenLoaiBanh) VALUES ('$tenLoai')";
                if ($conn->query($sqlThem) === TRUE) {
                    echo "<div class='alert alert-success'>Đã thêm loại bánh mới thành công!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
                }
            }
            ?>

            <!-- 📋 Danh sách loại bánh -->
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-3 text-primary">Danh sách loại bánh</h5>
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Mã loại bánh</th>
                            <th>Tên loại bánh</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
// Lấy dữ liệu từ bảng LoaiBanh
$sql = "SELECT * FROM LoaiBanh";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($loai = $result->fetch_assoc()) {
        // bảo đảm an toàn khi in ra HTML
        $ma = htmlspecialchars($loai['MaLoaiBanh']);
        $ten = htmlspecialchars($loai['TenLoaiBanh']);
        ?>
        <tr>
            <td><?= $ma ?></td>
            <td><?= $ten ?></td>
            <td>
                <a href="suaLoaiBanh.php?id=<?= urlencode($ma) ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="xoaLoaiBanh.php?id=<?= urlencode($ma) ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Bạn có chắc muốn xóa loại bánh này không?')">Xóa</a>
            </td>
        </tr>
        <?php
    }
} else {
    echo '<tr><td colspan="3">Chưa có loại bánh nào.</td></tr>';
}
?>
</tbody>

                </table>
            </div>

        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->
</div>
<!-- End of Content Wrapper -->
</div>
<?php include 'include/footer.php'; ?>
