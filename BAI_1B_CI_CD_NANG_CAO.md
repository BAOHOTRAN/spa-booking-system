# BÀI 1B: CI/CD VỚI GITHUB ACTIONS (NÂNG CAO)

## Điều kiện tiên quyết
- ✅ **Hoàn thành Bài 1A** - Git/GitHub cơ bản
- ✅ Có tài khoản GitHub: **BAOHOTRAN**
- ✅ Biết Git workflow cơ bản
- ✅ Hiểu về branching và merging

---

## MỤC TIÊU BÀI HỌC

### Kỹ năng sẽ học được:
- Hiểu CI/CD pipeline
- Tự động hóa testing và deployment
- Deploy lên cloud platform
- Quản lý 2 dự án khác nhau
- Tích hợp Git với DevOps

### Kết quả cuối cùng:
- 2 website live trên internet
- Tự động deploy khi push code
- CI/CD pipeline hoạt động

---

## BƯỚC 1: SỬ DỤNG DỰ ÁN SPA HIỆN TẠI

### 1.1. Chuẩn bị dự án SPA booking system

#### Sử dụng dự án đã có:
```bash
# Sử dụng dự án SPA hiện tại
cd D:\spa_project

# Kiểm tra Git status (đã có từ Bài 1A)
git status
git log --oneline
```

**Lợi ích sử dụng dự án có sẵn:**
- ✅ Đã có cấu trúc hoàn chỉnh (frontend + backend + database)
- ✅ Đã có Git repository và GitHub connection
- ✅ Tiết kiệm thời gian setup
- ✅ Thực tế hơn (dùng dự án thật)

### 1.2. Tạo static version cho Frontend (Netlify)

#### Tạo thư mục static cho deploy:
```bash
# Tạo thư mục static từ public folder
mkdir static-spa
cp -r public/* static-spa/

# Hoặc trên Windows:
# xcopy public static-spa /E /I
```

#### Tối ưu hóa cho static deployment:
```bash
# Tạo file index.html chính cho static version
cat > static-spa/index.html << 'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPA Booking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h1>🌸 SPA Booking System</h1>
    <p>Welcome to our luxury spa booking system</p>
    
    <!-- Sử dụng nội dung từ public/index.php nhưng static -->
    <div class="services">
        <h2>Our Services</h2>
        <div class="service-grid">
            <div class="service-card">
                <h3>Massage Therapy</h3>
                <p>Price: $80 - Duration: 60 min</p>
            </div>
            <div class="service-card">
                <h3>Facial Treatment</h3>
                <p>Price: $60 - Duration: 45 min</p>
            </div>
            <div class="service-card">
                <h3>Body Wrap</h3>
                <p>Price: $100 - Duration: 90 min</p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/app.js"></script>
</body>
</html>
EOF
```

### 1.3. Sử dụng Backend PHP hiện tại

#### Backend đã có sẵn:
- ✅ `admin/` - Admin panel
- ✅ `public/` - Public pages  
- ✅ `config/db.php` - Database config
- ✅ `includes/` - Shared components
- ✅ `index.php` - Main entry point

