# 📋 PHÂN CÔNG CÔNG VIỆC NHÓM - RESTAURANT MANAGEMENT SYSTEM

**Dự án:** Hệ thống Quản lý Nhà hàng (PHP MVC)  
**Thành viên:** 5 người  
**Thời gian dự kiến:** 2-3 tuần

---

## 👥 PHÂN CÔNG CHI TIẾT

### 🔵 **THÀNH VIÊN 1: Authentication & User Management**

**Trách nhiệm:** Hệ thống xác thực và quản lý người dùng

**File cần nắm (hàm chính):**
- [app/controllers/AuthController.php](app/controllers/AuthController.php) – `login()` render trang, `doLogin()` validate+JWT, `logout()` clear session, `verify()`/`refresh()` check/renew token, `register()` render đăng ký.
- [app/controllers/UserController.php](app/controllers/UserController.php) – `requireAdmin()` guard, `index()` list, `create()`/`store()` tạo user (check duplicate), `edit()`/`update()` cập nhật, `delete()` xóa (khóa tự xóa mình).
- [app/models/User.php](app/models/User.php) – `findByUsername()`, `authenticate()` (password_verify), `generateJWTPayload()`, `createUser()`/`updateUser()` chuẩn hóa hash.
- [app/views/auth/](app/views/auth/) – Form login/register, JS fetch login.
- [app/views/user/](app/views/user/) – Form CRUD user, flash message.
- [helpers/JWT.php](helpers/JWT.php) – `encode()`/`decode()` JWT, `getCurrentUser()` lấy user từ token/cookie.
- [config/jwt.php](config/jwt.php) – Secret/expiry; [config/config.php](config/config.php) – BASE_URL, env.

#### Công việc chính:
1. **Hoàn thiện Authentication**
   - ✅ Login/Logout (Đã hoàn thành)
   - 🔄 Trang Register - Xử lý đăng ký user mới
   - 🔄 Forgot Password - Khôi phục mật khẩu qua email
   - 🔄 Change Password - Đổi mật khẩu trong profile
   - 🔄 Two-Factor Authentication (2FA) - Tùy chọn

2. **User Management Module**
   - ✅ CRUD Users (Đã có controller/model/views)
   - 🔄 User Profile - Trang cá nhân, chỉnh sửa thông tin
   - 🔄 User Activity Log - Lịch sử hoạt động
   - 🔄 Role & Permission Management - Chi tiết phân quyền

3. **Security Enhancement**
   - 🔄 Rate Limiting cho login
   - 🔄 CSRF Protection
   - 🔄 XSS Protection cho tất cả form inputs
   - 🔄 Session Management cải tiến

#### Deliverables:
- [ ] Trang đăng ký hoàn chỉnh với validation
- [ ] Chức năng forgot/reset password
- [ ] User profile page với avatar upload
- [ ] Documentation về authentication flow
- [ ] Unit tests cho AuthController

**Ước tính:** 15-20 giờ

---

### 🟢 **THÀNH VIÊN 2: Inventory Management (Kho)**

**Trách nhiệm:** Quản lý kho nguyên liệu

**File cần nắm (hàm chính):**
- [app/controllers/InventoryReceiptController.php](app/controllers/InventoryReceiptController.php) – `index()` list phiếu, `create()`/`create_from_restock()` load nguyên liệu, `store()` tạo phiếu + detail, `edit()`/`update()` sửa + replace detail; dùng `getAllWithCreator()`, `getDetails()` từ model.
- [app/controllers/InventoryIssueController.php](app/controllers/InventoryIssueController.php) – Tương tự receipt: `index()/create()/store()/edit()/update()`; cần check tồn khi xuất (bổ sung logic).
- [app/controllers/IngredientController.php](app/controllers/IngredientController.php) – `index()/create()/store()/edit()/update()/delete()` CRUD nguyên liệu.
- [app/controllers/IngredientCategoryController.php](app/controllers/IngredientCategoryController.php) – CRUD danh mục.
- Model: [InventoryReceipt.php](app/models/InventoryReceipt.php) (`getAllWithCreator()`, `getDetails()`), [InventoryIssue.php](app/models/InventoryIssue.php) (issue detail), [Ingredient.php](app/models/Ingredient.php) (tìm/insert/update stock fields), [IngredientCategory.php](app/models/IngredientCategory.php).
- View: [app/views/inventory_receipt/](app/views/inventory_receipt/) (form nhập, detail lặp ingredient_id/qty/unit_price), [app/views/inventory_issue/](app/views/inventory_issue/) (form xuất, kiểm tồn), [app/views/ingredient/](app/views/ingredient/) & [app/views/ingredient_category/](app/views/ingredient_category/) (UI CRUD).
- [database/schema.sql](database/schema.sql) – Bảng inventory_receipt/issue + detail, ingredient, inventory_log để hiểu quan hệ.

