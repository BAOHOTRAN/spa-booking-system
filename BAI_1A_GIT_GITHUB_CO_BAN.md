# BÀI 1A: GIT/GITHUB CƠ BẢN

## Thông tin sinh viên
- **Tên GitHub:** BAOHOTRAN
- **Email:** tqbao200468@gmail.com
- **Tên Repository:** spa-booking-system

---

## BƯỚC 1: CÀI ĐẶT GIT VÀ TẠO TÀI KHOẢN GITHUB

### 1.1. Tải Git từ web để quản lý version
1. Truy cập: https://git-scm.com/download/win
2. Tải file installer cho Windows
3. Chạy file .exe và cài đặt với các tùy chọn mặc định
4. **QUAN TRỌNG:** Chọn "Git from the command line and also from 3rd-party software"

### 1.2. Tạo tài khoản GitHub
1. Truy cập: https://github.com
2. Đăng ký tài khoản với username: **BAOHOTRAN**
3. Xác nhận email: **tqbao200468@gmail.com**

### 1.3. Kiểm tra cài đặt Git:
```bash
git --version
```

---

## BƯỚC 2: SET UP PROJECT - KHỞI TẠO GIT TRONG DỰ ÁN

### 2.1. Khởi tạo Git trong dự án:
```bash
# Di chuyển vào thư mục dự án
cd D:\spa_project

# Khởi tạo git repository
git init
```

**Kết quả:** Git khởi tạo thành công, nhánh chính là **master** (hoặc **main**)

### 2.2. Hiểu về 3 vùng trong Git:
- **Working Directory (Màu đỏ):** Vùng các file đang làm việc, chưa được add
- **Staging Area (Màu xanh lá):** Vùng các file đã add, chuẩn bị commit  
- **Repository (Local):** Vùng lưu trữ các commit

### 2.3. Cấu hình user:
```bash
git config user.name "BAOHOTRAN"
git config user.email "tqbao200468@gmail.com"
```

---

## BƯỚC 3: ADD FILE VÀO STAGING VÀ COMMIT ĐẦU TIÊN

### File .gitignore cần tạo:
```gitignore
# IDE files
.vscode/
.idea/

# OS files
.DS_Store
Thumbs.db

# Temporary files
*.tmp
*.temp
temp.txt

# Log files
*.log

# Environment files
.env
.env.local

# Upload files (keep folder structure but ignore content)
uploads/*
!uploads/.gitkeep

# Cache files
cache/
*.cache

# Backup files
*.bak
*.backup
```

### 3.1. Kiểm tra trạng thái file:
```bash
git status
```
**Kết quả:** File hiển thị màu đỏ (ở vùng Working)

### 3.2. Add file vào Staging Area:
```bash
# Add tất cả file vào staging
git add .
```
**Kết quả:** File chuyển sang màu xanh lá (ở vùng Staging)

### 3.3. Commit - đóng gói file vào Repository:
```bash
# Commit với message mô tả
git commit -m "Initial commit: SPA booking system"
```

**Giải thích Commit:** Commit như việc đóng gói tất cả file thành kiện hàng, dán nhãn mô tả, lưu vào repository local để sẵn sàng đẩy lên GitHub.

---

## BƯỚC 4: TẠO REPOSITORY TRÊN GITHUB VÀ KẾT NỐI

### 4.1. Tạo Repository trên GitHub:
1. Đăng nhập GitHub với tài khoản **BAOHOTRAN**
2. Click "New repository"
3. Đặt tên: **spa-booking-system**
4. Để public, không tích "Initialize with README"
5. Click "Create repository"

### 4.2. Kết nối Local với GitHub:
Copy 3 lệnh cuối từ GitHub và chạy:
```bash
git remote add origin https://github.com/BAOHOTRAN/spa-booking-system.git
git branch -M main
git push -u origin main
```

**Kết quả:** Đã push thành công lên GitHub. Kiểm tra trên trang repo để xem kết quả.

---

## BƯỚC 5: PHÂN NHÁNH THEO CHUẨN GIT FLOW

### 5.1. Tạo nhánh develop (nhánh trung gian):
```bash
# Tạo và chuyển sang nhánh develop
git checkout -b develop
```

### 5.2. Tạo nhánh feature để phát triển tính năng:
```bash
# Tạo nhánh feature
git checkout -b feature/new-function
```

