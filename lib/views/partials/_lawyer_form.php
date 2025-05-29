<?php $lawyer = $lawyer ?? []; ?>

<div class="row g-3">
  <div class="col-md-6">
    <input 
      type="text" 
      class="form-control" 
      name="name" 
      placeholder="Full Name" 
      value="<?= htmlspecialchars($lawyer['Name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  </div>
  <div class="col-md-6">
    <input 
      type="email" 
      class="form-control" 
      name="email" 
      placeholder="Email" 
      value="<?= htmlspecialchars($lawyer['Email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  </div>
  <div class="col-md-6">
    <input 
      type="text" 
      class="form-control" 
      name="phone" 
      placeholder="Phone" 
      value="<?= htmlspecialchars($lawyer['Phone_Number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  </div>
  <div class="col-md-6">
    <input 
      type="text" 
      class="form-control" 
      name="firm" 
      placeholder="Law Firm" 
      value="<?= htmlspecialchars($lawyer['Firm'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  </div>
</div>