#### Công việc chính:
1. **Inventory Receipt (Phiếu Nhập Kho)**
   - ✅ CRUD Inventory Receipt (Đã có cơ bản)
   - 🔄 Tính năng tìm kiếm và lọc phiếu nhập
   - 🔄 In phiếu nhập PDF
   - 🔄 Tự động cập nhật tồn kho khi nhập
   - 🔄 Validation: Không cho nhập số âm, kiểm tra supplier

2. **Inventory Issue (Phiếu Xuất Kho)**
   - ✅ CRUD Inventory Issue (Đã có cơ bản)
   - 🔄 Kiểm tra tồn kho trước khi xuất
   - 🔄 Cảnh báo khi xuất vượt quá tồn
   - 🔄 Liên kết với Sale Order (tự động xuất khi có đơn)
   - 🔄 In phiếu xuất PDF

3. **Stock Management**
   - 🔄 Trang tổng quan tồn kho real-time
   - 🔄 Cảnh báo hết hàng (min_stock alert)
   - 🔄 Stock Adjustment - Điều chỉnh/kiểm kê
   - 🔄 Inventory Log - Lịch sử xuất nhập tồn

4. **Ingredient & Category**
   - ✅ CRUD Ingredient (Đã có)
   - ✅ CRUD Ingredient Category (Đã có)
   - 🔄 Import Excel cho nguyên liệu
   - 🔄 Export báo cáo tồn kho ra Excel

#### Deliverables:
- [ ] Chức năng in phiếu nhập/xuất PDF
- [ ] Dashboard tồn kho với biểu đồ
- [ ] Cảnh báo tự động khi hết hàng
- [ ] Import/Export Excel
- [ ] Unit tests cho Inventory modules

**Ước tính:** 18-22 giờ

---

### 🟡 **THÀNH VIÊN 3: Menu & Recipe Management**

**Trách nhiệm:** Quản lý thực đơn và công thức món ăn

**File cần nắm (hàm chính):**
- [app/controllers/MenuItemController.php](app/controllers/MenuItemController.php) – `index()` list món, `create()/store()` thêm món (code, price, desc), `edit()/update()` chỉnh, `delete()` xóa.
- [app/controllers/RecipeController.php](app/controllers/RecipeController.php) – `index()` xem công thức theo món, `create()/store()` thêm nguyên liệu vào món, `edit()/update()` chỉnh định lượng, `select_menu()` chọn món trước khi gán recipe.
- Model: [MenuItem.php](app/models/MenuItem.php) – CRUD món, [Recipe.php](app/models/Recipe.php) – Lưu recipe (menu_id, ingredient_id, qty).
- View: [app/views/menu_item/](app/views/menu_item/) – Form món, [app/views/recipe/](app/views/recipe/) – Form thêm/sửa nguyên liệu cho món, [select_menu.php](app/views/recipe/select_menu.php) – chọn món.
- [database/schema.sql](database/schema.sql) – Bảng menu_item, recipe, liên kết ingredient để tính cost.

#### Công việc chính:
1. **Menu Item Management**
   - ✅ CRUD Menu Items (Đã có cơ bản)
   - 🔄 Upload hình ảnh món ăn
   - 🔄 Categories cho món ăn (Khai vị, Chính, Tráng miệng...)
   - 🔄 Trạng thái món (Available, Out of Stock, Discontinued)
   - 🔄 Tìm kiếm và lọc món ăn

