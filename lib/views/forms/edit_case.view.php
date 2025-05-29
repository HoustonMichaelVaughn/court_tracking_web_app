<?php
function h($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<h2>Edit Case ID: <?= h($id) ?></h2>

<a href="<?= BASE_URL ?>/cases" class="btn btn-outline-secondary mb-3r">← Back to Case List</a>
<div class="my-3"></div>

<?php if (!empty($_GET['success'])): ?>
  <div class="alert alert-success"><?= h($_GET['success']) ?></div>
<?php endif; ?>

<h3>Charges</h3>
<ul>
  <?php foreach ($charges as $charge): ?>
    <li>
      <?= h($charge['Description']) ?> (Status: <?= h($charge['Status']) ?>)
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/charge/edit/<?= h($charge['charge_ID']) ?>?caseID=<?= h($id) ?>" class="btn btn-sm btn-secondary">Edit</a>
        <a href="<?= BASE_URL ?>/charge/delete/<?= h($charge['charge_ID']) ?>?caseID=<?= h($id) ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Are you sure you want to delete this charge?');">
           Delete
        </a>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <a href="<?= BASE_URL ?>/charge/add?caseID=<?= h($id) ?>" class="btn btn-primary">Add Charge</a>
<?php endif; ?>

<hr>

<h3>Events</h3>
<ul>
  <?php foreach ($events as $event): ?>
    <li>
      <?= h($event['Description']) ?> on <?= h($event['Date']) ?> at <?= h($event['Location']) ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/event/edit/<?= h($event['Event_ID']) ?>?caseID=<?= h($id) ?>" class="btn btn-sm btn-secondary">Edit</a>
        <a href="<?= BASE_URL ?>/event/delete/<?= h($event['Event_ID']) ?>?caseID=<?= h($id) ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Are you sure you want to delete this event?');">
           Delete
        </a>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <a href="<?= BASE_URL ?>/event/add?caseID=<?= h($id) ?>" class="btn btn-primary mt-3">Add Event</a>
<?php endif; ?>

<hr>
