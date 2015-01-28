<?php echo $this->load->view( 'common/meta' ); ?>
<body class="centered-layout">
	<?php echo $this->load->view( 'common/header' ); ?>
	<?php echo $this->load->view( 'common/panel_left' ); ?>
	
	<section class="container">
		<section class="row-fluid">
			<h3 class="box-header">User</h3>
			
			<div class="box-grid">
				<div class="box">
					<h4 class="center-title">User List</h4>
					<table class="table table-striped" id="user-grid">
						<thead>
							<tr>
								<th style="width: 50%;">Name</th>
								<th style="width: 25%;">Phone</th>
								<th style="width: 25%;">Email</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			
		</section>
		
		<?php echo $this->load->view( 'common/footer' ); ?>
	</section>
</body>

<script type="text/javascript">
$(document).ready(function() {
	// grid
	var param = {
		id: 'user-grid',
		source: web.base + 'teacher/grid', aaSorting: [[ 0, "ASC" ]],
		column: [ { }, { }, { } ]
	}
	var dt = Func.datatable(param);
});
</script>

</html>