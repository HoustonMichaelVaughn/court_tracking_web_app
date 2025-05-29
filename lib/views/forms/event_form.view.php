<h2><?= $isEdit ? 'Edit Event' : 'Add Event' ?></h2>
<a href="<?= BASE_URL ?>/case/edit/<?= $id ?>" class="btn btn-outline-danger">← Cancel</a>

<form method="POST" action="">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <?php include PARTIALS . '/_event_form.php'; ?>
    <button id='submit' type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Event' : 'Add Event' ?></button>
</form>

<script>
document.getElementById('submit').addEventListener('click', function(e) {
  // Get form elements for validation
  const description = document.querySelector('[name="description"]').value.trim();
  const date = document.querySelector('[name="date"]').value.trim();
  const location = document.querySelector('[name="location"]').value.trim();

  // Check if any field is empty
  if (!description || !date || !location) {
    e.preventDefault();  // Prevent form submission
    alert("Please fill in all fields before adding the event.");
  }
});
</script>
