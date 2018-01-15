<?php
$lost_password_subject = translate("Password request");
$lost_password_text    = translate("Password request") . "\n\n" . translate("We understand you'd like to change your password. Please click on the following link and follow the prompts.") . "\n\n%s\n\n" .translate("Once your registration is complete, please use the following link to access your account :") . "\n\n" ."%%access_website%%". "\n\n" . translate("If you have questions, please don't hesitate to contact us at %s anytime. Our representatives will be happy to help.") . "\n\n" . translate("You didn't ask to change your password? Then just ignore this email.") . "\n\n" . translate("* Please note that the link will be no longer valid once it has been clicked.") . "\n\n\n\n".'%%clinic_address%%';
$lost_password_html    = '<font style="font-family:arial, sans-serif; font-size:14px;"><b>' . translate("Password request") . '</b><br><br>' . "\n" . '</font><font style="font-family:arial, sans-serif; font-size:12px;">' . translate("We understand you'd like to change your password. Just <a href=\"%s\">click here</a> and follow the prompts.") ."<br><br>" . translate("Once your registration is complete, please use the following link to access your account :") . "<br><br>" ."%%access_website%%". "<br><br>". translate("If you have questions, please don't hesitate to contact us at %s anytime. Our representatives will be happy to help.") . '<br><br>' . translate("You didn't ask to change your password? Then just ignore this email.") .  '<br><br></font>';

$send_program_subject = translate("Your exercise program from %s");
$send_program_text    = '%%prepend%%' . translate("You can access your online report at:\n\n%s\n\nIf the link does not work, make a copy and paste of the link in your browser.\n\nProgram name: %s\n\nUser name:%s\nPassword: %s\n\n%s\n%%clinic_address%%\n\n%s");
$send_program_html    = '<font style="font-family:arial,sans-serif; font-size:14px;">%%prepend%%' . translate("You can access your online report <a target=\"blank\" href=\"%s\">here</a>.") . '<br><br>' . translate("Program name: %s") . '<br><br>' . translate("User name: %s") . '<br>' . translate("Password: %s") . '<br><br>%s<br><br>%s %s<br><br></font>';

$new_account_subject = translate("New Account");
$new_account_HTML    = '<font style="font-family:arial, sans-serif; font-size:14px;"><b>' . 
			htmlentities(translate("New Account")) . '</b><br><br>' . "\n" . 
			'</font><font style="font-family:arial, sans-serif; font-size:12px;">' .
			htmlentities(translate("Hi")) . "%%UserFulName%%" . '<br><br>' .
			htmlentities(translate("Welcome to")) . " %%brand_name%%." . '<br><br>' .
			translate("Click on the button") . " " .
			"<!--[if mso]>".
			"<v:roundrect xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:w=\"urn:schemas-microsoft-com:office:word\" href=\"%%url_link%%\" style=\"height:25px;v-text-anchor:middle;width:65px;\" arcsize=\"20%\" strokecolor=\"#0B77BD\" fillcolor=\"#0B77BD\">".
			"<w:anchorlock/>".
			"<center style=\"color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:12px;\">" . translate("Activate") . "</center>".
			"</v:roundrect>".
			"<![endif]-->".
			"<a href=\"%%url_link%%\" style=\"background-color:#0B77BD;border:1px solid #0B77BD;border-radius:3px;color:#ffffff;font-family:sans-serif;font-size:14px;line-height:25px;text-align:center;text-decoration:none;width:70px;-webkit-text-size-adjust:none;mso-hide:all;\">" . translate("Activate") . "</a>" .
			" " . translate("to create your username and password.") . "<br><br>" .
			translate("If you don't see the button <a href=\"%%url_link%%\">click here</a>.") . "<br><br>" .
			translate("Our team is ready to support you anytime at :")."<br><br>" . 
			"%%brand_contact%%". "<br><br><br>";
						
$new_account_Text    = translate("New Account") . "\n\n" . 
			htmlentities(translate("Hi")) . "%%UserFulName%%" . "\n\n" .
			htmlentities(translate("Welcome to")) . " %%brand_name%%." . "\n\n" .
			translate("click the following link to create your username and password.") . "\n\n%%url_link%%\n\n" .
			translate("If a link above doesn't work, please copy and paste the URL into a browser.") . "\n\n" .
			translate("Our team is ready to support you anytime at :")."\n\n".
			"%%brand_address%%". "\n\n" ;
						
