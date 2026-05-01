<!-- Config -->
<div class="modal fade" id="config">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Configure</b></h4>
            </div>
            <div class="modal-body">
              <div class="text-center">
                <?php
                  $settings = get_election_settings($conn);
                  $title = $settings['title'];
                ?>
                <form class="form-horizontal" method="POST" action="config_save.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>">
                  <?php echo csrf_field(); ?>
                  <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">Title</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="title" name="title" value="<?php echo e($title); ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="status" class="col-sm-3 control-label">Status</label>

                    <div class="col-sm-9">
                      <select class="form-control" id="status" name="status">
                        <option value="draft" <?php echo ($settings['status'] == 'draft') ? 'selected' : ''; ?>>Not Started</option>
                        <option value="open" <?php echo ($settings['status'] == 'open') ? 'selected' : ''; ?>>Open</option>
                        <option value="closed" <?php echo ($settings['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="start_at" class="col-sm-3 control-label">Start</label>

                    <div class="col-sm-9">
                      <input type="datetime-local" class="form-control" id="start_at" name="start_at" value="<?php echo e(format_datetime_for_input($settings['start_at'])); ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="end_at" class="col-sm-3 control-label">End</label>

                    <div class="col-sm-9">
                      <input type="datetime-local" class="form-control" id="end_at" name="end_at" value="<?php echo e(format_datetime_for_input($settings['end_at'])); ?>">
                    </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="save"><i class="fa fa-save"></i> Save</button>
              </form>
            </div>
        </div>
    </div>
</div>
