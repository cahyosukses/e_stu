<?php
	// get user
	$user = $this->user_model->get_session();
	
	// string student
	$string_student = (isset($string_student)) ? $string_student : '';
	if (empty($string_student)) {
		foreach ($user['array_student'] as $key => $row) {
			$string_student .= (empty($string_student)) ? $row['s_name'] : ', '.$row['s_name'];
		}
	}
	
	// student
	$student = $this->student_model->get_by_id(array( 's_id' => $user['student_id'] ));
	
	// signature
	$text_signature = (isset($text_signature)) ? $text_signature : '';
	$text_signature = (!empty($_POST['text_signature'])) ? $_POST['text_signature'] : $text_signature;
	$text_signature = (empty($text_signature)) ? '-' : $text_signature;
	
	// default data
	$_POST['full_name'] = (empty($_POST['full_name'])) ? $user['user_display'] : $_POST['full_name'];
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
</style>

<body style="background: url('<?php echo base_url('static/images/yellow-line.png'); ?>'); font-family: verdana, arial; font-size: 14px;">
	<div style="text-align: center;">
		<div><img src="<?php echo base_url('static/images/handbook-line.png'); ?>" style="width: 80%;" /></div>
		<div style="padding: 30px 0; font-size: 80px;">Parent & Student Handbook</div>
		<div style="padding: 15px 0;"><img src="<?php echo base_url('static/images/handbook-line.png'); ?>" style="width: 70%;" /></div>
		<div style="padding: 20px 0 50px 0; font-size: 26px; color: #ff6150; letter-spacing: 5px;">JAFARIA EDUCATION CENTER</div>
		<div style="padding: 50px 0 30px 0;"><img src="<?php echo base_url('static/images/handbook-logo.png'); ?>" style="width: 75%;" /></div>
	</div>
	<div class="footer" style="padding: 0px 5px 5px 5px; font-size: 12px;">
		<div style="float: left; width: 150px; text-align: left;">Jafaria Education Center</div>
		<div style="float: left; width: 360px;">1546 E La Palma Ave, Anaheim, CA 92805</div>
		<div style="float: left; width: 225px; text-align: right;"><a href="www.jafariaschool.org" style="color: #021eaa;">www.jafariaschool.org</a></div>
		<div class="clear"></div>
	</div>
	<pagebreak />
	
	<div style="text-align: center;">
		<div><img src="<?php echo base_url('static/images/handbook-line.png'); ?>" style="width: 80%;" /></div>
		<div style="padding: 150px 0; font-size: 16px; color: #ff6150; letter-spacing: 2px;">
			OUR VISION<br /><br />
			<div style="font-style: italic;">
			TO PROVIDE ISLAMIC EDUCATION FOR OUR CHILDREN TO BECOME<br />
			FOLLOWERS OF AHLULBAYT AND MAKE A POSITIVE IMPACT ON OUR<br />
			DYNAMIC SOCIETY.
			</div>
		</div>
		<div style="font-size: 16px; letter-spacing: 1px; line-height: 35px;">
			Jafaria Education Center (JEC), under Jafaria Islamic Society (JIS) operates a<br />
			Sunday school to give Islamic Education to children. All staff/teachers are on a<br />
			voluntary basis and work hard to give children the best Islamic education possible.<br />
			In order to run Sunday school most effectively and efficiently, certain rules and<br />
			responsibilities described below need to be implemented with the full support and<br />
			cooperation of parents. In addition, please sign and return the “Consent and<br />
			Release of Liability” declaration.
		</div>	
	</div>
	<div class="footer">1</div>
	<pagebreak />
	
	<div style="padding: 0 0 25px 0;">
		<div style="text-align: center;">
			<div><img src="<?php echo base_url('static/images/handbook-line.png'); ?>" style="width: 80%;" /></div>
		</div>
		<div class="cnt-listing" style="line-height: 35px;">
			<div class="left">•</div>
			<div class="right">
				<strong>Assembly</strong>: Sunday School begins with an assembly at 10:30am. The assembly is an integral part of the school’s schedule. It is a valuable means to disperse information about the school and its activities. The assembly starts the day with a brief Islamic lesson, which puts the children in a spiritual, cheerful and learning mindset. The parents are requested to drop their children on time before the assembly so that they can join, learn and benefit from the assembly.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Absences</strong>: Parents must ensure that students attend the school regularly. Any absence should be reported promptly by completing the absence form on the parental dashboard. The completed form must be sent in before the school day begins. Punctuality and good attendance is mandatory.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Late arrivals</strong>: Any student who arrives after 10:30am will be marked as late. Parents are required to sign in the logbook with security guard or at the administration office and provide reasons. Students tardiness disrupts the teacher & class instructions, so we request the parents to bring their children to school on time.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Early Pickup</strong>: For early pickup, please call administration office prior to pickup and sign your child out. Parents are not allowed to call or make arrangements with their children and pick them up without informing the administration. Early pickup causes them to miss the lecture and valuable information, which may affect their grade.
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="header">REGULATIONS</div>
	<div class="footer">2</div>
	<pagebreak />
	
	<div style="padding: 0 0 0 0;">
		<div class="cnt-listing" style="line-height: 29px;">
			<div class="left">•</div>
			<div class="right">
				<strong>Home work / Assignment</strong>: Check dashboard & your emails regularly for home work, test and quizzes. Assist your children with their homework & school activities. If you did not attend our Back to School Night, please be in touch with administration to schedule your dashboard login & training session.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Text Book</strong>: Text books are provided to the students at no cost. If a student needs to replace his/her book, you will be asked to pay for the replacement of the book. The book can also be downloaded from our school website.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Dress Code</strong>: All students, staff, teachers, volunteers and parents / visitors must adhere to Islamic dress code; no exception. Girls must wear loose pants, long tops with full sleeves and an appropriate scarf which is not transparent and covers all of their hair. For boys, no shorts or shirts with pictures or lyrics are allowed.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Disciplinary Actions</strong>: Students breaking the school rules will be disciplined. Actions causing the disciplinary actions are, but not limited to, being disrespectfulor misbehaving with staff/teachers etc.
				<div class="left">•</div>
				<div class="right" style="padding: 0 0 5px 0;">1st Warning - the student will be given a verbal warning</div>
				<div class="clear"></div>
				<div class="left">•</div>
				<div class="right" style="padding: 0 0 5px 0;">2nd Warning - email will be sent to parents and / or the matter will be discussed with parents.</div>
				<div class="clear"></div>
				<div class="left">•</div>
				<div class="right" style="padding: 0 0 5px 0;">3rd & Final warning will be issued & if an issue persists, it may result in suspension/dismissal from school.</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Tuition Fees & Donation</strong>: Tuition is due at the beginning of the term. The tuition can be paid by either cash or check payable to JIS. Those who cannot afford to pay the fee will be given a concession depending upon their individual circum stances. Your donations and support are always welcomed to offset the cost of the school programs.
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="footer" style="padding-top: 0px;">3</div>
	<pagebreak />
	
	<div>
		<div class="cnt-listing">
			<div class="left">•</div>
			<div class="right">
				<strong>Lunch</strong>: During a school year, each family is required to sponsor a one-time lunch. If you are not able to arrange food or if all the spaces are taken, you will be asked to pay the equivalent cash donation. It will be used to defray the food cost for the ongoing events and the other extra- curricular activities throughout the school year.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Communication</strong>: Good communication is essential to maintain a positive learning environment. We use a variety of communication channels such as dashboard, email, school website, facebook page, newsletter and text messages to ensure that the relevant people receive the necessary information. We also arrange events such as Back to School Night, End of the year program, and other activi ties / events. It is very important for parents to participate and show their com mitment to the students, teachers and volunteers.
			</div>
			<div class="clear"></div>
			<div class="left">•</div>
			<div class="right">
				<strong>Supervision</strong>: We make all possible efforts to provide supervision during school hours. We ask that students not be dropped off earlier than 15 minutes prior to school starting time. We allow 15 minutes optional play time after school finishes and students must be picked up immediately thereafter. Parents for classes 1 & 2 are required to drop their children to their respective teacher and sign in / sign out at that time. Parents/Visitors are not allowed in classrooms or inside the building when the classes are in session unless prior arrangements are made.
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="footer">4</div>
	<pagebreak />
	
	<div style="padding: 0px;">
		<div style="text-align: center;">
			<div><img src="<?php echo base_url('static/images/handbook-line.png'); ?>" style="width: 80%;" /></div>
		</div>
		<div class="cnt-listing">
			<div>The following ideals are which we all, teachers and students alike, are expected to apply and put in to practice in our own lives:</div>
			<div class="left-less">•</div>
			<div class="right-less">Always be your best and do your best</div>
			<div class="left-less">•</div>
			<div class="right-less">Treat all others and their property with courtesy and respect</div>
			<div class="left-less">•</div>
			<div class="right-less">Listen to your teacher and to your classmates when they are speaking</div>
			<div class="left-less">•</div>
			<div class="right-less">Show your desire to learn and participate in class activities with full motivation</div>
			<div class="left-less">•</div>
			<div class="right-less">Follow all the instructions given by your teacher</div>
			<div class="left-less">•</div>
			<div class="right-less">Make sure you bring all appropriate materials to class</div>
			<div class="left-less">•</div>
			<div class="right-less">Be on time for all lessons and activities</div>
			<div class="left-less">•</div>
			<div class="right-less">Each week you are expected to bring: your book, pens, pencils, rulers and erasers</div>
			<div class="left-less">•</div>
			<div class="right-less">An Islamic dress code should be observed by all</div>
			<div class="left-less">•</div>
			<div class="right-less">Food, drink, chewing gum etc. are only to be consumed during break time</div>
			<div class="left-less">•</div>
			<div class="right-less">Classrooms must be left as clean as they were at the beginning of the day</div>
			<div class="left-less">•</div>
			<div class="right-less">Valuable items (cell phones, ipods etc.) are to be kept switched off and stored safely</div>
			<div class="left-less">•</div>
			<div class="right-less">All items are brought in at your own risk</div>
			<div class="left-less">•</div>
			<div class="right-less">Confiscated items can only be collected by parents/guardians from the administration office</div>
			<div class="clear"></div>
			<div>The above rules are aimed to establish a free and fair classroom environment where students and teachers are all able to participate without any fears. Jafaria Education Center fully expects all its students to ensure that these rules are observed at all times and in all situations.</div>
		</div>
	</div>
	<div class="header">SCHOOL RULES</div>
	<div class="footer">5</div>
	<pagebreak />
	
	<div style="padding: 0px;">
		<div style="text-align: center;">
			<div><img src="<?php echo base_url('static/images/handbook-line.png'); ?>" style="width: 80%;" /></div>
		</div>
		<div class="paragraph">
			<div class="line">I acknowledge that my child’s <u><?php echo $string_student; ?></u> participation to Jafaria Islamic Society Sunday School, its field trips, sports and other related events & activities may involve certain risks including but not limited to physical exertion, injuries such as falls, breaks, sprains and cuts, accidents, emotional distress, disabilities or loss of life.</div>
			<div class="line">On behalf of my child, I hereby acknowledge these and other related risks and expressly assume all risks, including property damage, personal injury, and fatality, arising out of my child’s participation in the activities, regardless of whether or not caused in whole or in part by the negligence or other fault of Jafaria Islamic Society. In the case of accident, injury or illness, I grant the</div>
			<div class="line">Jafaria Islamic Society staff members the power to authorize emergency medical treatment necessary for my child. I assume responsibility of all resulting bills and costs, if any, associated with said medical procedures or treatment. I understand that Jafaria Islamic Society, its affiliates, its board members, Sunday School Principal, Administration, volunteers and any other associated personnel (“Releasees”) assume no responsibility for any injury or damage which might arise out of or in connection with such authorized emergency medical treatment. I agree to indemnify and hold harmless Releasees from any and all claims, demands, actions, right of action of judgments I may have arising from said medical procedures and treatment.</div>
			<div class="line">I agree, for myself, my heirs, executors and administrators not to sue and to release indemnify, defend and hold harmless Jafaria Islamic Society, its affiliates, its board members, Sunday School Principal, Administration, volunteers, any other associated personnel, and all sponsoring businesses and organizations and their agents and employees, from and against any and all liability, claims, expenses or penalties (including attorney’s fees), demands and causes of action whatsoever, arising out of or brought in connection with my child’s participation to Sunday School, events and/or related activities – whether resulting from the negligence or carelessness of any of the above or from any other cause.</div>
			<div class="line">Furthermore, I authorize the use of publication of my child’s name, image or voice as may be captured by photograph or recording while participating in the school or events in any medium for any purpose, including illustration, promotion, communication, advertisement, posted on social media and/or school website. The copyright(s) in such photograph, recording, illustration, promotion or advertisement or other material shall be owned by Jafaria Islamic Society.</div>
			<div class="line">We may use text message to contact and communicate with the family. Jafaria Islamic Society doesn’t pay or be responsible for any charges that may be incurred by you.</div>
		</div>
		<div style="float: right; width: 40%; text-align: center; font-size: 16px;">
			<div style="padding: 60px 0 0 0;"><?php echo date("F d, Y"); ?></div>
			
			<?php if (!empty($link_signature)) { ?>
			<div style="padding: 15px 0;"><img src="<?php echo $link_signature; ?>" style="width: 80%;" /></div>
			<?php } else { ?>
			<div style="padding: 10px 0; font-size: 36px; font-family: monotype;"><i><?php echo $text_signature; ?></i></div>
			<?php } ?>
			<div style="padding: 0 0 15px 0;"><?php echo $_POST['full_name']; ?></div>
			
			<div style="font-size: 12px;">
				This Form was electronically signed by <?php echo $_POST['full_name']; ?> at <?php echo date("F d, Y H:i"); ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="header">CONSENT & RELEASE OF LIABILITY</div>
	<div class="footer" style="padding: 0px 5px 5px 5px; font-size: 12px;">
		<div style="padding: 0 0 5px 0; font-size: 14px;">6</div>
		<div style="float: left; width: 150px; text-align: left;">Jafaria Education Center</div>
		<div style="float: left; width: 360px;">1546 E La Palma Ave, Anaheim, CA 92805</div>
		<div style="float: left; width: 225px; text-align: right;"><a href="www.jafariaschool.org" style="color: #021eaa;">www.jafariaschool.org</a></div>
		<div class="clear"></div>
	</div>
	
</body>