<?php
function h($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error"><?= h($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success"><?= h($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<form method="POST" action="/register">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required
           value="<?= isset($_POST['username']) ? h($_POST['username']) : '' ?>">

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <label for="confirm">Confirm Password:</label>
    <input type="password" name="confirm" id="confirm" required>

    <label for="staff_type">Staff Type:</label>
    <select name="staff_type" id="staff_type">
        <option value="admin" <?= (isset($_POST['staff_type']) && $_POST['staff_type'] === 'admin') ? 'selected' : '' ?>>Admin</option>
        <option value="user" <?= (isset($_POST['staff_type']) && $_POST['staff_type'] === 'user') ? 'selected' : '' ?>>User</option>
    </select>

    <button type="submit">Register</button>
</form>
