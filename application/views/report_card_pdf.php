<?php
	$parent = $this->parents_model->get_by_id(array( 'p_id' => $_POST['parent_id'] ));
	$array_student = $this->student_model->get_array(array( 's_parent_id' => $action['parent_id'] ));
?>
<style>
.clear {
	clear: both;
}
.header {
	position: absolute;
	overflow: visible;
	top: 0;
	left: 0;
	width: 100%;
	padding: 2.5em 0 0 0;
	margin: 0;
	text-align: center;
	font-size: 16px;
	color: #ff6150;
	letter-spacing: 2px;
}
.footer {
	position: absolute;
	overflow: visible;
	left: 0;
	bottom: 0;
	width: 100%;
	padding: 2em 0;
	margin: 0;
	text-align: center;
	color: #FFF;
}
.cnt-listing {
	font-size: 18px;
	line-height: 30px;
	padding: 15px 0 0 0;
}
.cnt-listing .left {
	float: left;
	width: 5%;
	text-align: center;
}
.cnt-listing .right {
	float: left;
	width: 95%;
	padding: 0 0 40px 0;
}
.cnt-listing .left-less {
	float: left;
	width: 5%;
	text-align: right;
}
.cnt-listing .right-less {
	float: left;
	width: 90%;
	padding: 0 0 10px 10px;
}
.paragraph {
	font-size: 15px;
}
.paragraph .line {
	padding: 0 0 10px 0;
}
.text-center {
	text-align: center;
}
.title-top {
	font-size: 11px;
	font-style: italic;
	padding-bottom: 2px;
	border-bottom: 4px solid rgb(203, 203, 203);
}
.title-top-student {
	font-size: 15px;
	font-weight: bold;
	border-bottom: 5px solid rgb(203, 203, 203);
	font-family: Arial;
	padding-bottom: 6px;
}
.title {
	font-size: 80px;
	color: rgb(212, 132, 0);
}
.title-school {
	border-top: 5px solid rgb(203, 203, 203);
	border-bottom: 5px solid rgb(203, 203, 203);
	font-family: Lucida Console;
	font-weight: bold;
	font-size: 20px;
	padding: 5px 0 2px;
	color: rgb(203, 203, 203);
	margin-bottom: 5px;
}
.name {
	font-weight: bold;
	font-size: 20px;
	color: rgb(203, 203, 203);
}
.name-student {
	font-size: 55px;
	padding-bottom: 5px;
	border-bottom: 1px solid #FFF;
	color: rgb(212, 132, 0);
}
.name-session {
	font-size: 52px;
	margin: 15px auto 10px;
}
.periode {
	font-size: 40px;
}
.text {
	font-size: 18px;
}
.text-middle {
	font-size: 18px;
	text-align: center;
	line-height: 26px;
	width: 88%;
	margin: 0 auto;
}
table {
	width: 95%;
}
table, th, td {
    border: 1px solid rgb(203, 203, 203);
	border-collapse: collapse;
	color: rgb(214, 214, 214);
}
th {
    text-align: center;
	width: 50%; 
	height: 50px;
	font-family: Arial;
	background-color: rgb(49, 49, 49);
}
td {
	margin: 10px 5px;
	padding: 10px 5px;
	background-color: rgb(60, 60, 60);
}
a {
	color: #FFF;
}
.table-report th {
	width: 20%; 
	height: 60px;
	border-top: 4px solid gray;
	border-right: 2px solid gray;
	border-bottom: 4px solid gray;
	border-left: 2px solid gray;
}
.table-report td {
	text-align: center;
}
.table-report .class {
	font-style: italic;
	font-weight: bold;
	font-size: x-large;
}
.table-report .even td {
	background-color: rgb(81, 81, 81);
}
.attendance td {
	height: 80px;
}
.text-red {
	color: red;
}

@page {
	margin: 20px 40px;
	background: url('<?php echo base_url('static/images/left-menu-bg.png'); ?>');
}
</style>

