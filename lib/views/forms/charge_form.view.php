
<h2><?= $isEdit ? 'Edit Charge' : 'Add Charge' ?></h2>
<div class="mb-4">
  <a href="<?= BASE_URL ?>/case/edit/<?= $id ?>" class="btn btn-outline-danger">← Cancel</a>
</div>

<form method="POST" action="" class="mt-4">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <?php include PARTIALS . '/_charge_form.php'; ?>
    <button id="submit" type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Charge' : 'Add Charge' ?></button>
</form>

<script>
document.getElementById('submit').addEventListener('click', function(e) {
  // Get form elements for validation
  const description = document.querySelector('[name="description"]').value.trim();
  const status = document.querySelector('[name="status"]').value.trim();

  // Check if any field is empty
  if (!description || !status) {
    e.preventDefault();  // Prevent form submission
    alert("Please fill in all fields before adding the event.");
  }
});
</script>
