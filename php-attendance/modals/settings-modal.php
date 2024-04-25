<?php
$invoice_settings_qry = $conn->query("SELECT * FROM `settings_tbl`")->fetch_all(MYSQLI_ASSOC);
$invoice_settings = array_column($invoice_settings_qry, 'meta_value', 'meta_field');
?>
<div class="modal fade" id="settingsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
    <div class="modal-content">
      <div class="modal-header rounded-0">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Invoice Data Settings</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body rounded-0">
        <div class="container-fluid">
            <form action="./?action=update_settings" id="settings-form" method="POST">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="store_name" value="<?= $invoice_settings['store_name'] ?? "" ?>" required="required">
                        </div>
                        <div class="mb-3">
                            <label for="store_address" class="form-label">Store Address</label>
                            <input type="text" class="form-control" id="store_address" name="store_address" value="<?= $invoice_settings['store_address'] ?? "" ?>" required="required">
                        </div>
                        <div class="mb-3">
                            <label for="store_contact" class="form-label">Store Contact</label>
                            <input type="text" class="form-control" id="store_contact" name="store_contact" value="<?= $invoice_settings['store_contact'] ?? "" ?>" required="required">
                        </div>
                        <div class="mb-3">
                            <label for="store_contact" class="form-label">Footer Note</label>
                            <textarea rows="3" class="form-control" id="footer_note" name="footer_note" required="required"><?= $invoice_settings['footer_note'] ?? "" ?></textarea>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
      </div>
      <div class="modal-footer rounded-0">
        <button type="submit" form="settings-form" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>