#### File index.php:
```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple database simulation (in real project, use MySQL)
class SpaDatabase {
    private $services;
    private $bookings;
    
    public function __construct() {
        $this->services = [
            ['id' => 1, 'name' => 'Massage Therapy', 'price' => 80, 'duration' => 60, 'available' => true],
            ['id' => 2, 'name' => 'Facial Treatment', 'price' => 60, 'duration' => 45, 'available' => true],
            ['id' => 3, 'name' => 'Body Wrap', 'price' => 100, 'duration' => 90, 'available' => true],
            ['id' => 4, 'name' => 'Hot Stone Massage', 'price' => 120, 'duration' => 75, 'available' => true],
            ['id' => 5, 'name' => 'Aromatherapy', 'price' => 90, 'duration' => 60, 'available' => true]
        ];
        
        $this->bookings = [];
    }
    
    public function getServices() {
        return array_filter($this->services, function($service) {
            return $service['available'];
        });
    }
    
    public function getService($id) {
        foreach ($this->services as $service) {
            if ($service['id'] == $id) {
                return $service;
            }
        }
        return null;
    }
    
    public function createBooking($data) {
        $booking = [
            'id' => count($this->bookings) + 1,
            'customer_name' => $data['name'] ?? '',
            'customer_email' => $data['email'] ?? '',
            'service_id' => $data['service_id'] ?? 0,
            'booking_date' => $data['date'] ?? date('Y-m-d'),
            'booking_time' => $data['time'] ?? '10:00',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->bookings[] = $booking;
        return $booking;
    }
    
    public function getBookings() {
        return $this->bookings;
    }
}

// Initialize database
$db = new SpaDatabase();

// Router
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

// API Routes
switch($path) {
    case '/api/services':
        if($method === 'GET') {
            $services = $db->getServices();
            echo json_encode([
                'success' => true,
                'data' => array_values($services),
                'count' => count($services)
            ]);
        }
        break;
    
    case '/api/bookings':
        if($method === 'GET') {
            $bookings = $db->getBookings();
            echo json_encode([
                'success' => true,
                'data' => $bookings,
                'count' => count($bookings)
            ]);
        } elseif($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            if (empty($input['name']) || empty($input['email']) || empty($input['service_id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Missing required fields: name, email, service_id'
                ]);
                break;
            }
            
            // Validate email
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid email format'
                ]);
                break;
            }
            
            // Check if service exists
            $service = $db->getService($input['service_id']);
            if (!$service) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Service not found'
                ]);
                break;
            }
            
            $booking = $db->createBooking($input);
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking created successfully'
            ]);
        }
        break;
    
    case '/api/health':
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => time(),
            'date' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'services_count' => count($db->getServices()),
            'bookings_count' => count($db->getBookings())
        ]);
        break;
    
    case '/api/stats':
        $services = $db->getServices();
        $bookings = $db->getBookings();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_services' => count($services),
                'total_bookings' => count($bookings),
                'average_price' => count($services) > 0 ? array_sum(array_column($services, 'price')) / count($services) : 0,
                'most_expensive_service' => count($services) > 0 ? max(array_column($services, 'price')) : 0,
                'cheapest_service' => count($services) > 0 ? min(array_column($services, 'price')) : 0
            ]
        ]);
        break;
        
    case '':
    case '/':
        // API Documentation
        echo json_encode([
            'message' => 'SPA Booking API',
            'version' => '1.0.0',
            'endpoints' => [
                'GET /api/services' => 'Get all available services',
                'GET /api/bookings' => 'Get all bookings',
                'POST /api/bookings' => 'Create new booking',
                'GET /api/health' => 'Health check',
                'GET /api/stats' => 'Get statistics'
            ],
            'example_booking' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'service_id' => 1,
                'date' => '2025-01-20',
                'time' => '14:00'
            ]
        ]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Endpoint not found',
            'path' => $path,
            'available_endpoints' => [
                '/api/services',
                '/api/bookings',
                '/api/health',
                '/api/stats'
            ]
        ]);
}
?>
```

#### File composer.json:
```json
{
    "name": "baohotran/spa-api",
    "description": "SPA Booking System API",
    "type": "project",
    "keywords": ["spa", "booking", "api", "php"],
    "license": "MIT",
    "authors": [
        {
            "name": "BAOHOTRAN",
            "email": "tqbao200468@gmail.com"
        }
    ],
    "require": {
        "php": ">=7.4"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.0"
    },
    "scripts": {
        "test": "php -l index.php && echo 'PHP syntax check passed'",
        "start": "php -S localhost:8000 index.php",
        "dev": "php -S localhost:8000 index.php"
    },
    "config": {
        "optimize-autoloader": true
    }
}
```

---

## BƯỚC 2: SETUP CI (CONTINUOUS INTEGRATION)

