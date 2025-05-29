<?php
function h($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= h($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= h($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-4">Register</h2>
            <form method="POST" action="<?= BASE_URL ?>/register/submit">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" required
                        value="<?= isset($_POST['username']) ? h($_POST['username']) : '' ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="confirm" class="form-label">Confirm Password:</label>
                    <input type="password" name="confirm" id="confirm" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label for="staff_type" class="form-label">Staff Type:</label>
                    <select name="staff_type" id="staff_type" class="form-select">
                        <option value="admin" <?= (isset($_POST['staff_type']) && $_POST['staff_type'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= (isset($_POST['staff_type']) && $_POST['staff_type'] === 'user') ? 'selected' : '' ?>>User</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
        </div>
    </div>
</div>
