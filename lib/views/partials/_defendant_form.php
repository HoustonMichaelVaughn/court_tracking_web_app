<div class="row g-3">
    <div class="col-md-6">
        <input 
            type="text" 
            class="form-control" 
            name="name" 
            placeholder="Name"
            value="<?= htmlspecialchars($defendant['Name'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>
    <div class="col-md-6">
        <input 
            type="date" 
            class="form-control" 
            name="dob" 
            placeholder="Date of Birth"
            value="<?= htmlspecialchars($defendant['Date_of_Birth'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>
    <div class="col-md-12">
        <input 
            type="text" 
            class="form-control" 
            name="address" 
            placeholder="Address"
            value="<?= htmlspecialchars($defendant['Address'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>
    <div class="col-md-6">
        <input 
            type="text" 
            class="form-control" 
            name="ethnicity" 
            placeholder="Ethnicity"
            value="<?= htmlspecialchars($defendant['Ethnicity'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>
    <div class="col-md-6">
        <input 
            type="text" 
            class="form-control" 
            name="phone" 
            placeholder="Phone"
            value="<?= htmlspecialchars($defendant['Phone_Number'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>
    <div class="col-md-12">
        <input 
            type="email" 
            class="form-control" 
            name="email" 
            placeholder="Email"
            value="<?= htmlspecialchars($defendant['Email'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>
</div>
