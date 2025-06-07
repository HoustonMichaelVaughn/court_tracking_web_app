<h2>Select or Add Lawyer</h2>
<a href="<?= BASE_URL ?>/case/cancel" class="btn btn-outline-danger">Cancel Wizard</a>

<?php if (!empty($_GET['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>


<form id="lawyer-form" method="POST" action="<?= BASE_URL ?>/case/lawyer">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <div class="mb-3">
    <label class="form-label">Select Existing Lawyer</label>
    <select class="form-select" name="lawyer_ID" id="lawyer_ID">
      <option value="">-- Choose --</option>
      <?php foreach ($lawyers as $l): ?>
        <option value="<?= $l['lawyer_ID'] ?>"><?= htmlspecialchars($l['Name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="btn btn-primary mt-3" id="next-btn">Next: Court Event</button>

  <hr>
  <h5>Or Add New Lawyer</h5>
  <?php require PARTIALS . '/_lawyer_form.php'; ?>
  <button type="submit" class="btn btn-secondary mt-3" id="add-btn">Add New</button>

  <input type="hidden" name="action" id="action-input" value="">
</form>

<script>
  const form = document.getElementById('lawyer-form');
  const actionInput = document.getElementById('action-input');
  
  const nextBtn = document.getElementById('next-btn');
  const addBtn = document.getElementById('add-btn');
  
  nextBtn.addEventListener('click', function(e) {
    const lawyerID = document.getElementById('lawyer_ID').value.trim();
    if (!lawyerID) {
      e.preventDefault();
      alert("Please select a lawyer before continuing.");
    } else {
      actionInput.value = 'select_existing';
    }
  });

  addBtn.addEventListener('click', function(e) {
    const name = form.querySelector('[name="name"]')?.value.trim();
    
    if (!name) {
      e.preventDefault();
      alert("Please enter a name to add a new lawyer.");
    } else {
      actionInput.value = 'add_new';
    }
  });
</script>
