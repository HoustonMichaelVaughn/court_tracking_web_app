<div class="mb-3">
  <label class="form-label">Charge Description</label>
  <input 
    type="text" 
    class="form-control" 
    name="description" 
    value="<?= htmlspecialchars($charge["Description"], ENT_QUOTES, 'UTF-8') ?>"
    >
</div>

<div class="mb-3">
  <label class="form-label">Status</label>
  <select class="form-select" name="status">
    <?php
      $statuses = ['Pending', 'Resolved', 'Dismissed'];
      $currentStatus = $charge['Status'] ?? '';
      foreach ($statuses as $status):
    ?>
      <option value="<?= $status ?>" <?= ($status === $currentStatus) ? 'selected' : '' ?>>
        <?= $status ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
