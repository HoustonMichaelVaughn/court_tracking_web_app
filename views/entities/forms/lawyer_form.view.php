<h2><?= $isEdit ? 'Edit Lawyer' : 'Add Lawyer' ?></h2>
<a href="<?= BASE_URL ?>/lawyer/manage" class="btn btn-outline-danger">← Cancel</a>
<div class="my-3"></div>

<form method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <?php include PARTIALS . '/_lawyer_form.php'; ?>
    <br>
    <button id="submit" type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Lawyer' : 'Add Lawyer' ?></button>
</form>

<script>
document.getElementById('submit').addEventListener('click', function(e) {
  // Get form elements for validation
  const name = document.querySelector('[name="name"]').value.trim();

  // Check if any field is empty
  if (!name) {
    e.preventDefault();  // Prevent form submission
    alert("Please fill in name before adding the lawyer.");
  }
});
</script>