$reset_account_subject = translate("Updating Credentials");
$reset_account_HTML =	'<font style="font-family:arial, sans-serif; font-size:14px;"><b>' . 
			htmlentities(translate("Reset Account")) . '</b><br><br>' . "\n" . 
			'</font><font style="font-family:arial, sans-serif; font-size:12px;">' .
			htmlentities(translate("Hi")) . "%%UserFulName%%" . '<br><br>' .
			htmlentities(translate("Welcome to")) . " %%brand_clinic_name%%." . '<br><br>' .
			translate("Click on the button") . " " .
			"<!--[if mso]>".
			"<v:roundrect xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:w=\"urn:schemas-microsoft-com:office:word\" href=\"%%url_link%%\" style=\"height:25px;v-text-anchor:middle;width:65px;\" arcsize=\"20%\" strokecolor=\"#0B77BD\" fillcolor=\"#0B77BD\">".
			"<w:anchorlock/>".
			"<center style=\"color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:12px;\">" . translate("Activate") . "</center>".
                          "</v:roundrect>".
                        "<![endif]-->".
                        "<a href=\"%%url_link%%\" style=\"background-color:#0B77BD;border:1px solid #0B77BD;border-radius:3px;color:#ffffff;font-family:sans-serif;font-size:14px;line-height:25px;text-align:center;text-decoration:none;width:70px;-webkit-text-size-adjust:none;mso-hide:all;\">" . translate("Activate") . "</a>" .
                        " " . translate("to reset your username and password.") . "<br><br>" .
                        translate("If you don't see the button  <a href=\"%%url_link%%\">click here</a>.") . "<br><br>" .
                        translate("Our team is ready to support you anytime at :")."<br><br>" . 
                        "%%clinic_brand_address%%". "<br><br><br>";
						
$reset_account_Text = 	translate("Reset Account") . "\n\n" . 
                        htmlentities(translate("Hi")) . "%%UserFulName%%" . "\n\n" .
                        htmlentities(translate("Welcome to")) . " %%brand_clinic_name%%." . "\n\n" .
                        translate("click the following link to reset your username and password.") . "\n\n%%url_link%%\n\n" .
                        translate("If a link above doesn't work, please copy and paste the URL into a browser.") . "\n\n" .
                        translate("Our team is ready to support you anytime at :")."\n\n".
                        "%%clinic_brand_address%%". "\n\n" ;

$confirm_account_update_subject = translate("Account Updated");
$confirm_account_update_HTML =	'<font style="font-family:arial, sans-serif; font-size:14px;"><b>' . 
                                htmlentities(translate("Account Updated")) . '</b><br><br>' . "\n" . 
                                '</font><font style="font-family:arial, sans-serif; font-size:12px;">' .
                                "%%brand_clinic_name%% " . htmlentities(translate("access confirmation")) . '<br><br>' .
                                translate("Thank you for setting up your account to %%brand_clinic_name%%.") . '<br><br>' .
                                translate("To login from %%brand_clinic_name%% , click on the button") . " " . 
                                "<!--[if mso]>".
                                  "<v:roundrect xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:w=\"urn:schemas-microsoft-com:office:word\" href=\"%%url_link%%\" style=\"height:25px;v-text-anchor:middle;width:180px;\" arcsize=\"20%\" strokecolor=\"#0B77BD\" fillcolor=\"#0B77BD\">".
                                        "<w:anchorlock/>".
                                        "<center style=\"color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:12px;\">" . translate("Access") . " %%brand_clinic_name%%</center>".
                                  "</v:roundrect>".
                                "<![endif]-->".
                                "<a href=\"%%url_link%%\" style=\"background-color:#0B77BD;border:1px solid #0B77BD;border-radius:3px;color:#ffffff;font-family:sans-serif;font-size:14px;line-height:25px;text-align:center;text-decoration:none;width:180px;-webkit-text-size-adjust:none;mso-hide:all;\">" . translate("Access") . " %%brand_clinic_name%%</a>" .
                                "<br><br>" .
                                translate("If you don't see the button  <a href=\"%%url_link%%\">click here</a>.") . "<br><br>" .
                                translate("Our team is ready to support you anytime at :")."<br><br>" . 
                                "%%clinic_brand_address%%". "<br><br><br>";
	
