<div class="mb-3">
  <label class="form-label">Event Description</label>
  <input 
    type="text" 
    class="form-control" 
    name="description" 
    value="<?= htmlspecialchars($event["Description"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label class="form-label">Event Date</label>
  <input 
    type="date" 
    class="form-control" 
    name="date" 
    value="<?= htmlspecialchars($event["Date"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label class="form-label">Event Location</label>
  <input 
    type="text" 
    class="form-control" 
    name="location" 
    value="<?= htmlspecialchars($event["Location"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
</div>