2. **Recipe Management**
   - ✅ CRUD Recipe (Đã có cơ bản)
   - 🔄 Giao diện tốt hơn để thêm nguyên liệu cho món
   - 🔄 Tính toán cost món ăn dựa trên recipe
   - 🔄 Tính profit margin (giá bán - giá vốn)
   - 🔄 Kiểm tra tồn kho khi tạo món mới

3. **Menu Planning**
   - 🔄 Menu của ngày/tuần/tháng
   - 🔄 Popular dishes report
   - 🔄 Dish profitability analysis
   - 🔄 Suggestion món dựa trên tồn kho

4. **Nutritional Info (Bonus)**
   - 🔄 Thêm thông tin dinh dưỡng cho món ăn
   - 🔄 Allergen warnings
   - 🔄 Calories calculator

#### Deliverables:
- [ ] Upload và quản lý hình ảnh món ăn
- [ ] Recipe builder interface (drag-drop ingredients)
- [ ] Cost calculation cho từng món
- [ ] Menu report với biểu đồ
- [ ] Documentation về recipe system

**Ước tính:** 16-20 giờ

---

### 🟠 **THÀNH VIÊN 4: Sales & Table Management**

**Trách nhiệm:** Quản lý bán hàng và bàn ăn

**File cần nắm (hàm chính):**
- [app/controllers/RestaurantTableController.php](app/controllers/RestaurantTableController.php) – `index()` list bàn, `create()/store()` thêm bàn, `edit()/update()` chỉnh thông tin/trạng thái, `delete()` xóa.
- [app/controllers/ReservationController.php](app/controllers/ReservationController.php) – `index()` lịch đặt, `create()/store()` đặt bàn (thời gian, khách), `edit()/update()` đổi giờ/bàn, `delete()` hủy, check trùng giờ.
- [app/controllers/SaleOrderController.php](app/controllers/SaleOrderController.php) – `index()` list đơn, `create()/store()` tạo đơn và add item, `addItem()` thêm món vào đơn hiện có, `edit()/update()` cập nhật, `delete()` hủy; tính tổng/giảm giá.
- Model: [RestaurantTable.php](app/models/RestaurantTable.php) – trạng thái bàn, [Reservation.php](app/models/Reservation.php) – lưu booking, [SaleOrder.php](app/models/SaleOrder.php) – lưu order, tính toán, join details.
- View: [app/views/restaurant_table/](app/views/restaurant_table/), [app/views/reservation/](app/views/reservation/), [app/views/sale_order/](app/views/sale_order/) – form CRUD, add item, tổng tiền.
- [public/js/validation.js](public/js/validation.js) – Validate input front-end.

#### Công việc chính:
1. **Restaurant Table Management**
   - ✅ CRUD Tables (Đã có cơ bản)
   - 🔄 Floor/Zone management (Tầng 1, 2, VIP...)
   - 🔄 Real-time table status (Empty, Occupied, Reserved, Cleaning)
   - 🔄 QR Code cho mỗi bàn
   - 🔄 Table map visualization

2. **Reservation System**
   - ✅ CRUD Reservations (Đã có cơ bản)
   - 🔄 Email/SMS confirmation cho khách đặt bàn
   - 🔄 Check-in/Check-out flow
   - 🔄 Conflict detection (không cho đặt trùng giờ)
   - 🔄 Calendar view cho reservations

3. **Sale Order Management**
   - ✅ CRUD Sale Orders (Đã có cơ bản)
   - 🔄 POS Interface - Giao diện bán hàng nhanh
   - 🔄 Add multiple items to order
   - 🔄 Calculate total, tax, discount
   - 🔄 Payment methods (Cash, Card, Transfer)
   - 🔄 Print bill/invoice
   - 🔄 Split bill functionality

4. **Order Kitchen Integration**
   - 🔄 Kitchen Display System (KDS) - Màn hình bếp
   - 🔄 Order status tracking (Pending → Cooking → Ready → Served)
   - 🔄 Order notifications

#### Deliverables:
- [ ] Table map với trạng thái real-time
- [ ] POS interface hoàn chỉnh
- [ ] In hóa đơn PDF
- [ ] Reservation calendar view
- [ ] Kitchen Display System (KDS)

**Ước tính:** 20-24 giờ

---

### 🔴 **THÀNH VIÊN 5: Reports, Expenses & Dashboard**