$confirm_account_update_Text = 	translate("Account Updated") . "\n\n" . 
                                "%%brand_clinic_name%% " . htmlentities(translate("access confirmation")) . "\n\n" .
                                translate("Thank you for setting up your account to %%brand_clinic_name%%.") . "\n\n" .
                                translate("To login from %%brand_clinic_name%%, click the following link.") . "\n\n%%url_link%%\n\n" .
                                translate("If a link above doesn't work, please copy and paste the URL into a browser.") . "\n\n" .
                                translate("Our team is ready to support you anytime at :")."\n\n".
                                "%%clinic_brand_address%%". "\n\n" ;

$email_template_special_5609 = 
				'<!DOCTYPE html>' . "\n" .
				'<html lang="en">' . "\n" .
				'<head>' . "\n" .
				'<meta charset="utf-8">' . "\n" .
				'<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n" .
				'<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n" .
				'<meta name="description" content="">' . "\n" .
				'<meta name="copyright" content="Copyright (c) 2014, ' . date("Y") . '"/>' . "\n" .
				'<meta property="og:title" content="Physiotec"/>' . "\n" .
				'<meta property="og:url" content="index.php"/>' . "\n" .
				'<meta property="og:type" content="website"/>' . "\n" .
				'<meta property="og:image" content="img/ico-social/og_default.png"/>' . "\n" .
				'<meta name="msapplication-TileImage" content="img/ico-social/metro-tile.png"/>' . "\n" .
				'<meta name="msapplication-TileColor" content="#ffffff"/>' . "\n" .
				'</head>' . "\n" .
				'<body>' . "\n" .
				'<font face="serif">An exercise program has been especially prepared for you by your physical therapist at DPT Sport. Please follow the easy 4-step instructions below to access your program. It should take less than two minutes' . "\n" .
				'<br><br><br><br>1) Open our website (<a href="http://www.dptsport.com" target="_blank">www.dptsport.com</a>) in another window/tab ' . "\n" .
				'<br><br><br><br>2) Click on "Home Exercise Patient Portal" (in the top right of our homepage) ' . "\n" .
				'<br><br>*This will bring you to our login page, where you can access your custom program at any time at your convenience. ' . "\n" .
				'<br><br><br><br>3) Go back to your email in the first window/tab- copy your "User name"; return to portal login page and paste User name in top box.' . "\n" .
				'<br><br><br><br>4) Repeat for Password and then paste in 2nd box on portal login page ' . "\n" .
				'%%email_content%%' . "\n" .
				'</font>' . "\n" .
				'</body>' . "\n" .
				'</html>';
$send_program_subject_special_5609   = "Your exercise program from %s";
$send_program_text_special_5609       = "An exercise program has been especially prepared for you by your physical therapist at DPT Sport. Please follow the easy 4-step instructions below to access your program. It should take less than two minutes\n\n\n1) Open our website (www.dptsport.com) in another window/tab\n\n\n\n2) Click on \"Home Exercise Patient Portal\" (in the top right of our homepage)\n\n*This will bring you to our login page, where you can access your custom program at any time at your convenience.\n\n\n\n3) Go back to your email in the first window/tab- copy your \"User name\"; return to portal login page and paste User name in top box.\n\n\n\n4) Repeat for Password and then paste in 2nd box on portal login page\n\nYou can access your online report at:\n\n%s\n\nMobile version: %s\n\nIf the link does not work, make a copy and paste of the link in your browser.\n\nProgram name: %s\nUser name:%s\n\nPassword: %s\n\n%s\n\n%s";
$send_program_html_special_5609       = "<br><br>You can access your online report at:" . "\n" .
					"<br><br><a target=\"blank\" href=\"%s\">%s</a>." . "\n" .
					"<br><br>Mobile version: <a href=\"%s\" target=\"_blank\">%s</a>" . "\n" .
					"<br><br>If the link does not work, make a copy and paste of the link in your browser." . "\n" .
					"<br><br>Program name: %s" . "\n" .
					"<br>User name: %s" . "\n" .
					"<br><br>Password: %s" . "\n" .
					"<br><br>%s" . "\n" .
					"<br><br>%s %s" . "\n";