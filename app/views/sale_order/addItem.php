<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-plus-circle me-2"></i>Thêm món ăn vào đơn hàng #<?php echo $order['id']; ?></h3>
            <a href="<?php echo BASE_URL; ?>/sale_order" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Danh sách món ăn</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã</th>
                                    <th>Tên món</th>
                                    <th>Giá</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($menuItems)): ?>
                                    <?php foreach ($menuItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['code']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['description'] ?? ''); ?></small>
                                            </td>
                                            <td><strong><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</strong></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="addItemToOrder(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['name']); ?>', <?php echo $item['price']; ?>)">
                                                    <i class="bi bi-plus"></i> Thêm
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có món ăn nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Chi tiết đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Bàn</small>
                            <h6><?php echo htmlspecialchars($order['table_number'] ?? 'Không xác định'); ?></h6>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Thời gian tạo</small>
                            <h6><?php echo date('d/m/Y H:i', strtotime($order['order_time'])); ?></h6>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Trạng thái</small>
                            <h6>
                                <span class="badge bg-warning">Đang phục vụ</span>
                            </h6>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Tổng tiền hiện tại</small>
                            <h5 class="text-primary"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</h5>
                        </div>

                        <hr>

                        <div id="selectedItems" class="mb-3">
                            <small class="text-muted">Các món sẽ thêm</small>
                            <div id="itemsList" class="mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Tổng tiền thêm</small>
                            <h5 class="text-success" id="totalAdd">0 đ</h5>
                        </div>

                        <button class="btn btn-primary w-100" id="submitBtn" onclick="submitAddItems()">
                            <i class="bi bi-check-circle me-1"></i>Xác nhận thêm
                        </button>
                        <a href="<?php echo BASE_URL; ?>/sale_order" class="btn btn-secondary w-100 mt-2">
                            <i class="bi bi-x-circle me-1"></i>Hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const orderId = <?php echo $order['id']; ?>;
    let selectedItems = [];

    function addItemToOrder(menuId, menuName, price) {
        // Check if item already exists
        const existingItem = selectedItems.find(item => item.menu_id === menuId);

        if (existingItem) {
            existingItem.qty += 1;
        } else {
            selectedItems.push({
                menu_id: menuId,
                name: menuName,
                price: price,
                qty: 1
            });
        }

        updateItemsList();
    }

    function updateItemsList() {
        const itemsList = document.getElementById('itemsList');
        const totalAdd = document.getElementById('totalAdd');

        if (selectedItems.length === 0) {
            itemsList.innerHTML = '<p class="text-muted">Chưa chọn món ăn</p>';
            totalAdd.textContent = '0 đ';
            return;
        }

        let html = '';
        let total = 0;

        selectedItems.forEach((item, index) => {
            const itemTotal = item.price * item.qty;
            total += itemTotal;
            html += `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                <div>
                    <div class="small"><strong>${item.name}</strong></div>
                    <div class="small text-muted">${item.price.toLocaleString()} đ x ${item.qty}</div>
                </div>
                <div class="text-end">
                    <div class="small text-success"><strong>${itemTotal.toLocaleString()} đ</strong></div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQty(${index}, -1)">-</button>
                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="removeItem(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        });

        itemsList.innerHTML = html;
        totalAdd.textContent = total.toLocaleString() + ' đ';
    }

    function updateQty(index, delta) {
        selectedItems[index].qty += delta;

        if (selectedItems[index].qty <= 0) {
            removeItem(index);
        } else {
            updateItemsList();
        }
    }

    function removeItem(index) {
        selectedItems.splice(index, 1);
        updateItemsList();
    }

    function submitAddItems() {
        if (selectedItems.length === 0) {
            alert('Vui lòng chọn ít nhất một món ăn');
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${BASE_URL}/sale_order/saveAddItems/${orderId}`;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'items';
        input.value = JSON.stringify(selectedItems);

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
</script>