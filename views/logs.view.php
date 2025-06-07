<?php
// This view displays a list of logs with pagination and user details.
?>

<div class="container my-5">

  <!-- Page Title -->
  <h1 class="mb-4">All Logs</h1>

  <!-- Back Button -->
  <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary mb-4">← Back</a>

  <?php if (!empty($logs)): ?>
    <!-- Log List -->
    <ol start="<?= ($currentPage - 1) * 5 + 1 ?>" class="list-group list-group-numbered">

      <?php foreach ($logs as $log): ?>
        <li class="list-group-item mb-4 p-4 shadow-sm rounded">

          <!-- Top Row: Username on left, Date/Time on right -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <strong class="fs-5"><?= htmlspecialchars($log['username'] ?: 'Unknown User') ?></strong>
            <span class="text-muted ms-3"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($log['created_at']))) ?></span>
          </div>

          <!-- Action Message -->
          <div class="mt-3 ps-1">
            <p class="mb-0"><?= nl2br(htmlspecialchars($log['action'])) ?></p>
          </div>

        </li>
      <?php endforeach; ?>

    </ol>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-5">
      <ul class="pagination justify-content-center">

        <!-- Previous Button -->
        <?php if ($currentPage > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $currentPage - 1 ?>">&laquo; Prev</a>
          </li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <!-- Next Button -->
        <?php if ($currentPage < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next &raquo;</a>
          </li>
        <?php endif; ?>

      </ul>
    </nav>

  <?php else: ?>

    <!-- No Logs Message -->
    <p>No logs available.</p>

  <?php endif; ?>

</div>