**Trách nhiệm:** Báo cáo, chi phí và dashboard

**File cần nắm (hàm chính):**
- [app/controllers/DashboardController.php](app/controllers/DashboardController.php) – `index()` gom số liệu tổng quan (doanh thu, chi phí, tồn) để render dashboard.
- [app/controllers/ExpenseController.php](app/controllers/ExpenseController.php) – `index()` list chi phí, `create()/store()` thêm, `edit()/update()` sửa, `delete()` xóa; dùng flash message.
- [app/controllers/ReportController.php](app/controllers/ReportController.php) – `index()` trang chọn báo cáo, `stock_report()` xem tồn kho, `add_stock_out()` xuất nhanh từ báo cáo; chuẩn bị data cho view.
- Model: [Expense.php](app/models/Expense.php) – CRUD chi phí; các model kho/bán hàng được reuse trong Report.
- View: [app/views/dashboard/index.php](app/views/dashboard/index.php) – render cards/chart placeholders, [app/views/expense/](app/views/expense/) – form/list chi phí, [app/views/report/](app/views/report/) – stock report, add stock out.
- [database/schema.sql](database/schema.sql) – Bảng expense, audit_log, inventory_log; nắm field để build chart.
- [public/css/dashboard.css](public/css/dashboard.css), [public/css/shadcn.css](public/css/shadcn.css) – Style dashboard/cards/form.

#### Công việc chính:
1. **Dashboard Enhancement**
   - ✅ Basic Dashboard (Đã có)
   - 🔄 Real-time statistics cards
   - 🔄 Charts: Doanh thu theo ngày/tuần/tháng
   - 🔄 Charts: Top selling dishes
   - 🔄 Charts: Inventory value
   - 🔄 Quick actions panel

2. **Expense Management**
   - ✅ CRUD Expenses (Đã có cơ bản)
   - 🔄 Expense categories (Rent, Salary, Utilities...)
   - 🔄 Recurring expenses - Chi phí định kỳ
   - 🔄 Expense approval workflow
   - 🔄 Budget tracking

3. **Report System**
   - ✅ Basic Reports (Đã có phần stock report)
   - 🔄 Sales Report (theo ngày/tuần/tháng/năm)
   - 🔄 Revenue vs Expense Report
   - 🔄 Profit/Loss Statement
   - 🔄 Inventory Turnover Report
   - 🔄 Employee Performance Report (nếu có track)
   - 🔄 Customer Report (frequent customers)

4. **Export & Analytics**
   - 🔄 Export tất cả reports ra PDF
   - 🔄 Export ra Excel
   - 🔄 Email reports tự động (scheduled)
   - 🔄 Data visualization với Chart.js
   - 🔄 Filter by date range cho tất cả reports

5. **Audit Log**
   - 🔄 Xem và quản lý audit logs
   - 🔄 Filter logs by user, action, date
   - 🔄 Export logs

#### Deliverables:
- [ ] Dashboard với 5+ charts và real-time data
- [ ] Expense management hoàn chỉnh
- [ ] 5+ loại reports khác nhau
- [ ] Export PDF/Excel cho tất cả reports
- [ ] Audit log viewer

**Ước tính:** 20-25 giờ

---

## 🎯 CÔNG VIỆC CHUNG (Tất cả thành viên)

### 1. **Code Quality**
- [ ] Tuân thủ coding standards trong MVC_RULES.md
- [ ] Comment code rõ ràng (PHPDoc)
- [ ] Error handling đầy đủ
- [ ] Input validation cho tất cả form

### 2. **UI/UX**
- [ ] Responsive design cho mobile
- [ ] Loading states cho tất cả actions
- [ ] Success/Error notifications
- [ ] Confirm dialogs cho delete actions
- [ ] Accessibility (ARIA labels)

### 3. **Testing**
- [ ] Manual testing các chức năng đã làm
- [ ] Cross-browser testing (Chrome, Firefox, Edge)
- [ ] Test trên mobile devices
- [ ] Viết test cases document

### 4. **Documentation**
- [ ] Update README.md nếu có thay đổi
- [ ] API documentation cho controllers
- [ ] User manual cho end-users
- [ ] Developer guide

