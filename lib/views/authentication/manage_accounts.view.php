<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<div class="container mt-4">
  <h3 class="mb-4">Manage Staff Accounts</h3>

  <?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
  <?php endif; ?>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Staff Type</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($accounts as $account): ?>
        <tr>
          <td><?= htmlspecialchars($account['username']) ?></td>
          <td><?= htmlspecialchars($account['role']) ?></td>
          <td><?= htmlspecialchars($account['staff_type']) ?></td>
          <td>
            <!-- In the next steps we'll add working links for edit and delete -->
            <a href="#" class="btn btn-sm btn-warning disabled">Edit</a>
            <a href="#" class="btn btn-sm btn-danger disabled">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>