<!-- temp -->
<div id="cnt-temp"></div>

<div id="modal-update-password" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-update-passwordLabel" aria-hidden="true">
	<form class="form-horizontal" style="margin: 0px;">
		<input type="hidden" name="action" value="update_password" />
		
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="modal-update-passwordLabel">Update Password</h3>
		</div>
		<div class="modal-body">
			<div class="control-group">
				<label class="control-label">Old Password</label>
				<div class="controls"><input type="password" name="passwd_old" class="span4" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">New Password</label>
				<div class="controls"><input type="password" name="passwd_new" class="span4" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Confirm Password</label>
				<div class="controls"><input type="password" name="passwd_confirm" class="span4" /></div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="submit" class="btn btn-primary" value="Update" />
			<input type="button" class="btn" data-dismiss="modal" value="Close" />
		</div>
	</form>
</div>

<div id="modal-update-contact" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby=" modal-update-contactLabel" aria-hidden="true">
	<form class="form-horizontal" style="margin: 0px;">
		<input type="hidden" name="action" value="update_contact" />
		<input type="hidden" name="p_id" />
		
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="modal-update-contactLabel">Update Contact Details</h3>
		</div>
		<div class="modal-body">
			<div class="control-group">
				<label class="control-label">Phone</label>
				<div class="controls"><input type="text" name="p_phone" class="span4" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Address</label>
				<div class="controls"><input type="text" name="p_address" class="span4" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Father Email</label>
				<div class="controls"><input type="text" name="p_father_email" class="span4" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Mother Email</label>
				<div class="controls"><input type="text" name="p_mother_email" class="span4" /></div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="submit" class="btn btn-primary" value="Update" />
			<input type="button" class="btn" data-dismiss="modal" value="Close" />
		</div>
	</form>
</div>
<!-- / temp -->

<!-- Page footer -->
<footer id="main-footer">
	Copyright &copy; 2014 <a target="_blank" href="http://www.jafariaschool.org">Jafaria Education Center</a>, all rights reserved.
	<a href="#" class="pull-right" id="on-top-link">
		On Top &nbsp;<i class=" icon-chevron-up"></i>
	</a>
</footer>
<!-- / Page footer -->