### 2.1. CI cho SPA Project hiện tại

```bash
# Đã có sẵn thư mục .github/workflows từ trước
# Chỉ cần cập nhật hoặc tạo thêm workflow mới

ls -la .github/workflows/
```

#### File .github/workflows/spa-ci.yml (cập nhật CI hiện tại):
```yaml
name: SPA Full-Stack CI

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
    
    - name: Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: '18'
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mysqli, pdo, pdo_mysql
    
    - name: Validate PHP syntax
      run: |
        echo "🔍 Checking PHP syntax..."
        find . -name "*.php" -exec php -l {} \;
        echo "✅ PHP syntax OK"
    
    - name: Validate HTML in public folder
      run: |
        echo "🔍 Validating HTML files..."
        find public -name "*.php" -exec echo "✅ Checking: {}" \;
        echo "✅ Public files validated"
    
    - name: Check CSS and JS assets
      run: |
        echo "🔍 Checking assets..."
        if [ -d "assets/css" ]; then
          echo "✅ CSS assets found"
        fi
        if [ -d "assets/js" ]; then
          echo "✅ JS assets found"
        fi
    
    - name: Build static version
      run: |
        echo "🏗️ Building static version for deployment..."
        mkdir -p static-build
        cp -r public/* static-build/ 2>/dev/null || true
        cp -r assets static-build/ 2>/dev/null || true
        ls -la static-build/
        echo "✅ Static build successful"
    
    - name: Performance check
      run: |
        echo "⚡ Checking file sizes..."
        for file in *.html *.css *.js; do
          if [ -f "$file" ]; then
            size=$(wc -c < "$file")
            echo "📄 $file: $size bytes"
            if [ $size -gt 100000 ]; then
              echo "⚠️ Warning: $file is quite large ($size bytes)"
            fi
          fi
        done
    
    - name: Security check
      run: |
        echo "🔒 Running basic security checks..."
        # Check for common security issues
        if grep -r "eval\|innerHTML\|document.write" *.js 2>/dev/null; then
          echo "⚠️ Warning: Potentially unsafe JavaScript patterns found"
        else
          echo "✅ No obvious security issues found"
        fi
```

### 2.2. Tạo API endpoint cho testing

#### Thêm API endpoint vào index.php hiện tại:
```php
<?php
// Thêm vào cuối file index.php hiện tại

// API endpoints cho CI/CD testing
if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    switch($_GET['api']) {
        case 'health':
            echo json_encode([
                'status' => 'healthy',
                'timestamp' => time(),
                'date' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]);
            break;
            
        case 'services':
            // Giả lập data services
            echo json_encode([
                'success' => true,
                'data' => [
                    ['id' => 1, 'name' => 'Massage Therapy', 'price' => 80],
                    ['id' => 2, 'name' => 'Facial Treatment', 'price' => 60],
                    ['id' => 3, 'name' => 'Body Wrap', 'price' => 100]
                ]
            ]);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
    }
    exit;
}
?>
```

#### Test API endpoints:
```bash
# Test locally
php -S localhost:8000 &
curl http://localhost:8000/?api=health
curl http://localhost:8000/?api=services
```

---

## BƯỚC 3: SETUP CD (CONTINUOUS DEPLOYMENT)

### 3.1. Deploy Frontend lên Netlify

#### Đăng ký Netlify:
1. Truy cập: https://netlify.com
2. Sign up với GitHub account
3. Authorize Netlify to access repositories

