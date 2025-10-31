
<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>
<?php include 'db/connect.php'; ?> <!-- file kết nối CSDL -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
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
      <h1 class="h3 mb-4 text-gray-800">Thực hiện bán hàng</h1>

      <!-- Nội dung trang bán hàng -->
      <div class="row">
        <!-- Cột trái: danh sách sản phẩm -->
        <div class="col-lg-7">
          <h5 class="text-primary mb-3">Danh sách sản phẩm</h5>
          <table class="table table-bordered table-hover">
            <thead class="thead-light">
              <tr>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Thêm</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM Banh"; // Bảng sản phẩm
              $result = $conn->query($sql);
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['TenBanh']}</td>
                          <td>" . number_format($row['Gia'], 0, ',', '.') . " đ</td>
                          <td><button class='btn btn-sm btn-success addToCart' 
                              data-id='{$row['MaBanh']}'
                              data-name='{$row['TenBanh']}'
                              data-price='{$row['Gia']}'>
                              <i class='fas fa-cart-plus'></i> Thêm
                          </button></td>
                        </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- Cột phải: giỏ hàng và thông tin khách hàng -->
        <div class="col-lg-5">
          <h5 class="text-primary mb-3">Thông tin khách hàng</h5>
          <form id="orderForm">
            <div class="form-group">
              <label>Họ tên khách hàng</label>
              <input type="text" name="tenKH" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Số điện thoại</label>
              <input type="text" name="sdt" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Địa chỉ</label>
              <textarea name="diachi" class="form-control" rows="2" required></textarea>
            </div>

            <h5 class="text-primary mb-3">Giỏ hàng</h5>
            <table class="table table-bordered" id="cartTable">
              <thead>
                <tr>
                  <th>Sản phẩm</th>
                  <th>SL</th>
                  <th>Giá</th>
                  <th>Tổng</th>
                  <th></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <div class="text-right">
              <strong>Tổng cộng: <span id="grandTotal">0</span> đ</strong>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">
              <i class="fas fa-receipt"></i> Tạo đơn hàng
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$conn = mysqli_connect("localhost","root","","nhom9");
if(!$conn) die("Kết nối thất bại: ".mysqli_connect_error());

?>
<script>
let cart = [];
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

document.querySelectorAll('.addToCart').forEach(btn => {
  btn.addEventListener('click', function() {
    let id = this.dataset.id;
    let name = this.dataset.name;
    let price = parseFloat(this.dataset.price);
// Thêm sản phẩm vào đơn tạm
if(isset($_POST['add'])){
    $id = $_POST['maBanh'];
    $ten = $_POST['tenBanh'];
    $gia = $_POST['gia'];
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]['soluong'] += 1;
    } else {
        $_SESSION['cart'][$id] = ['ten'=>$ten,'gia'=>$gia,'soluong'=>1];
    }
}

    let existing = cart.find(item => item.id === id);
    if (existing) {
      existing.qty++;
// Thanh toán
if(isset($_POST['pay'])){
    $tenkh = $_POST['tenkh'] ?: 'Khách lẻ';
    $sdt = $_POST['sdt'] ?? '';

    if($sdt != ''){
        $resKH = mysqli_query($conn,"SELECT MaKH FROM KhachHang WHERE SDT='$sdt'");
        if(mysqli_num_rows($resKH)){
            $row = mysqli_fetch_assoc($resKH);
            $maKH = $row['MaKH'];
        } else {
            mysqli_query($conn,"INSERT INTO KhachHang (HoTen,SDT) VALUES ('$tenkh','$sdt')");
            $maKH = mysqli_insert_id($conn);
        }
    } else {
      cart.push({ id, name, price, qty: 1 });
        mysqli_query($conn,"INSERT INTO KhachHang (HoTen) VALUES ('$tenkh')");
        $maKH = mysqli_insert_id($conn);
    }

    $maNV = 1;
    $tong = 0;
    foreach($_SESSION['cart'] as $item){
        $tong += $item['gia'] * $item['soluong'];
    }

    mysqli_query($conn,"INSERT INTO DonHang (NgayLap,TongTien,MaKH,MaNV)
                        VALUES (NOW(),'$tong','$maKH','$maNV')");
    $maDon = mysqli_insert_id($conn);

    foreach($_SESSION['cart'] as $id=>$item){
        $tt = $item['gia']*$item['soluong'];
        mysqli_query($conn,"INSERT INTO ChiTietDonHang (MaDon,MaBanh,SoLuong,DonGia,ThanhTien)
                            VALUES ('$maDon','$id','".$item['soluong']."','".$item['gia']."','$tt')");
    }

    $_SESSION['cart'] = [];
    echo "<script>alert('Thanh toán thành công: ".number_format($tong)." VND');</script>";
}
// Cập nhật số lượng
if(isset($_POST['update_qty'])){
    $id = $_POST['id'];
    $qty = max(1, intval($_POST['qty'])); // số lượng tối thiểu 1
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]['soluong'] = $qty;
    }
    renderCart();
  });
});

function renderCart() {
  let tbody = document.querySelector('#cartTable tbody');
  tbody.innerHTML = '';
  let total = 0;

  cart.forEach((item, index) => {
    let itemTotal = item.qty * item.price; 
    total += itemTotal;
    tbody.innerHTML += `
      <tr>
        <td>${item.name}</td>
        <td><input type="number" min="1" value="${item.qty}" class="form-control form-control-sm qtyInput" data-index="${index}"></td>
        <td>${item.price.toLocaleString()} đ</td>
        <td>${itemTotal.toLocaleString()} đ</td>
        <td><button class="btn btn-sm btn-danger removeItem" data-index="${index}"><i class="fas fa-trash"></i></button></td>
      </tr>
    `;
  });

  document.getElementById('grandTotal').innerText = total.toLocaleString();
  attachCartEvents();
}

