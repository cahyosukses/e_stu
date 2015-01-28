<?php
	$fee = $this->fee_model->get_by_id(array( 'id' => $_POST['id'] ));
	$array_student = $this->student_model->get_array(array( 's_parent_id' => $fee['parent_id'] ));
?>

<style>
.right { text-align: right; }
div, table { font-size: 14px; font-family: verdana; }
#invoice td { padding: 10px 0; }
#list-item td { padding: 10px; }
</style>

<table border="0" cellspacing="0">
	<tr>
		<td style="width: 400px; vertical-align: top;">
			Jafaria Education Center<br />
			1546 E. La Palma Ave<br />
			Anaheim, CA 92805<br /><br /><br /><br /><br /><br /><br /><br />
			<?php if (empty($fee['p_father_name'])) { ?>
			<?php $parent_fullname = $fee['p_mother_name']; ?>
			<?php } else if (empty($fee['p_mother_name'])) { ?>
			<?php $parent_fullname = $fee['p_father_name']; ?>
			<?php } else { ?>
			<?php $parent_fullname = $fee['p_father_name'].' & '.$fee['p_mother_name'];; ?>
			<?php } ?>
			<?php echo $parent_fullname; ?><br />
			<?php echo $fee['p_address']; ?>
		</td>
		<td style="width: 400px; vertical-align: top; text-align: right;">
			<div>
				<img src="<?php echo base_url('static/images/Logo.jpg'); ?>" style="width: 175px;" />
			</div><br /><br />
			
			<div style="font-size: 22px;"><strong>Sunday School Tuition</strong></div><br /><br />
			
			<table border="0" cellspacing="0" id="invoice">
				<tr>
					<td style="width: 150px; text-align: right;"><strong>Invoice #</strong></td>
					<td style="width: 150px; text-align: right;"><?php echo $fee['invoice_no']; ?></td>
				</tr>
				<tr>
					<td style="text-align: right;"><strong>Invoice Date</strong></td>
					<td style="text-align: right;"><?php echo get_format_date($fee['invoice_date'], array( 'date_format' => 'm/d/Y' )); ?></td>
				</tr>
				<tr>
					<td style="text-align: right;"><strong>Due Date</strong></td>
					<td style="text-align: right;"><?php echo get_format_date($fee['invoice_date'], array( 'date_format' => 'm/d/Y' )); ?></td>
				</tr>
				<tr>
					<td style="text-align: right;"><strong>Payment Method</strong></td>
					<td style="text-align: right;"><?php echo $fee['payment_method']; ?></td>
				</tr>
				<?php if ($fee['payment_method'] == 'Check') { ?>
				<tr>
					<td style="text-align: right;"><strong>Last 4 digits</strong></td>
					<td style="text-align: right;"><?php echo $fee['payment_no']; ?></td>
				</tr>
				<?php } ?>
			</table>
		</td>
	</tr>
</table>

<table border="0" cellspacing="0" id="list-item">
	<tr style="background: #eaedee;">
		<td style="width: 125px; border: 1px solid #000000; border-right: none;"><strong>Item</strong></td>
		<td style="width: 350px; border-top: 1px solid #000000; border-bottom: 1px solid #000000;"><strong>Description</strong></td>
		<td style="width: 100px; text-align: right; border-top: 1px solid #000000; border-bottom: 1px solid #000000;"><strong>Unit Price</strong></td>
		<td style="width: 100px; text-align: right; border-top: 1px solid #000000; border-bottom: 1px solid #000000;"><strong>Quantity</strong></td>
		<td style="width: 100px; text-align: right; border: 1px solid #000000; border-left: none;"><strong>Amount</strong></td>
	<tr>
	<?php foreach ($array_student as $key => $row) { ?>
	<?php $value = (empty($key)) ? $fee['first_student'] : $fee['additional_student']; ?>
	<tr>
		<td style="border-left: 1px solid #000000;">Enrollment Fee</td>
		<td><?php echo $row['s_name']; ?></td>
		<td class="right"><?php echo invoice_number_format($value); ?></td>
		<td class="right">1.00</td>
		<td class="right" style="border-right: 1px solid #000000;"><?php echo invoice_number_format($value); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="5" style="border-left: 1px solid #000000; border-right: 1px solid #000000;">
			<br /><br /><br /><br />
			NOTES: Thank you for Registering your Child/Children for Jafaria Sunday School.<br /><br />
			JazakAllah<br />
			Jafaria Education center<br /><br />
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2" rowspan="3" style="border: 1px solid #000000;">&nbsp;</td>
		<td colspan="2" style="padding-left: 30px; border: 1px solid #000000; border-right: none; border-left: none;">
			<strong>Subtotal</strong><br />
			&nbsp;- Discount
		</td>
		<td class="right" style="border: 1px solid #000000; border-left: none;">
			<?php echo invoice_number_format($fee['subtotal']); ?><br />
			<?php echo invoice_number_format($fee['discount']); ?>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-left: 30px; border-bottom: 1px solid #000000;">
			<strong>Total</strong><br />
			<strong>Amount Paid</strong>
		</td>
		<td class="right" style="border: 1px solid #000000; border-top: none; border-left: none;">
			<?php echo invoice_number_format($fee['total']); ?><br />
			<?php echo invoice_number_format($fee['total']); ?>
		</td>
	</tr>
	<tr style="background: #eaedee;">
		<td colspan="2" style="padding-left: 30px; border-bottom: 1px solid #000000;">
			<strong>Balance Due</strong>
		</td>
		<td class="right" style="border: 1px solid #000000; border-top: none; border-left: none;">
			0.00
		</td>
	</tr>
</table>