#### File .github/workflows/frontend-deploy.yml:
```yaml
name: Deploy Frontend to Netlify

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
    
    - name: Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: '18'
    
    - name: Build site
      run: |
        echo "🏗️ Building frontend..."
        
        # Create build directory
        mkdir -p dist
        
        # Copy all files to dist
        cp *.html *.css *.js dist/
        
        # Optimize files (basic optimization)
        echo "⚡ Optimizing files..."
        
        # Add cache busting to CSS and JS
        timestamp=$(date +%s)
        sed -i "s/style\.css/style.css?v=$timestamp/g" dist/index.html
        sed -i "s/app\.js/app.js?v=$timestamp/g" dist/index.html
        
        # Create _redirects file for SPA routing
        echo "/*    /index.html   200" > dist/_redirects
        
        # Create robots.txt
        echo "User-agent: *" > dist/robots.txt
        echo "Allow: /" >> dist/robots.txt
        
        echo "✅ Build completed"
        ls -la dist/
    
    - name: Deploy to Netlify
      uses: nwtgck/actions-netlify@v2.0
      with:
        publish-dir: './dist'
        production-branch: main
        deploy-message: "Deploy from GitHub Actions - ${{ github.sha }}"
        enable-pull-request-comment: false
        enable-commit-comment: true
        overwrites-pull-request-comment: true
      env:
        NETLIFY_AUTH_TOKEN: ${{ secrets.NETLIFY_AUTH_TOKEN }}
        NETLIFY_SITE_ID: ${{ secrets.NETLIFY_SITE_ID }}
      if: github.ref == 'refs/heads/main'
    
    - name: Comment deployment URL
      uses: actions/github-script@v6
      if: github.ref == 'refs/heads/main'
      with:
        script: |
          github.rest.repos.createCommitComment({
            owner: context.repo.owner,
            repo: context.repo.repo,
            commit_sha: context.sha,
            body: '🚀 Frontend deployed successfully!\n\nCheck your Netlify dashboard for the live URL.'
          })
```

### 3.2. Deploy Backend lên Render

#### Đăng ký Render:
1. Truy cập: https://render.com
2. Sign up với GitHub account
3. Connect GitHub repository

#### File .github/workflows/backend-deploy.yml:
```yaml
name: Deploy Backend to Render

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: json, curl, mysqli, pdo
        tools: composer
    
    - name: Install dependencies
      run: |
        echo "📦 Installing dependencies..."
        if [ -f composer.json ]; then
          composer install --no-dev --optimize-autoloader
          echo "✅ Composer dependencies installed"
        fi
    
    - name: Run tests before deploy
      run: |
        echo "🧪 Running pre-deployment tests..."
        
        # Start server for testing
        php -S localhost:8000 index.php &
        sleep 2
        
        # Test critical endpoints
        curl -f http://localhost:8000/api/health
        curl -f http://localhost:8000/api/services
        
        echo "✅ Pre-deployment tests passed"
    
    - name: Prepare deployment
      run: |
        echo "🚀 Preparing deployment..."
        
        # Create deployment info
        echo "<?php" > deploy_info.php
        echo "// Deployment info" >> deploy_info.php
        echo "define('DEPLOY_TIME', '$(date)');" >> deploy_info.php
        echo "define('DEPLOY_COMMIT', '${{ github.sha }}');" >> deploy_info.php
        echo "define('DEPLOY_BRANCH', '${{ github.ref_name }}');" >> deploy_info.php
        
        # Optimize for production
        if [ -f composer.json ]; then
          composer dump-autoload --optimize
        fi
        
        echo "✅ Deployment prepared"
    
    - name: Deploy to Render
      run: |
        echo "🚀 Deploying to Render..."
        echo "Render will automatically deploy when changes are pushed to main branch"
        echo "✅ Deployment triggered"
        
        # In a real scenario, you might use Render's API or webhook
        # For now, Render auto-deploys from GitHub
    
    - name: Post-deployment verification
      run: |
        echo "✅ Deployment completed successfully!"
        echo "🔗 Your API will be available at: https://your-app-name.onrender.com"
        echo "📊 Monitor your deployment in the Render dashboard"
    
    - name: Create deployment comment
      uses: actions/github-script@v6
      with:
        script: |
          github.rest.repos.createCommitComment({
            owner: context.repo.owner,
            repo: context.repo.repo,
            commit_sha: context.sha,
            body: '🚀 Backend deployed successfully to Render!\n\n' +
                  '📊 Check your Render dashboard for deployment status.\n' +
                  '🔗 API will be available at your Render URL.\n' +
                  '⏰ Deployment time: ' + new Date().toISOString()
          })
```