function attachCartEvents() {
  document.querySelectorAll('.qtyInput').forEach(input => {
    input.addEventListener('change', function() {
      let index = this.dataset.index;
      cart[index].qty = parseInt(this.value);
      renderCart();
    });
  });

  document.querySelectorAll('.removeItem').forEach(btn => {
    btn.addEventListener('click', function() {
      let index = this.dataset.index;
      cart.splice(index, 1);
      renderCart();
    });
  });
// Xóa sản phẩm
if(isset($_POST['remove_id'])){
    $id = $_POST['remove_id'];
    unset($_SESSION['cart'][$id]);
}

document.getElementById('orderForm').addEventListener('submit', function(e) {
  e.preventDefault();
  if (cart.length === 0) {
    alert('⚠️ Giỏ hàng đang trống!');
    return;
  }

  let customer = {
    tenKH: this.tenKH.value,
    sdt: this.sdt.value,
    diachi: this.diachi.value
  };

  console.log('Khách hàng:', customer);
  console.log('Giỏ hàng:', cart);

  alert('✅ Đơn hàng đã được tạo thành công!');
  cart = [];
  renderCart();
  this.reset();
});
</script>

<div class="container-fluid">
    <div class="row">

        <!-- Cột trái: danh sách sản phẩm -->
        <div class="col-md-6">
    <h5>Danh sách sản phẩm</h5>

    <?php
    // Lấy tất cả loại bánh
    $resLoai = mysqli_query($conn, "SELECT * FROM LoaiBanh");
    $first = true; // tab đầu tiên active
    echo '<ul class="nav nav-tabs mb-2" role="tablist">';
    while($loai = mysqli_fetch_assoc($resLoai)){
        $active = $first ? 'active' : '';
        echo '<li class="nav-item">
                <a class="nav-link '.$active.'" id="tab'.$loai['MaLoaiBanh'].'" data-toggle="tab" href="#content'.$loai['MaLoaiBanh'].'" role="tab">'.$loai['TenLoaiBanh'].'</a>
              </li>';
        $first = false;
    }
    echo '</ul>';

    // Nội dung tab
    $resLoai = mysqli_query($conn, "SELECT * FROM LoaiBanh");
    $first = true;
    echo '<div class="tab-content" style="max-height:500px; overflow-y:auto; border:1px solid #ddd; padding:5px; border-radius:5px; background:#fff;">';
    while($loai = mysqli_fetch_assoc($resLoai)){
        $active = $first ? 'show active' : '';
        echo '<div class="tab-pane fade '.$active.'" id="content'.$loai['MaLoaiBanh'].'" role="tabpanel">';
        echo '<div class="row">';
        // Lấy sản phẩm theo loại
        $resSP = mysqli_query($conn, "SELECT * FROM Banh WHERE MaLoaiBanh=".$loai['MaLoaiBanh']);
        while($row = mysqli_fetch_assoc($resSP)){
            echo '<div class="col-6 mb-1">
                    <form method="post">
                        <input type="hidden" name="maBanh" value="'.$row['MaBanh'].'">
                        <input type="hidden" name="tenBanh" value="'.$row['TenBanh'].'">
                        <input type="hidden" name="gia" value="'.$row['Gia'].'">
                        <button type="submit" name="add" class="btn btn-primary btn-block" style="padding:4px; font-size:12px;">
                            '.$row['TenBanh'].'<br>'.number_format($row['Gia']).' VND
                        </button>
                    </form>
                  </div>';
        }
        echo '</div></div>';
        $first = false;
    }
    echo '</div>';
    ?>
</div>

        <!-- Cột phải: khách hàng + đơn hàng tạm + thanh toán -->
       <!-- Cột phải: khách hàng + đơn hàng tạm + thanh toán -->
<div class="col-md-6">
    <h5>Khách hàng</h5>
    <form method="post">
        <input type="text" name="tenkh" placeholder="Tên khách" class="form-control mb-2">
        <input type="text" name="sdt" placeholder="SĐT" class="form-control mb-3">

        <h5>Đơn hàng tạm</h5>
        <div style="max-height:300px; overflow-y:auto; border:1px solid #ddd; padding:5px; border-radius:5px; background:#fff;">
    <table class="table table-bordered mb-0">
        <thead>
            <tr>
                <th>Tên</th>
                <th>SL</th>
                <th>Giá</th>
                <th>Thành tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        foreach($_SESSION['cart'] as $id=>$item){
            $tt = $item['gia']*$item['soluong'];
            $total += $tt;
            echo "<tr>
                    <td>{$item['ten']}</td>
<td>
    <form method='post' style='display:flex; gap:5px; align-items:center;'>
        <input type='hidden' name='id' value='$id'>
        <input type='number' name='qty' value='{$item['soluong']}' min='1' class='no-spin' style='width:60px;'>

    </form>
</td>

<!-- Thêm CSS để bỏ dấu tăng giảm -->
<style>
/* Chrome, Safari, Edge, Opera */
input.no-spin::-webkit-outer-spin-button,
input.no-spin::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input.no-spin[type=number] {
    -moz-appearance: textfield;
}
</style>

                    <td>".number_format($item['gia'])."</td>
                    <td>".number_format($tt)."</td>
                    <td>
                        <form method='post' style='margin:0;'>
                            <input type='hidden' name='remove_id' value='$id'>
                            <button type='submit' class='btn btn-sm btn-danger'>Xóa</button>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<p class="mt-2 font-weight-bold">Tổng: <?php echo number_format($total); ?> VND</p>

   

        <button type="submit" id="payBtn" name="pay" class="btn btn-success btn-block mt-3" disabled>Thanh toán</button>

    </form>
</div>
