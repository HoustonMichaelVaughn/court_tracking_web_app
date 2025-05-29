<h2>Enter Charge Information</h2>
<a href="<?= BASE_URL ?>/case/cancel" class="btn btn-outline-danger">Cancel Wizard</a>

<form method="POST" action="<?= BASE_URL ?>/case/charges">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <?php require PARTIALS . '/_charge_form.php'; ?>

  <!-- Display the charges already added -->
  <?php if (isset($charges) && count($charges) > 0): ?>
      <h3>Current Charges:</h3>
      <ul>
          <?php foreach ($charges as $charge): ?>
              <li><?php echo htmlspecialchars($charge['description']); ?> - <?php echo htmlspecialchars($charge['status']); ?></li>
          <?php endforeach; ?>
      </ul>
  <?php endif; ?>
  <!-- Add More Charges button -->
  <button type="submit" name="add_more" class="btn btn-secondary mt-3">Add Charge</button>
  <button type="submit" class="btn btn-primary mt-3">Next: Lawyer</button>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const nextBtn = form.querySelector('button.btn-primary');

    nextBtn.addEventListener('click', function (e) {
      const hasCharges = <?= isset($charges) && count($charges) > 0 ? 'true' : 'false' ?>;
      
      if (!hasCharges) {
        e.preventDefault();
        alert("Please add at least one charge before continuing.");
      }
    });
  });
</script>