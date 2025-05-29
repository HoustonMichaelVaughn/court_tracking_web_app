<?php
function h($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<h2><?= h($isEdit ? 'Edit Defendant' : 'Add Defendant') ?></h2>

<a href="<?= BASE_URL ?>/defendant/manage" class="btn btn-outline-danger">← Cancel</a>
<div class="my-3"></div>

<form method="POST" action="">
    <?php include PARTIALS . '/_defendant_form.php'; ?>
    <br>
    <button id="submit" type="submit" class="btn btn-primary">
        <?= h($isEdit ? 'Update Defendant' : 'Add Defendant') ?>
    </button>
</form>

<script>
document.getElementById('submit').addEventListener('click', function(e) {
  // Get form elements for validation
  const name = document.querySelector('[name="name"]').value.trim();
  const dob = document.querySelector('[name="dob"]').value.trim();

  // Check if any field is empty
  if (!name || !dob) {
    e.preventDefault();  // Prevent form submission
    alert("Please fill in name and date of birth before adding the defendant.");
  }
});
</script>
