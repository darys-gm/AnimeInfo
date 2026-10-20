<?php
require_once 'config/auth.php';
require_once 'config/anime_list.php';
include 'includes/header.php';

$auth = new Auth();
$animeList = new AnimeList();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: auth.php');
    exit();
}

$user = $auth->getCurrentUser();
$message = '';
$messageType = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    $result = $auth->updateProfile($user['id'], $username, $email);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
    
    if ($result['success']) {
        $user = $auth->getCurrentUser();
    }
}

// Get statistics
$totalWatching = $animeList->getListCount($user['id'], 'watching');
$totalCompleted = $animeList->getListCount($user['id'], 'completed');
$totalPlanned = $animeList->getListCount($user['id'], 'planned');
$totalAnime = $totalWatching + $totalCompleted + $totalPlanned;
?>

<div class="profile-page">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    </div>
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <h5><?php echo $totalAnime; ?></h5>
                            <small class="text-muted">Total Anime</small>
                        </div>
                        <div class="col-6">
                            <h5><?php echo $totalWatching; ?></h5>
                            <small class="text-muted">Sedang Ditonton</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Edit Profile Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Edit Profil
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profil
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-eye fa-2x text-primary"></i>
                            <h4 class="mt-2"><?php echo $totalWatching; ?></h4>
                            <small>Sedang Tayang</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                            <h4 class="mt-2"><?php echo $totalCompleted; ?></h4>
                            <small>Selesai</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                            <h4 class="mt-2"><?php echo $totalPlanned; ?></h4>
                            <small>Rencana</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-heart fa-2x text-danger"></i>
                            <h4 class="mt-2"><?php echo $totalAnime; ?></h4>
                            <small>Total List</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Logout Button -->
            <div class="card">
                <div class="card-body text-center">
                    <a href="logout.php" class="btn btn-danger" onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.profile-page .card {
    border-radius: 16px;
}

@media (max-width: 768px) {
    .profile-page .col-md-3 {
        margin-bottom: 12px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>