### 5.3. Thêm file mới và commit:
```bash
# Tạo file mới (ví dụ: about.html)
echo "<h1>About Page</h1>" > about.html

# Add vào staging
git add .

# Commit lên nhánh feature
git commit -m "Add about page"

# Push nhánh feature lên GitHub
git push origin feature/new-function
```

---

## BƯỚC 6: MERGE NHÁNH VÀ PULL REQUEST

### 6.1. Merge local (cách 1):
```bash
# Chuyển sang nhánh develop
git checkout develop

# Pull code mới từ GitHub về
git pull origin develop

# Merge nhánh feature vào develop
git merge feature/new-function

# Push develop lên GitHub
git push origin develop
```

### 6.2. Tạo Pull Request trên GitHub (cách 2 - khuyến nghị):
1. Vào GitHub repository
2. Click "Compare & pull request" 
3. Chọn merge từ `feature/new-function` vào `main`
4. Viết mô tả và click "Create pull request"
5. Review và click "Merge pull request"

### 6.3. Cập nhật local sau khi merge:
```bash
# Chuyển về nhánh main
git checkout main

# Pull toàn bộ code mới từ GitHub về local
git pull origin main
```

---

## BƯỚC 7: CLONE TỪ GITHUB VỀ LOCAL (DEMO)

### Lệnh thực hiện:
```bash
# Tạo thư mục mới để demo clone
cd ..
mkdir spa-clone-demo
cd spa-clone-demo

# Clone repository từ GitHub
git clone https://github.com/BAOHOTRAN/spa-booking-system.git

# Vào thư mục đã clone
cd spa-booking-system

# Kiểm tra các nhánh remote
git branch -r

# Checkout các nhánh khác
git checkout develop
git checkout feature/new-function
```

---

## BƯỚC 8: GIẢI QUYẾT XUNG ĐỘT (CONFLICT)

### 8.1. Tạo xung đột:
```bash
# Về thư mục gốc
cd D:\spa_project

# Thay đổi trên nhánh main
git checkout main
# Sửa file index.php: thêm "Version 2.0" 
git add .
git commit -m "Update version on main"

# Thay đổi trên nhánh develop  
git checkout develop
# Sửa cùng dòng trong index.php: thêm "Version 1.5"
git add .
git commit -m "Update version on develop"
```

### 8.2. Merge và giải quyết conflict:
```bash
# Merge develop vào main
git checkout main
git merge develop
# Sẽ có conflict!
```

**File sẽ hiển thị:**
```
<<<<<<< HEAD
Version 2.0
=======
Version 1.5  
>>>>>>> develop
```

**Sửa thành:**
```
Version 2.1 - Merged
```

```bash
# Hoàn tất merge
git add .
git commit -m "Resolve merge conflict"
```

---

## BƯỚC 9: KIỂM TRA KẾT QUẢ CUỐI CÙNG

### Lệnh kiểm tra:
```bash
# Kiểm tra log
git log --oneline --graph --all

# Kiểm tra remote
git remote -v

# Kiểm tra nhánh
git branch -a

# Kiểm tra trạng thái
git status
```

---

## TÓM TẮT CÁC FILE CẦN TẠO:

### Files BẮT BUỘC:
1. `.gitignore` - Ignore các file không cần thiết

### Files sẽ chỉnh sửa:
1. `index.php` - Thay đổi version để tạo conflict và merge
2. `about.html` - File demo cho feature branch

### Cấu trúc thư mục cuối cùng:
```
spa_project/
├── .git/                    # Git repository
├── .gitignore              # Git ignore file
├── admin/                  # Admin files
├── assets/                 # CSS, JS, Images
├── config/                 # Database config
├── includes/               # Header, Footer
├── public/                 # Public pages
├── sql/                    # Database files
├── uploads/                # Upload folder
├── about.html              # Feature demo file
└── index.php              # Main entry point
```

---

## KIỂM TRA HOÀN THÀNH:

### YÊU CẦU BẮT BUỘC:
✅ Tạo repo trên local
✅ Phân nhánh  
✅ Tạo repo trên GitHub và kết nối
✅ Push kết quả lên GitHub
✅ Clone từ GitHub về local
✅ Merge các nhánh trong git về main
✅ Giải quyết xung đột

**Tỷ lệ hoàn thành yêu cầu cơ bản: 100%**

**Kết quả đạt được:**
- Biết sử dụng Git cơ bản
- Có thể làm việc nhóm với GitHub  
- Hiểu workflow: Working → Staging → Repository → Remote
- Thành thạo branching và merging
- Biết giải quyết conflict
- Có thể clone và đồng bộ code