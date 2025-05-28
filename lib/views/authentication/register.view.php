<div class="container d-flex justify-content-center mt-5">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-3">Register Court Officer</h3>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/register/submit">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Staff Type</label>
        <select name="staff_type" class="form-select" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-100">Register Officer</button>
    </form>
  </div>
</div>