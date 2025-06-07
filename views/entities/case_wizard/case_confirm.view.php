<?php
// This view displays a success message after a case is added.
?>

<h2>Success</h2>

<?php if (!empty($success)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php else: ?>
  <p>Case has been successfully added.</p>
<?php endif; ?>

<a href="<?= BASE_URL ?>/cases" class="btn btn-outline-secondary">Return to Cases</a>
<a href="<?= BASE_URL ?>/" class="btn btn-primary">Return to Home</a>