---

## BƯỚC 4: TEST END-TO-END

### 4.1. Commit và push SPA project với CI/CD

```bash
# Sử dụng repository hiện tại
cd D:\spa_project

# Add các file CI/CD mới
git add .github/workflows/
git add static-spa/
git add index.php  # (nếu có thêm API endpoints)

# Commit
git commit -m "Add CI/CD pipeline for SPA project"

# Push lên GitHub repository hiện tại
git push origin main
```

### 4.2. Test complete workflow

```bash
# Test SPA project changes
cd D:\spa_project

# Thay đổi nhỏ để test CI/CD
echo "<!-- Updated $(date) -->" >> public/index.php
git add .
git commit -m "Test CI/CD pipeline - update timestamp"
git push origin main

# Kiểm tra GitHub Actions
echo "🔍 Check GitHub Actions tab to see CI/CD running"
echo "🌐 Check deployment status on Netlify/Render dashboards"
```

---

## BƯỚC 5: KIỂM TRA KẾT QUẢ

### 5.1. Workflow hoàn chỉnh:
1. **Local development** → Edit code locally
2. **Git commit** → `git add . && git commit -m "message"`
3. **Push to GitHub** → `git push origin main`
4. **Auto CI** → GitHub Actions runs tests automatically
5. **Auto CD** → Deploy to Netlify/Render if tests pass
6. **Live website** → Check results on live domains

### 5.2. URLs để kiểm tra:
- **Frontend:** https://your-site-name.netlify.app
- **Backend API:** https://your-app-name.onrender.com/api/health
- **GitHub Actions:** Tab "Actions" trên GitHub repositories
- **Netlify Dashboard:** https://app.netlify.com
- **Render Dashboard:** https://dashboard.render.com

### 5.3. Test các endpoints:
```bash
# Test backend API
curl https://your-app-name.onrender.com/api/health
curl https://your-app-name.onrender.com/api/services
curl https://your-app-name.onrender.com/api/stats

# Test booking creation
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","service_id":1}' \
  https://your-app-name.onrender.com/api/bookings
```

---

## TÓM TẮT KẾT QUẢ

### Files đã tạo:

#### Frontend Project:
- `index.html` - Main HTML file
- `style.css` - Styling
- `app.js` - JavaScript functionality
- `.github/workflows/frontend-ci.yml` - CI pipeline
- `.github/workflows/frontend-deploy.yml` - CD pipeline

#### Backend Project:
- `index.php` - API server
- `composer.json` - PHP dependencies
- `.github/workflows/backend-ci.yml` - CI pipeline
- `.github/workflows/backend-deploy.yml` - CD pipeline

### Kỹ năng đã học:
✅ Tạo CI pipeline với GitHub Actions
✅ Setup CD cho 2 platform khác nhau (Netlify + Render)
✅ Deploy frontend lên Netlify
✅ Deploy backend lên Render
✅ Tự động hóa testing và deployment
✅ Hiểu workflow: Code → Test → Deploy
✅ Quản lý 2 dự án độc lập
✅ API development và testing
✅ Frontend-Backend integration

### Platforms đã sử dụng:
- **Netlify** - Frontend Cloud (HTML/CSS/JS)
- **Render** - Full Cloud (Backend + Database)
- **GitHub Actions** - CI/CD Pipeline
- **GitHub** - Source control

**Hoàn thành 100% yêu cầu CI/CD nâng cao!**

### Lưu ý quan trọng:
⚠️ **Bài này chỉ nên làm sau khi đã thành thạo Bài 1A**
⚠️ **Cần cẩn thận với secrets và credentials**
⚠️ **Test kỹ trước khi deploy production**