---

## 📅 LỊCH TRÌNH ĐỀ XUẤT

### **Tuần 1:** Core Features
- Mỗi người hoàn thành 50% công việc được giao
- Daily standup meetings (15 phút)
- Code review lẫn nhau

### **Tuần 2:** Advanced Features & Integration
- Hoàn thành 90% features
- Integration testing giữa các modules
- Bug fixing

### **Tuần 3:** Polish & Documentation
- UI/UX improvements
- Performance optimization
- Complete documentation
- Prepare for deployment

---

## 🔄 QUY TRÌNH LÀM VIỆC

### **Git Workflow:**
```bash
# Mỗi người tạo branch riêng
git checkout -b feature/your-name-feature-name

# Làm việc và commit thường xuyên
git add .
git commit -m "feat: add user profile page"

# Push lên remote
git push origin feature/your-name-feature-name

# Tạo Pull Request để review
# Sau khi được approve → merge vào main
```

### **Branch Naming:**
- `feature/user-profile` - Feature mới
- `fix/login-bug` - Bug fix
- `refactor/auth-controller` - Refactoring
- `docs/api-documentation` - Documentation

### **Commit Message:**
- `feat:` - Feature mới
- `fix:` - Sửa bug
- `refactor:` - Refactor code
- `docs:` - Documentation
- `style:` - Formatting, CSS
- `test:` - Testing

---

## 📊 TIÊU CHÍ ĐÁNH GIÁ

### **Chất lượng code (40%)**
- Code clean, dễ đọc, dễ maintain
- Tuân thủ MVC pattern
- Error handling đầy đủ
- Security best practices

### **Chức năng (30%)**
- Hoàn thành đúng requirements
- Không có bug nghiêm trọng
- Logic xử lý chính xác

### **UI/UX (15%)**
- Giao diện đẹp, responsive
- User-friendly
- Consistent design

### **Documentation (10%)**
- Code comments đầy đủ
- Technical documentation
- User guide

### **Teamwork (5%)**
- Hỗ trợ thành viên khác
- Code review quality
- Communication

---

## 🆘 HỖ TRỢ & LIÊN LẠC

### **Daily Standup (Mỗi ngày 9:00 AM):**
1. Hôm qua làm gì?
2. Hôm nay làm gì?
3. Có vướng mắc gì không?

### **Communication Channels:**
- **Urgent:** Phone/Zalo group
- **Discussion:** Slack/Discord channel
- **Code Review:** GitHub Pull Requests
- **Documentation:** Google Docs

### **Technical Issues:**
- Tạo issue trên GitHub
- Tag người có thể hỗ trợ
- Thảo luận trong team meeting

---

## 📚 TÀI LIỆU THAM KHẢO

- [README.md](README.md) - Hướng dẫn cài đặt
- [INSTALLATION.md](INSTALLATION.md) - Chi tiết cài đặt
- [MVC_RULES.md](MVC_RULES.md) - Quy tắc coding
- [database/schema.sql](database/schema.sql) - Database structure
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)
- [PHP PDO Tutorial](https://www.php.net/manual/en/book.pdo.php)

---

## ✅ CHECKLIST HOÀN THÀNH

### Mỗi thành viên cần:
- [ ] Tạo branch riêng cho features của mình
- [ ] Code đầy đủ CRUD operations
- [ ] Validation cho tất cả inputs
- [ ] Error handling
- [ ] Responsive UI
- [ ] Viết comments/documentation
- [ ] Test thủ công tất cả features
- [ ] Tạo Pull Request
- [ ] Code review cho ít nhất 2 PRs của người khác
- [ ] Fix bugs từ code review
- [ ] Merge vào main branch

---

## 🎉 KẾT THÚC DỰ ÁN

Khi hoàn thành tất cả:
1. **Deployment** lên server
2. **User Acceptance Testing** (UAT)
3. **Training** cho người dùng cuối
4. **Handover documentation**
5. **Celebration!** 🎊

---

**Lưu ý:** Đây là phân công đề xuất. Có thể điều chỉnh dựa trên năng lực và sở thích của từng thành viên. Quan trọng nhất là teamwork và communication!

**Good luck và happy coding! 💻🚀**
