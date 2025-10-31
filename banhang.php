<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>
<?php include 'include/dbconnect.php'; ?> <!-- file kết nối CSDL -->

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
              $sql = "SELECT * FROM sanpham"; // Bảng sản phẩm
              $result = $conn->query($sql);
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['TenSanPham']}</td>
                          <td>" . number_format($row['Gia'], 0, ',', '.') . " đ</td>
                          <td><button class='btn btn-sm btn-success addToCart' 
                              data-id='{$row['MaSP']}'
                              data-name='{$row['TenSanPham']}'
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

<?php include 'include/footer.php'; ?>

<!-- Script xử lý giỏ hàng -->
<script>
let cart = [];

document.querySelectorAll('.addToCart').forEach(btn => {
  btn.addEventListener('click', function() {
    let id = this.dataset.id;
    let name = this.dataset.name;
    let price = parseFloat(this.dataset.price);

    let existing = cart.find(item => item.id === id);
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ id, name, price, qty: 1 });
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