<body style="color: white"> 
	<div class="text-center">
		<div class="title-top">In The Name of Allah (SWT), The Most Gracious, The Most Merciful</div>
	</div>
	<div class="title">MID-TERM</div>
	<div class="title">REPORT CARD</div>
	<div class="title-school">JAFARIA EDUCATION CENTER</div>
	<div class="name"><?php echo $parent['p_father_name'].' & '.$parent['p_mother_name']; ?></div>
	
	<p class="text">Key: Achievement is graded from A to F. For additional comments, please refer to the following:</p>
	
	<table align="center">
		<thead>
			<tr>
				<th>GOOD</th>
				<th>BAD</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					1. Great Work<br />
					2. Outstanding Student<br />
					3. Good Work Habits<br />
					4. Very Neat & Accurate work<br />
					5. Highly motivated<br />
					6. Contributes intelligently to class<br />
					7. Works well in group activities<br />
				</td>
				<td>
					8. Appears disorganized<br />
					9. Quality of Work Declining<br />
					10. Does not bring Materials<br />
					11. Does not follow Directions<br />
					12. Inconsistent effort<br />
					13. Unacceptable behavior<br />
					14. Difficulty in understanding subject matter<br />
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<br />
	<br />
	
	<div class="text-center">
		<p class="text-middle">
			If you have any questions about this report, please contact your child's teacher. To get the contact information, 
			please log on to your dashboard by visiting <a href="http://www.jafariaschool.org/student" target="_blank">http://www.jafariaschool.org/student</a> or email <a href="school@jafaria.org" target="_blank">school@jafaria.org</a>
		</p>
	</div>
	
	<div style="padding: 50px 0 30px 0; text-align: center;">
		<img src="<?php echo base_url('static/images/logo.gif'); ?>" style="width: 30%;" />
	</div>
	
	<div class="footer" style="padding: 0px 5px 15px 5px; font-size: 12px;">
		<div class="text-center" style="color: rgb(212, 132, 0);">
			1546 E. La Palma Ave Anaheim CA 92805 | <a href="http://www.jafariaschool.org/" style="color: rgb(212, 132, 0);">www.jafariaschool.org</a> | <a href="school@jafaria.org" style="color: rgb(212, 132, 0);">school@jafaria.org</a>
		</div>
		<div class="clear"></div>
	</div>
	
<?php foreach($array_student as $row) { ?>
	<pagebreak />
	<div class="title-top-student">JAFARIA EDUCATION CENTER</div>
	<div class="name-student"><?php echo $row['s_name'] ?></div>
	<div class="text-center">
		<div class="name-session">MID-TERM REPORT CARD</div>
		<div class="periode">September 2014 - January 2015</div>
		<br />
		<br />
		<?php $student_detail = 0; ?>
		<?php echo 'continue here'; ?>
		<?php exit; ?>
		
		<table class="table-report" align="center" width="100%">
			<thead>
				<tr>
					<th>COURSE</th>
					<th>TEACHER</th>
					<th>PERCENTAGE</th>
					<th>LETTER GRADE</th>
					<th>ADDITIONAL COMMENTS</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="class">Quran <br/>Advanced</td>
					<td>Brother Taha Dharsi</td>
					<td>92%</td>
					<td>A</td>
					<td>1,2</td>
				</tr>
				<tr class="even">
					<td class="class">Akhlaq <br/>Class 6</td>
					<td>Brother Taha Dharsi</td>
					<td>92%</td>
					<td>A</td>
					<td>1,2</td>
				</tr>
				<tr>
					<td class="class">Fiqh <br/>Class 6</td>
					<td>Brother Taha Dharsi</td>
					<td>92%</td>
					<td>A</td>
					<td>1,2</td>
				</tr>
				<tr class="even">
					<td class="class">Taareekh <br/>Class 6</td>
					<td>Brother Taha Dharsi</td>
					<td>92%</td>
					<td>A</td>
					<td>1,2</td>
				</tr>
				<tr>
					<td class="class">Aqaid <br/>Class 6</td>
					<td>Brother Taha Dharsi</td>
					<td>92%</td>
					<td>A</td>
					<td>1,2</td>
				</tr>
				<tr class="attendance even">
					<td class="class" colspan="2">Attendance</td>
					<td>85%</td>
					<td colspan="2" class="text-red"><b>Total Absences : 2</b></td>
				</tr>
			</tbody>
		</table>
		
		<br />
		<br />
		<div style="padding: 50px 0 30px 0; text-align: center;">
			<img src="<?php echo base_url('static/images/logo.gif'); ?>" style="width: 30%;" />
		</div>
	</div>
<?php } ?>
</body>
<?php
exit;
?>