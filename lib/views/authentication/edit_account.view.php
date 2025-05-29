<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<div class="container mt-4">
  <div class="card p-4 shadow" style="max-width: 500px; margin: auto;">
    <h4 class="mb-3">Edit User: <?= htmlspecialchars($user['username']) ?></h4>

    <form method="POST" action="<?= BASE_URL ?>/accounts/edit/<?= $user['id'] ?>/submit">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
        </select>
      </div>


      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="<?= BASE_URL ?>/accounts/manage" class="btn btn-secondary ms-2">Cancel</a>
    </form>
  </div>
</div>