<!-- Reset -->
<div class="modal fade" id="reset">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Reseting...</b></h4>
            </div>
            <div class="modal-body">
              <div class="text-center">
                  <p>RESET VOTES</p>
                  <h4>This will delete all votes and counting back to 0.</h4>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <form method="POST" action="votes_reset.php" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger btn-flat" name="reset"><i class="fa fa-refresh"></i> Reset</button>
              </form>
            </div>
        </div>
    </div>
</div>
