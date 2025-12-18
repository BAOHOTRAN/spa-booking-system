<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Thực Hành - SPA Mini Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4 fade-in">
        <a href="../index.php" class="btn btn-outline-primary mb-4">
            <i class="fas fa-arrow-left me-2"></i>Về trang chủ
        </a>

        <div class="text-center mb-5">
            <h1 class="display-5 mb-3">
                <i class="fas fa-code me-3 text-primary"></i>Lab Thực Hành Web
            </h1>
        </div>

        <!-- Lab Sessions -->
        <div class="row lab-accordion">
            <?php
            $labFolders = [
                'Buoi 2' => ['title' => 'Buổi 2: PHP Cơ Bản', 'icon' => 'fas fa-play-circle'],
                'Buoi 3' => ['title' => 'Buổi 3: Functions & Arrays', 'icon' => 'fas fa-code-branch'],
                'Buoi 4' => ['title' => 'Buổi 4: Forms & File Handling', 'icon' => 'fas fa-file-upload'],
                'Buoi 5' => ['title' => 'Buổi 5: Advanced PHP', 'icon' => 'fas fa-shield-alt'],
                'Buoi 6' => ['title' => 'Buổi 6: Web Forms Advanced', 'icon' => 'fas fa-check-circle'],
                'Buoi 7' => ['title' => 'Buổi 7: Database Basics', 'icon' => 'fas fa-database'],
                'Buoi 8' => ['title' => 'Buổi 8: PDO & Database', 'icon' => 'fas fa-server']
            ];

            // Function để scan thư mục đệ quy
            function scanDirectoryRecursive($dir, $basePath = '') {
                $items = [];
                if (!is_dir($dir)) return $items;
                
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    
                    $fullPath = $dir . '/' . $file;
                    $relativePath = $basePath ? $basePath . '/' . $file : $file;
                    
                    if (is_dir($fullPath)) {
                        $items[] = [
                            'name' => $file,
                            'type' => 'folder',
                            'path' => $relativePath,
                            'children' => scanDirectoryRecursive($fullPath, $relativePath)
                        ];
                    } else {
                        $items[] = [
                            'name' => $file,
                            'type' => 'file',
                            'path' => $relativePath,
                            'extension' => pathinfo($file, PATHINFO_EXTENSION)
                        ];
                    }
                }
                return $items;
            }

            // Function để đếm tổng số file
            function countFiles($items) {
                $count = 0;
                foreach ($items as $item) {
                    if ($item['type'] === 'file') {
                        $count++;
                    } elseif ($item['type'] === 'folder') {
                        $count += countFiles($item['children']);
                    }
                }
                return $count;
            }

            // Function để hiển thị items
            function displayItems($items, $folderName) {
                foreach ($items as $item) {
                    if ($item['type'] === 'folder') {
                        echo '<div class="list-group-item bg-light">';
                        echo '<div class="d-flex justify-content-between align-items-center">';
                        echo '<div><i class="fas fa-folder text-warning me-2"></i>';
                        echo '<strong>' . htmlspecialchars($item['name']) . '/</strong>';
                        echo '<span class="badge bg-info ms-2">' . count($item['children']) . ' items</span>';
                        echo '</div>';
                        echo '<button class="btn btn-sm btn-outline-secondary" type="button" ';
                        echo 'data-bs-toggle="collapse" data-bs-target="#folder' . md5($folderName . $item['path']) . '">';
                        echo '<i class="fas fa-chevron-down"></i></button>';
                        echo '</div>';
                        
                        if (!empty($item['children'])) {
                            echo '<div class="collapse mt-2" id="folder' . md5($folderName . $item['path']) . '">';
                            echo '<div class="ms-3 border-start ps-3">';
                            displayItems($item['children'], $folderName);
                            echo '</div></div>';
                        }
                        echo '</div>';
                    } else {
                        $extension = $item['extension'];
                        $icon = 'fas fa-file';
                        $btnClass = 'btn-outline-secondary';
                        $btnIcon = 'fas fa-download';
                        
                        switch ($extension) {
                            case 'php': 
                                $icon = 'fab fa-php text-primary'; 
                                $btnClass = 'btn-outline-primary';
                                $btnIcon = 'fas fa-external-link-alt';
                                break;
                            case 'html': 
                                $icon = 'fab fa-html5 text-warning'; 
                                $btnClass = 'btn-outline-warning';
                                $btnIcon = 'fas fa-external-link-alt';
                                break;
                            case 'pdf': 
                                $icon = 'fas fa-file-pdf text-danger'; 
                                $btnClass = 'btn-outline-danger';
                                $btnIcon = 'fas fa-eye';
                                break;
                            case 'txt': 
                                $icon = 'fas fa-file-alt text-info'; 
                                break;
                            case 'sql': 
                                $icon = 'fas fa-database text-success'; 
                                $btnClass = 'btn-outline-success';
                                $btnIcon = 'fas fa-code';
                                break;
                            case 'png': case 'jpg': case 'jpeg': case 'webp': case 'gif': 
                                $icon = 'fas fa-image text-success'; 
                                $btnClass = 'btn-outline-success';
                                $btnIcon = 'fas fa-eye';
                                break;
                        }
                        
                        echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                        echo '<div>';
                        echo '<i class="' . $icon . ' me-2"></i>';
                        echo htmlspecialchars($item['name']);
                        echo '</div>';
                        echo '<div>';
                        
                        if (in_array($extension, ['php', 'html'])) {
                            echo '<a href="' . $folderName . '/' . $item['path'] . '" ';
                            echo 'class="btn btn-sm ' . $btnClass . '" target="_blank">';
                            echo '<i class="' . $btnIcon . '"></i></a>';
                        } elseif (in_array($extension, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'sql', 'txt'])) {
                            echo '<a href="' . $folderName . '/' . $item['path'] . '" ';
                            echo 'class="btn btn-sm ' . $btnClass . '" target="_blank">';
                            echo '<i class="' . $btnIcon . '"></i></a>';
                        }
                        
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }

            foreach ($labFolders as $folder => $info):
                $folderPath = __DIR__ . '/' . $folder;
                $items = scanDirectoryRecursive($folderPath);
                $totalFiles = countFiles($items);
            ?>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-link w-100 text-start p-0" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse<?php echo str_replace(' ', '', $folder); ?>">
                            <i class="<?php echo $info['icon']; ?> me-2 text-primary"></i>
                            <?php echo $info['title']; ?>
                            <span class="badge bg-secondary ms-2"><?php echo $totalFiles; ?> files</span>
                            <i class="fas fa-chevron-down float-end mt-1"></i>
                        </button>
                    </div>
                    
                    <div class="collapse" id="collapse<?php echo str_replace(' ', '', $folder); ?>">
                        <div class="card-body p-0">
                            <?php if (empty($items)): ?>
                                <div class="p-3 text-muted">
                                    <i class="fas fa-folder-open me-2"></i>Thư mục trống
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php displayItems($items, $folder); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Thông tin tác giả -->
        <div class="text-center mt-5 pt-4" style="border-top: 1px solid #dee2e6;">
            <p class="text-muted mb-1" style="font-size: 0.9rem;">
                Được Tạo Bởi <strong>Trần Quốc Bảo</strong> _ DH52200377<br>
                Năm Học 2025-2026
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>