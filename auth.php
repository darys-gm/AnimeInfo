<?php
// Hapus output buffer jika ada
ob_start();

require_once 'config/auth.php';

$auth = new Auth();
$message = '';
$messageType = '';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Handle Register
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $message = 'Password tidak cocok!';
        $messageType = 'danger';
    } elseif (strlen($password) < 6) {
        $message = 'Password minimal 6 karakter!';
        $messageType = 'danger';
    } else {
        $result = $auth->register($username, $email, $password);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $result = $auth->login($username, $password);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
    
    if ($result['success']) {
        header('Location: index.php');
        exit();
    }
}

$action = isset($_GET['action']) ? $_GET['action'] : 'login';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Login / Register - AnimeInfo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        
        .auth-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .auth-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .auth-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e9ecef;
            font-size: 0.9rem;
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        
        .btn-auth {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52,152,219,0.3);
        }
        
        /* Tombol Kembali ke Home */
        .btn-back-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #3498db;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }
        
        .btn-back-home i {
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }
        
        .btn-back-home:hover {
            background: #3498db;
            border-color: #3498db;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-back-home:hover i {
            transform: translateX(-3px);
        }
        
        .switch-link {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
            font-size: 0.85rem;
        }
        
        .switch-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        
        .switch-link a:hover {
            text-decoration: underline;
        }
        
        .input-group-text {
            background: white;
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        /* Alert styling */
        .alert {
            border-radius: 12px;
            font-size: 0.85rem;
            padding: 12px 15px;
        }
        
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e9ecef;
        }
        
        .divider span {
            padding: 0 10px;
            color: #7f8c8d;
            font-size: 0.75rem;
        }
        
        /* ========================================
           RESPONSIVE STYLES
           ======================================== */
        
        /* Tablet */
        @media (max-width: 768px) {
            body {
                padding: 16px;
            }
            
            .auth-header {
                padding: 25px 20px;
            }
            
            .auth-header h2 {
                font-size: 1.5rem;
            }
            
            .auth-header p {
                font-size: 0.8rem;
            }
            
            .auth-body {
                padding: 25px 20px;
            }
            
            .form-control {
                padding: 10px 12px;
                font-size: 0.85rem;
            }
            
            .btn-auth {
                padding: 10px;
                font-size: 0.9rem;
            }
            
            .btn-back-home {
                padding: 8px 16px;
                font-size: 0.85rem;
                margin-top: 12px;
            }
            
            .switch-link {
                font-size: 0.8rem;
                margin-top: 15px;
            }
        }
        
        /* Mobile */
        @media (max-width: 480px) {
            body {
                padding: 12px;
            }
            
            .auth-container {
                max-width: 100%;
            }
            
            .auth-header {
                padding: 20px 15px;
            }
            
            .auth-header h2 {
                font-size: 1.3rem;
            }
            
            .auth-header p {
                font-size: 0.75rem;
            }
            
            .auth-body {
                padding: 20px 15px;
            }
            
            .form-control {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .btn-auth {
                padding: 10px;
                font-size: 0.85rem;
            }
            
            .btn-back-home {
                padding: 8px 12px;
                font-size: 0.8rem;
                gap: 6px;
            }
            
            .btn-back-home i {
                font-size: 0.8rem;
            }
            
            .switch-link {
                font-size: 0.75rem;
            }
            
            .input-group-text {
                padding: 0 10px;
            }
        }
        
        /* Small Mobile */
        @media (max-width: 380px) {
            .auth-header {
                padding: 15px 12px;
            }
            
            .auth-header h2 {
                font-size: 1.1rem;
            }
            
            .auth-body {
                padding: 15px 12px;
            }
            
            .form-control {
                padding: 6px 8px;
                font-size: 0.75rem;
            }
            
            .btn-auth, .btn-back-home {
                padding: 8px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-tv fa-2x mb-2"></i>
                <h2><?php echo $action === 'login' ? 'Selamat Datang Kembali!' : 'Daftar Akun Baru'; ?></h2>
                <p><?php echo $action === 'login' ? 'Login untuk mengelola list anime Anda' : 'Buat akun untuk mulai membuat list anime'; ?></p>
            </div>
            
            <div class="auth-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($action === 'login'): ?>
                    <!-- Login Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username atau Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn-auth">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span>atau</span>
                    </div>
                    
                    <!-- Tombol Kembali ke Home -->
                    <a href="index.php" class="btn-back-home">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                    
                    <div class="switch-link">
                        Belum punya akun? <a href="?action=register">Daftar Sekarang</a>
                    </div>
                    
                <?php else: ?>
                    <!-- Register Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" required minlength="3" autofocus>
                            </div>
                            <small class="text-muted">Minimal 3 karakter</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check"></i></span>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" name="register" class="btn-auth">
                            <i class="fas fa-user-plus me-2"></i> Daftar
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span>atau</span>
                    </div>
                    
                    <!-- Tombol Kembali ke Home -->
                    <a href="index.php" class="btn-back-home">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                    
                    <div class="switch-link">
                        Sudah punya akun? <a href="?action=login">Login Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto hide alert after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 3000);
            });
        });
    </script>
</body>
</html>

<?php
ob_end_flush();
?>