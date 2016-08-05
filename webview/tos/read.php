<?php
$user_agent = null_coalescing($REQUEST_HEADERS['user-agent'], '');
$is_iphone = stripos($user_agent, 'iphone') >= 0;
$is_ipad = stripos($user_agent, 'ipad') >= 0;
$is_ios = $is_iphone || $is_ipad;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Terms of Use</title>
		<link rel="stylesheet" href="/resources/css1.3/bstyle.css" />
		<link rel="stylesheet" href="/resources/css1.3/regulation.css" />
		<style type="text/css">
			.test {border: solid 1px #f00;}
			h2 {width:100%; font-size:28px; text-align:center; margin-top:20px;}
			h3 {width:100%; font-size:26px; margin-bottom:10px;}
			ul {margin-left:2em;}
			ul ul {margin-left:3em;}
			ul#tabs{margin-left:0;}
			li {list-style-type:disc;}
			li li {list-style-type:circle;}
			.footer img {width:913px;}
			.kc-p {font-size:24px; text-indent:2em; margin-bottom:1em;}
			.kc-bold {font-size:bold;}
		</style>
		<script src="/resources/js1.0/tab.js" type="text/javascript"></script>
		<script type="text/javascript">
			var iOSClient = <?php echo $is_ios ? 'true' : 'false'; ?>;
			if(iOSclient) {
			  var engineIF = function() {};
			  engineIF.prototype.cmd = function(keyString) {
				location.href = "native://" + keyString;
			  }
			}

			var eng = new engineIF();
			if (eng === undefined || eng === null) {
			  eng = {
				cmd : function() {
				//noop
				}
			  }
			}
		</script>
<?php
if($is_iphone):
?>
		<meta name="viewport" content="width=880px, minimum-scale=0.45, maximum-scale=0.45" />
<?php
elseif($is_ipad):
?>
		<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9" />
<?php
else:
?>
		<meta name="viewport" content="width=880px, user-scalable=no, initial-scale=1, width=device-width" />
<?php
endif;
?>
	</head>

	<body>
		<div id="wrapper_regu">
			<div class="title">
				<ul id="tabs" style="border-bottom: solid 2px #828282;">
					<li name="box1"><span  class="fs30">Terms of Use</span></li>
					<li name="box3"><span  class="fs30">Privacy Policy</span></li>
				</ul>
			</div>

			<div id="box1">
				<div class="content_regu">
					<div class="note">
						<!--利用規約-->
						<p class="fs34 tx_center mt20">Terms of Use</p>
						<p class="kc-p">The following are KLab Global Pte. Ltd. and its related entities, including but not limited to, KLab Inc. (collectively referred to as "<strong>KLab</strong>", "<strong>we</strong>", "<strong>us</strong>", or "<strong>our</strong>") terms and conditions of use ("<strong>Terms</strong>"):</p>

						<p class="kc-p">Please read these Terms carefully because they govern your access to and use of the <strong>KLab Services</strong> (as defined below), KLab Content, and User Content (defined below) and are legally binding. KLab’s Privacy Policy is incorporated as a part of these Terms and, by installing, accessing or using the KLab Services you explicitly agree with the terms and conditions of KLab’s Privacy Policy and to any terms and conditions included therein by reference.</p>

						<p class="kc-p">YOU ACKNOWLEDGE AND AGREE THAT, BY CLICKING ON THE "AGREE" BUTTON WHERE APPLICABLE, OR BY ACCESSING OR USING THE KLAB SERVICES OR BY DOWNLOADING OR POSTING ANY CONTENT ON OR THROUGH THE KLAB SERVICES, YOU ARE INDICATING THAT YOU HAVE READ, UNDERSTAND AND AGREE TO BE BOUND BY THESE TERMS. IF YOU DO NOT AGREE TO THESE TERMS, THEN YOU HAVE NO RIGHT TO ACCESS OR USE THE KLAB SERVICES OR KLAB CONTENT.</p>

						<p class="kc-p">The "KLab Services" means (i) the websites, including any services, applications, games, features and content accessible or downloadable from the websites, and (ii) any other application, game, service, product, feature or content of KLab or its licensors licensed, downloaded, or otherwise assessed by you through any third party website, platform or source. KLab Services also include updates and upgrades as well as accompanying manuals and documentation, and all copies thereof.</p>

						<p class="kc-p">Your access to or use of the KLab Services may have different service-specific terms and conditions ("<strong>Rules</strong>") posted on our websites or third party websites or platforms (such as terms related to a specific mobile game), or may require you to agree with and accept such additional Rules. If there is a conflict between these Terms and Rules, the applicable Rules will take precedence with respect to your use of, or access to, the KLab Services.</p>

						<h3>1. Eligibility and Registration</h3>

						<p class="kc-p">Unless otherwise provided for a specific KLab Service, in order to access the KLab Services, and to post any User Content on or through the KLab Services, you must register through the registration process established by KLab and obtain approval from us to access and use the KLab Services ("<strong>Approval</strong>"). Upon Approval, you will become an authorized user of the KLab Services ("<strong>User</strong>") and be issued a Gamer ID ("<strong>Gamer ID</strong>") which is necessary to use the KLab Services, and if we determine appropriate, a password for the Gamer ID will also be issued. The Gamer ID and password cannot be modified. The Gamer ID will contain Personal Information (as defined in KLab’s Privacy Policy) and Non-Identifying Information (as defined in KLab’s Privacy Policy) as well as Virtual Items and Virtual Currency (as defined below).</p>

						<p class="kc-p">You agree that if you are under the age of 18 or whatever is the age of legal majority where you access the KLab Services, you represent that your legal guardian has read, understood, and agreed to these Terms.</p>

						<p class="kc-p">In registering for Approval and a Gamer ID, you agree to monitor and restrict any use of the KLab Services by minors. You accept full responsibility for any unauthorized use of the KLab Services by minors, and this includes any use of your credit cards or other payment or settlement instruments or devices by minors.</p>

						<p class="kc-p">You may not register for Approval or a Gamer ID if you are below the age of 14. In registering for Approval and a Gamer ID, you agree to prevent access to anyone under 14. Since the KLab Services are not intended for children under the age of 14 (“children”), access to the KLab Services or issuance of a Gamer ID is not knowingly granted to children.</p>

						<p class="kc-p">During the registration process, you will be required to provide certain information, including your email address. You agree to provide accurate, current and complete information during the registration process and to update such information to keep it accurate, current and complete. KLab reserves the right to suspend or terminate your Gamer ID if any information provided during the registration process or thereafter proves to be inaccurate, not current or incomplete.</p>

						<p class="kc-p">If you are issued a password, you are responsible for safeguarding your password. You agree not to disclose your password to any third party or to allow any third party to use your Gamer ID, and you agree to take sole responsibility for any activities or actions under your Gamer ID, whether or not you have authorized such activities or actions. You will immediately notify KLab of any unauthorized use of your Gamer ID and/or loss of your password. In the event of loss of your password, you agree that you will solely be responsible for any missing items related to the KLab Service, including but not limited to, Virtual Currency (as defined below).</p>

						<p class="kc-p">A Gamer ID will be issued per terminal (e.g., your smart phone, computer, etc.). If you decide to use your Gamer ID for another terminal, you will be required to complete KLab’s process for changing terminals.</p>


						<h3>2. Modification</h3>

						<p class="kc-p">KLab reserves the right, at its sole discretion, to modify, discontinue or terminate all or any of the KLab Services, including any portion thereof, on a global or individual basis, or to modify these Terms, at any time and without prior notice. If we modify these Terms or our Privacy Policy, we will post these Terms or Privacy Policy as modified or otherwise provide you with notice of the modification. By continuing to access or use the KLab Services after we have posted a modification to these Terms or Privacy Policy or have provided you with notice of a modification, you are indicating that you agree to be bound by the modified Terms or Privacy Policy. If the modified Terms or Privacy Policy are not acceptable to you, your only recourse is to cease using the KLab Services.</p>


						<h3>3. Content</h3>

						<p class="kc-p">The following types of content will be made available to you through the KLab Services:</p>

						<p class="kc-p">"<strong>KLab Content</strong>" means KLab’s and the KLab Services’ name, trademarks, logos, text, data, graphics, images, illustrations, forms, documents, marketing materials, look and feel attributes, or our licensors’ name, trademarks and logos, and other content made available by KLab on or through the KLab Services, but excluding User Content (as defined below).</p>

						<p class="kc-p">"<strong>User Content</strong>" means text, data, graphics, images, photos, and any other content uploaded, transmitted or submitted by you on or through the KLab Services.</p>


						<h3>4. Intellectual Property</h3>

						<p class="kc-p">The KLab Services and KLab Content are protected by copyright, trademark, and other laws of the United States and foreign countries. Except as expressly provided in these Terms, KLab and our licensors exclusively own all right, title and interest in and to the KLab Services and KLab Content, including all associated intellectual property rights, including any patents, copyrights, trademarks, service marks, trade names, database rights, domain name rights, applications for any of the foregoing, moral rights and trade secret rights ("<strong>Intellectual Property Rights</strong>").</p>

						<p class="kc-p">You will not remove, alter or obscure any copyright, trademark, service mark or other proprietary rights notices incorporated in or accompanying the KLab Services or KLab Content.</p>


						<h3>5. User License </h3>
						<p class="kc-p">Subject to your compliance with these Terms, KLab hereby grants you a limited, non-exclusive, non-transferable, non-sublicensable license to access, view, download and print, where applicable, the KLab Content and KLab Services solely for your personal and non-commercial purposes. You will not use, copy, adapt, modify, prepare derivative works based upon, distribute, license, sell, transfer, publicly display, publicly perform, transmit, stream, broadcast or otherwise exploit the KLab Services or KLab Content, except only as expressly permitted by these Terms. No licenses or rights are granted to you by implication or otherwise under any Intellectual Property Rights owned or controlled by KLab or its licensors, except for the licenses and rights expressly granted by these Terms.</p>


						<h3>6. User Content</h3>

						<p class="kc-p">By making available any User Content on or through the KLab Services, you hereby grant to KLab a worldwide, irrevocable, perpetual, non-exclusive, transferable, royalty-free license, with the right to sublicense, use, copy, adapt, modify, distribute, license, sell, transfer, publicly display, publicly perform, transmit, stream, broadcast and otherwise exploit such User Content only on, through or by means of the KLab Services. KLab does not claim any ownership rights in any such User Content and nothing in these Terms will be deemed to restrict any rights that you may have to use and exploit any such User Content.</p>

						<p class="kc-p">You acknowledge and agree that you are solely responsible for all User Content that you make available on or through the KLab Services. Accordingly, you represent and warrant that:</p>

						<ul>
						  <li>You either are the sole and exclusive owner of all User Content that you make available on or through the KLab Services or you have all rights, licenses, consents and releases that are necessary to grant to KLab the rights in such User Content as contemplated under these Terms; and </li>
						  <li>Neither the User Content nor your posting, uploading, publication, submission or transmittal of the User Content or KLab’s use of the User Content (or any portion thereof) on, through or by means of the KLab Services will infringe, misappropriate or violate a third party's Intellectual Property Rights, or rights of publicity or privacy, or result in the violation of any applicable law or regulation.</li>
						</ul>


						<h3>7. No Infringing Use</h3>

						<p class="kc-p">You will not use the KLab Services to offer, display, distribute, transmit, route, provide connections to or store any material that infringes copyrighted works or otherwise infringes, violates, or promotes the infringement or violation of the Intellectual Property Rights of any third party.</p>


						<h3>8. Virtual Items and Virtual Currency</h3>

						<p class="kc-p">KLab owns, has licensed, or otherwise has the rights to use all KLab Content and the KLab Services, including virtual items ("<strong>Virtual Items</strong>") and virtual currency such as "gold" ("<strong>Virtual Currency</strong>"). </p>

						<p class="kc-p">Virtual Items and Virtual Currency are provided solely for your personal and entertainment use, they may only be used in the applicable KLab Services, and they have no "real world" value. By purchasing or receiving Virtual Items and Virtual Currency, all that you receive is a limited license to use them in the applicable KLab Services by these Terms or such other terms as may apply; Virtual Items and Virtual Currency are not your personal property and no ownership interest in them is transferred to you.</p>

						<p class="kc-p">The prices for and the amounts and kinds of Virtual Items and Virtual Currency available may be changed at any time without notice. Virtual Items and Virtual Currency that you receive may also be changed or discontinued at any time without notice.</p>

						<p class="kc-p">You cannot sell or transfer, or attempt to sell or transfer, Virtual Items or Virtual Currency, except only that where allowed you can exchange, within the applicable KLab Services, those Virtual Items and Virtual Currency that cannot be purchased with "real world" money ("<strong>Tradable Items</strong>") for any other User’s Tradable Items so long as no money or anything of monetary value is paid or given for Tradable Items; any other purported or attempted exchange is strictly prohibited.</p>

						<p class="kc-p">Virtual Items and Virtual Currency may never be redeemed by you for “real world” money, goods, wares, merchandise, services, or anything of monetary value from KLab or any other person.</p>

						<p class="kc-p">You are solely responsible for any payments of Virtual Items and Virtual Currency made through a third-party digital media distribution service ("<strong>Third-Party Distributor</strong>"), and any claims, demands, or complaints made against a Third-Party Distributor shall be resolved between you and such Third-Party Distributor. KLab will not be liable for any claims, demands, or complaints you make against a Third-Party Distributor.</p>


						<h3>9. Orders and Payment </h3>

						<p class="kc-p">You agree that if you are under the age of 18 or whatever is the age of legal majority where you access the KLab Services, you may make payments only with the involvement of your legal guardian, and you represent that your legal guardian has read, understood, and agreed to these Terms.</p>

						<p class="kc-p">You may purchase, with "real world" money, limited licenses to use Virtual Items or Virtual Currency from KLab in accordance with these Terms, and you agree that all such purchases are final. If you order licenses for Virtual Items or Virtual Currency from KLab that become unavailable before they can be provided to you, your only remedy is to request a refund of the purchase price from the payment processor of the transaction.</p>

						<p class="kc-p">Your orders for limited licenses to Virtual Items or Virtual Currency are offers for use of those Virtual Items or Virtual Currency, and if accepted those Virtual Items or Virtual Currency will be immediately downloaded to your Gamer ID.</p>

						<p class="kc-p">You expressly consent to the making available of Virtual Items and Virtual Currency immediately upon acceptance of your order. You understand and agree that KLab provides no refunds for any purchases except only as expressly stated in these Terms.</p>


						<h3>10. Taxes</h3>

						<p class="kc-p">You are responsible and will pay all fees and applicable taxes incurred by you or anyone using the Gamer ID registered to you.</p>


						<h3>11. Mobile Operating Software Providers and Third-Party Publishers</h3>

						<p class="kc-p">Providers of operating software for mobile devices ("<strong>OS Providers</strong>") offer virtual storefronts and marketplaces for you to browse, locate and download, among other things, mobile applications. If you download the KLab Services from a virtual storefront or marketplace operated by your OS Provider, please note that, in addition to complying with these Terms (and the terms and conditions of any applicable third-party publisher), you must also comply with the terms and conditions of such virtual storefront or marketplace, such as, for example, Apple’s iTunes market, Google’s Google Play market or the Amazon Appstore for Android market.</p>


						<h3>12. Interactions between Users</h3>

						<p class="kc-p">You are solely responsible for your interactions (including any disputes) with other Users. Even if we choose to offer report user, block user, or similar features on the KLab Services, you will remain solely responsible for, and you must exercise caution, discretion, common sense and judgment in, using the KLab Services and disclosing personal information to other Users. You agree to take reasonable precautions in all interactions with other Users, particularly if you decide to contact or meet another User offline, or in person. Your use of the KLab Services, KLab Content, User Content and any other content made available through the KLab Services is at your sole risk and discretion and KLab hereby disclaims any and all liability to you or any third party relating thereto. KLab reserves the right to contact Users, in compliance with applicable law, in order to evaluate compliance with these Terms and any other applicable Rules. You will cooperate fully with KLab to investigate any suspected unlawful, fraudulent or improper activity, including, without limitation, granting authorized KLab representatives access to any password-protected portions of gamer ID.</p>


						<h3>13. General Prohibitions</h3>

						<p class="kc-p">You agree not to do any of the following while using the KLab Services or using the KLab Content or User Content:</p>

						<ul>
						  <li>
							Post, upload, publish, submit or transmit any text, graphics, images, software, music, audio, video, information or other material that: 
							<ul>
							  <li>infringes, misappropriates or violates a third party's Intellectual Property Rights, or rights of publicity or privacy;</li>
							  <li>violates, or encourages any conduct that would violate, any applicable law or regulation or would give rise to civil liability;</li>
							  <li>is fraudulent, false, misleading or deceptive;</li>
							  <li>is defamatory, obscene, pornographic, vulgar or offensive;</li>
							  <li>promotes discrimination, bigotry, racism, hatred, harassment or harm against any individual or group;</li>
							  <li>is violent or threatening or promotes violence or actions that are threatening to any other person;</li>
							  <li>impersonates any employee, executive, or customer service support employee of KLab;</li>
							  <li>promotes illegal, harmful, or inappropriate activities or substances (including but not limited to activities that promote or provide instructional information regarding gambling, solicitation for sexual activities or dating, providing alcohol to minors, or the manufacture or purchase of illegal weapons, drugs, or substances).</li>
							</ul>
						  </li>
						  <li>Use, display, mirror, frame or utilize framing techniques to enclose the KLab Services, or any individual element or materials within the KLab Services, KLab Content, or KLab licensors’ trademarks, logos or other proprietary information, the content of any text or the layout and design of any page or form contained on a page, without KLab’s express written consent;</li>
						  <li>Access, tamper with, or use non-public areas of the KLab Services, KLab’s computer systems, or the technical delivery systems of KLab’s providers;</li>
						  <li>Attempt to probe, scan, or test the vulnerability of any KLab system or network or breach any security or authentication measures;</li>
						  <li>Avoid, bypass, remove, deactivate, impair, descramble or otherwise circumvent any technological measure implemented by KLab or any of KLab’s providers or any other third party (including another User) to protect the KLab Services or KLab Content;</li>
						  <li>Attempt to access or search the KLab Services or KLab Content, or download the KLab Services or KLab Content in the KLab Services, through the use of any engine, software, tool, agent, device or mechanism (including spiders, robots, crawlers, data mining tools or the like) other than the software and/or search agents provided by KLab or other generally available third-party web browsers (such as Google Chrome, Microsoft Internet Explorer, Mozilla Firefox, Apple Safari or Opera);</li>
						  <li>Send any unsolicited or unauthorized advertising, promotional materials, email, junk mail, spam, chain letters or other form of solicitation;</li>
						  <li>Use any meta tags or other hidden text or metadata utilizing KLab Content or KLab licensor’s trademark, logo URL or product name without KLab’s express written consent;</li>
						  <li>Use the KLab Services or KLab Content for any commercial purpose or the benefit of any third party or in any manner not permitted by these Terms;</li>
						  <li>Forge any TCP/IP packet header or any part of the header information in any email or newsgroup posting, or in any way the KLab Services or KLab Content to send altered, deceptive or false source-identifying information;</li>
						  <li>Attempt to decipher, decompile, disassemble or reverse engineer any of the software used to provide the KLab Services or KLab Content;</li>
						  <li>Interfere with, or attempt to interfere with, the access of any user, host or network, including, without limitation, sending a virus, overloading, flooding, spamming, or mail-bombing the KLab Services;</li>
						  <li>Collect, store, or disclose any Personal Information of other Users from the KLab Services, or from other Users, without their express permission;</li>
						  <li>Impersonate or misrepresent your affiliation with any person or entity;</li>
						  <li>Violate any applicable law or regulation;</li>
						  <li>Post User Content or take any action that infringes or violates the rights of another User;</li>
						  <li>Bully, harass or intimidate any User;</li>
						  <li>Solicit User passwords from another User or collect User Content or otherwise access the KLab Services by automated means including but not limited to, bots, robots, spiders;</li>
						  <li>Create a Gamer ID for anyone other than yourself;</li>
						  <li>Use cheats, exploits, hacks, bots, mods or third party software designed to gain an advantage, perceived or actual, over other Users, or modify or interfere with the KLab Services;</li>
						  <li>Abuse or exploit a bug, glitch or mechanism in the KLab Services;</li>
						  <li>Engage in any fraudulent behavior, including but not limited to credit card scams or credit card misappropriation; or</li>
						  <li>Encourage or enable any other individual to do any of the foregoing.</li>
						</ul>

						<p class="kc-p">KLab will have the right to investigate and prosecute violations of any of the above, including Intellectual Property Rights infringement and KLab Services security issues, to the fullest extent of the law. KLab may involve and cooperate with law enforcement authorities in prosecuting users who violate these Terms.</p>

						<p class="kc-p">You acknowledge that KLab has no obligation to monitor or record your access to or use of the KLab Services or KLab Content, or to monitor, record, or edit any User Content, but agree that we have the right to do so for the purpose of operating the KLab Services, to ensure your compliance with these Terms, or to comply with applicable law or the order or requirement of a court, administrative agency or other governmental body. You acknowledge and agree that you have no expectation of privacy concerning uploads, transmissions, or submissions of any User Content. KLab reserves the right, at any time and without prior notice, to remove or disable access to any User Content that KLab, in its sole discretion, considers to be in violation of these Terms or otherwise harmful to the KLab Services.</p>

						<p class="kc-p">We encourage Users to report any suspected misconduct or misuse of the KLab Services by sending us an email to email address designated by KLab for a specific KLab Service.</p>


						<h3>14. Sweepstakes and Contests</h3>

						<p class="kc-p">KLab may permit the offer of sweepstakes, contests and similar promotions (collectively, "<strong>Promotions</strong>") through the KLab Services. You should carefully review the rules of each Promotion in which you participate through the KLab Services, as they may contain additional important information about KLab’s rights to and ownership of the submissions you make as part of the Promotions and as a result of your participation in such Promotion. To the extent that the terms and conditions of such rules conflict with these Terms, the terms and conditions of such rules will control.</p>


						<h3>15. Transfer of user data</h3>

						<p class="kc-p">In the event you intend to transfer any of your data concerning your use of any KLab Services (including, but not limited to, User Content, Virtual Items and Virtual Currency) to another mobile devices or to a reinstalled version of any KLab Services, you shall comply with the procedures specified by KLab and acquire a password from KLab for such transfer.</p>

						<p class="kc-p">Data transfers shall be limited to transfers between the same version of a particular KLab Service on the same platform. Except in the event the transfer is effected by means of an authorized function of the applicable KLab Service, you may not transfer any of your data (including, but not limited to, User Content, Virtual Items and Virtual Currency) to any other KLab Service or to any other service, equipment or device or to any other platform.</p>


						<h3>16. Termination of Gamer ID</h3>

						<p class="kc-p">Without limiting other remedies, KLab may at any time suspend or terminate your Gamer ID and refuse to provide you access to all or any of the KLab Services if KLab suspects or determines, in its own discretion, that you may have or there is a significant risk that you have: (i) failed to comply with any provision of these Terms or any policies or Rules established by KLab; (ii) engaged in actions relating to or in the course of accessing or using the KLab Services that may be illegal or cause liability, harm, embarrassment, harassment, abuse or disruption for you, other Users, KLab or any other third parties or any KLab Services; or (iii) infringed the proprietary rights, rights of privacy, or Intellectual Property Rights of any person, including as a repeat infringer. In addition, KLab may notify authorities or take any other actions it deems appropriate, without notice to you, in the event of any of the above.</p>

						<p class="kc-p">You may terminate your Gamer ID at any time and for any reason by sending an email to email address designated by KLab for a specific KLab Service.</p>


						<h3>17. Effects of Termination, Suspension of Gamer ID</h3>

						<p class="kc-p">Upon termination of your Gamer ID for any reason by you or us, you will lose all access to such ID. Terminated Gamer IDs cannot be reinstated; any Gamer ID that may be registered by you after termination of a Gamer ID is a unique ID.</p>

						<p class="kc-p">If your Gamer ID is terminated for any reason by you or us, you understand and agree that any Virtual Items to which you had access via your Gamer ID at the time of termination will be lost and no longer be available to you, and you will have no right to them.</p>

						<p class="kc-p">If your Gamer ID is terminated for any reason by you or us, you understand and agree that KLab may redeem and use the Virtual Currency in the Gamer ID at the time of termination for any purpose that it may choose, and that on termination you will have no right to that Virtual Currency.</p>

						<p class="kc-p">In addition, if your Gamer ID is not used to use the KLab Services for a period of three (3) or more years ("<strong>Inactive Gamer ID</strong>"), you understand and agree that (i) KLab may redeem and use the Virtual Currency in the Inactive Gamer ID at such time for any purpose that it may choose, and that on the account becoming an Inactive Gamer ID you will have no right to that Virtual Currency; and (ii) any Virtual Items to which you had access via the Inactive Gamer ID up to the ID becoming an Inactive Gamer ID will be lost and no longer be available to you, and you will have no right to them.</p>

						<p class="kc-p">YOU AGREE THAT KLAB IS NOT REQUIRED TO PROVIDE A REFUND FOR ANY REASON, AND THAT YOU WILL NOT RECEIVE MONEY OR OTHER COMPENSATION FOR UNUSED VIRTUAL ITEMS OR VIRTUAL CURRENCY IN AN INACTIVE GAMER ID OR THAT WAS IN A TERMINATED GAMER ID, NO MATTER HOW EITHER CAME ABOUT.</p>

						<p class="kc-p">After any termination, you understand and acknowledge that we will have no further obligation to provide the KLab Services and all licenses and other rights granted to you by these Terms will immediately cease. KLab will not be liable to you or any third party for termination of the Gamer ID or termination of your use of the KLab Services. UPON ANY TERMINATION OR SUSPENSION OF YOUR GAMER ID, ANY CONTENT, MATERIALS OR INFORMATION (INCLUDING USER CONTENT) THAT YOU HAVE SUBMITTED ON THE KLAB SERVICES OR THAT WHICH IS RELATED TO YOUR GAMER ID MAY NO LONGER BE ACCESSED BY YOU. Furthermore, KLab will have no obligation to maintain any information stored in our database related to your Gamer ID or to forward any information to you or any third party.</p>

						<p class="kc-p">Any suspension, termination or cancellation will not affect your obligations to KLab under these Terms (including, without limitation, proprietary rights and ownership, indemnification and limitation of liability), which by their sense and context are intended to survive such suspension, termination or cancellation.</p>


						<h3>18. Disclaimers</h3>

						<p class="kc-p">THE KLAB SERVICES, KLAB CONTENT, AND USER CONTENT ARE PROVIDED AS IS, WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED. WITHOUT LIMITING THE FOREGOING, KLAB EXPLICITLY DISCLAIMS ANY WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT, AND ANY WARRANTIES ARISING OUT OF COURSE OF DEALING OR USAGE OF TRADE.</p>

						<p class="kc-p">KLAB MAKES NO WARRANTY THAT THE LODS GAME, KLAB CONTENT OR USER CONTENT WILL MEET YOUR REQUIREMENTS OR BE AVAILABLE ON AN UNINTERRUPTED, SECURE, OR ERROR-FREE BASIS. KLAB MAKES NO WARRANTY REGARDING THE QUALITY OF ANY PRODUCTS, SERVICES OR CONTENT PURCHASED OR OBTAINED THROUGH THE KLAB SERVICES OR THE ACCURACY, TIMELINESS, TRUTHFULNESS, COMPLETENESS OR RELIABILITY OF ANY CONTENT OBTAINED THROUGH THE LODS GAME.</p>

						<p class="kc-p">NO ADVICE OR INFORMATION, WHETHER ORAL OR WRITTEN, OBTAINED FROM KLAB OR THROUGH THE KLAB SERVICES, KLAB CONTENT OR USER CONTENT, WILL CREATE ANY WARRANTY NOT EXPRESSLY MADE HEREIN.</p>


						<h3>19. Indemnity</h3>

						<p class="kc-p">You agree to defend, indemnify, and hold KLab, its officers, directors, employees and agents, harmless from and against any claims, liabilities, damages, losses, and expenses, including, without limitation, reasonable legal and accounting fees, arising out of or in any way connected with User Content you submit to KLab, your access to and use of the KLab Services or KLab Content, or your violation of these Terms.</p>


						<h3>20. Limitation of Liability</h3>

						<p class="kc-p">YOU ACKNOWLEDGE AND AGREE THAT, TO THE MAXIMUM EXTENT PERMITTED BY LAW, THE ENTIRE RISK ARISING OUT OF YOUR ACCESS TO AND USE OF THE KLAB SERVICES, KLAB CONTENT, AND USER CONTENT THEREIN REMAINS WITH YOU. NEITHER KLAB NOR ANY OTHER PARTY INVOLVED IN CREATING, PRODUCING, OR DELIVERING THE KLAB SERVICES OR KLAB CONTENT WILL BE LIABLE FOR ANY INCIDENTAL, SPECIAL, EXEMPLARY OR CONSEQUENTIAL DAMAGES, INCLUDING LOST PROFITS, LOSS OF DATA OR LOSS OF GOODWILL, SERVICE INTERRUPTION, COMPUTER DAMAGE OR SYSTEM FAILURE OR THE COST OF SUBSTITUTE PRODUCTS OR SERVICES, ARISING OUT OF OR IN CONNECTION WITH THESE TERMS OR FROM THE USE OF OR INABILITY TO USE THE KLAB SERVICES OR KLAB CONTENT THEREIN, WHETHER BASED ON WARRANTY, CONTRACT, TORT (INCLUDING NEGLIGENCE), PRODUCT LIABILITY OR ANY OTHER LEGAL THEORY, AND WHETHER OR NOT KLAB HAS BEEN INFORMED OF THE POSSIBILITY OF SUCH DAMAGE, EVEN IF A LIMITED REMEDY SET FORTH HEREIN IS FOUND TO HAVE FAILED OF ITS ESSENTIAL PURPOSE.</p>

						<p class="kc-p">YOU SPECIFICALLY ACKNOWLEDGE THAT KLAB IS NOT LIABLE FOR THE DEFAMATORY, OFFENSIVE OR ILLEGAL CONDUCT OF OTHER USERS OR THIRD PARTIES AND THAT THE RISK OF INJURY FROM THE FOREGOING RESTS ENTIRELY WITH YOU.</p>

						<p class="kc-p">IN NO EVENT WILL KLAB’S AGGREGATE LIABILITY ARISING OUT OF OR IN CONNECTION WITH THESE TERMS OR FROM THE USE OF OR INABILITY TO USE THE KLAB SERVICES OR KLAB CONTENT OR USER CONTENT THEREIN OR PROVIDED THEREBY EXCEED ONE HUNDRED SINGAPORE DOLLARS ($100). THE LIMITATIONS OF DAMAGES SET FORTH ABOVE ARE FUNDAMENTAL ELEMENTS OF THE BASIS OF THE BARGAIN BETWEEN KLAB AND YOU. SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OR LIMITATION OF LIABILITY OF CONSEQUENTIAL OR INCIDENTAL DAMAGES, AND SO THE ABOVE LIMITATION MAY NOT APPLY TO YOU.</p>


						<h3>21. Trademarks & Other Proprietary Rights Notices</h3>

						<p class="kc-p">All trademarks, service marks, logos, trade names and any other proprietary designations of KLab or our licensors used in association with the KLab Services are trademarks or registered trademarks of KLab or our licensors. Any other trademarks, service marks, logos, trade names and any other proprietary designations are the trademarks or registered trademarks of the respective owners of same.</p>


						<h3>22. Controlling Law and Jurisdiction</h3>

						</p>These Terms and any action related thereto will be governed by the laws of Singapore without regard to its conflict of laws provisions. The exclusive jurisdiction and venue of any action with respect to the subject matter of these Terms will be the courts located in Singapore, and each of the parties hereto waives any objection to jurisdiction and venue in such courts.</p>


						<h3>23. Entire Agreement</h3>

						<p class="kc-p">These Terms (including KLab’s Privacy Policy) constitute the entire and exclusive understanding and agreement between KLab and you regarding the KLab Services and KLab Content, and these Terms supersede and replace any and all prior oral or written understandings or agreements between KLab and you regarding the KLab Services and KLab Content.</p>


						<h3>24. Assignment</h3>

						<p class="kc-p">You may not assign or transfer these Terms, by operation of law or otherwise, without KLab’s prior written consent. Any attempt by you to assign or transfer these Terms, without such consent, will be null and of no effect. KLab may freely assign these Terms. Subject to the foregoing, these Terms will bind and inure to the benefit of the parties, their successors and permitted assigns.</p>


						<h3>25. Notices</h3>

						<p class="kc-p">You consent to the use of: (i) electronic means to complete these Terms and to deliver any notices or other communications permitted or required hereunder; and (ii) electronic records to store information related to these Terms or your use of the KLab Services. Any notices or other communications permitted or required hereunder, including those regarding modifications to these Terms, will be in writing and given: (x) by KLab via email (in each case to the address that you provide) or (y) by posting on or through the KLab Services. For notices made by e-mail, the date of receipt will be deemed the date on which such notice is transmitted.</p>


						<h3>26. General</h3>

						<p class="kc-p">The failure of KLab to enforce any right or provision of these Terms will not constitute a waiver of future enforcement of that right or provision. The waiver of any such right or provision will be effective only if in writing and signed by a duly authorized representative of KLab. Except as expressly set forth in these Terms, the exercise by either party of any of its remedies under these Terms will be without prejudice to its other remedies under these Terms or otherwise. If for any reason a court of competent jurisdiction finds any provision of these Terms invalid or unenforceable, that provision will be enforced to the maximum extent permissible and the other provisions of these Terms will remain in full force and effect. You agree that (i) these Terms are intended to cover any third-party publisher who is an affiliate of KLab ("<strong>Affiliate Publisher</strong>"); (ii) your obligations to KLab under these Terms extend to Affiliate Publishers as applicable unless otherwise agreed under any third-party terms with any such Affiliate Publisher; and (iii) Affiliate Publishers are third party beneficiaries under these Terms who may rely on and directly enforce these Terms against you as such.</p>


						<h3>27. Language</h3>

						<p class="kc-p">The controlling language of these Terms is English; any provided translation of these Terms is for purposes of convenience only and the English version shall govern to the extent of any inconsistency.</p>


						<h3>28. Contacting Us</h3>

						<p class="kc-p">If you have any questions about these Terms, please contact KLab to email address designated by KLab for a specific KLab Service.</p>

						<!-- <div class="mt40 tx_right">
						<p>KLab????<br />
						2013?3?1???</p>
						</div> -->
						<!--????????-->

							</div>
						</div>
						</div>




						<!--??????????-->
						<div id="box3">
						<div class="content_regu">
							<div class="note">
						<p class="fs34 tx_center mt20">Privacy Policy</p>
						<p class="fs20">KLab Global Pte. Ltd. Privacy Policy</p>
						<p class="fs20">Effective Date: April 1st, 2013 </p>
						<br />
						<p class="kc-p">
						  KLab Global Pte. Ltd. (including its subsidiaries, parent companies, joint ventures and other corporate entities under common ownership, collectively "KLab") has adopted this privacy policy ("Privacy Policy") to explain how KLab collects, stores, and uses the information collected in connection with KLab's products and services including, but not limited to, KLab's websites, applications for mobile platforms or devices (including third party applications utilizing the Services), web applications accessible on third party social networking services (SNS), game publisher and other networks and platforms, portals for third-party publishers, and web forums or messaging boards (collectively, the "Services").
						</p>

						<p class="kc-p kc-bold">
						  BY INSTALLING, USING, REGISTERING TO, OR OTHERWISE ACCESSING ANY SERVICES, YOU AGREE TO THIS PRIVACY POLICY (WHICH IS INCORPORATED INTO KLAB’S TERMS OF USE) AND TO THE PROCESSING, USE AND DISCLOSURE OF INFORMATION AS DESCRIBED HEREIN.
						</p>

						<p class="kc-p">
						  KLab reserves the right to modify this Privacy Policy from time to time. KLab will post all such changes together with the effective date for such updated Privacy Policy. By continuing to access or use the Services after KLab has posted an updated Privacy Policy or has provided you with notice of a modification, you are agreeing to be bound by the modified Privacy Policy. If the modified Privacy Policy is not acceptable to you, your only recourse is to cease using the Services.
						</p>

						<p class="kc-p">If you have any questions relating to this Privacy Policy or to any updates, please contact KLab at privacy@klab.com.</p>

						<h3>1. Collection of Personal Information and Non-identifying Information</h3>
						<p class="kc-p">
						  1.1 In the course of using the Services, KLab may ask you to provide certain personally identifiable information (“Personal Information”). The types of Personal Information that may be collected, processed or used may vary depending on the type of activity you are engaged in. Personal Information may include, but is not limited to, your name, screen/nick name, email address, Services or other IDs, phone number, photo or other image (including avatars), birthdate, gender, mailing address, friend connections, credit card information, shipping information, and location (if directly identifiable to you; otherwise, KLab treats location as non-personal data). KLab will collect, use, transfer and disclose Personal Information in accordance with this Privacy Policy. 
						</p>
						<p class="kc-p">
						  1.2 KLab also collects non-identifying information (“Non-identifying Information”). For purposes of this Privacy Policy, Non-identifying Information is information that does not enable KLab to directly identify you. The types of Non-identifying Information that KLab may collect and use include, but is not limited to: (i) device properties, including, but not limited to IP address, Media Access Control (“MAC”) address and unique device identifier or other persistent or non-persistent device identifier (“Device ID”); (ii) device software platform and firmware; (iii) mobile phone carrier; (iv) geographical data such as zip code, area code and location; (v) game progress, time used playing, score and achievements; and (vi) other non-personal data as reasonably required by KLab to enhance the Services. KLab may collect, use, transfer and disclose Non-identifying Information for any legal purpose.
						</p>
						<p class="kc-p">
						  1.3 Situations when you make Personal Information available to KLab may include, but are not limited to: (i) registration for Services, contests, promotions and special events; (ii) accessing Services using a third party ID, such as social networking sites or gaming services; (iii) subscribing to newsletters; (iv) purchasing a product or services through KLab’s online stores; (v) using “tell a friend,” "email this page," or other similar features (in which cases the names and contact information of your friends or other addressees may also be collected through the Services); (vi) requesting technical support; (vii) if you are a third party publisher, registration with KLab to develop third party applications utilizing the Services; and (viii) otherwise through use of Services where personal data is required for use and/or participation.
						</p>

						<h3>2. Use of Information</h3>
						<p class="kc-p">
						  2.1 Your Personal Information may be used for various purposes, including, but not limited to, the following:
						</p>
						  <ul>
							<li>To help KLab develop, deliver, manage and improve the Services and advertising;</li>
							<li>For market research purposes that support KLab’s efforts to deliver a more valuable service to KLab users;</li>
							<li>To serve ads that contain links to services, websites or application, and KLab may further utilize your Device ID to track clicks or other ad fulfillment metrics;</li>
							<li>To provide customer and technical support;</li>
							<li>To send you messages with informative or commercial content about KLab, the Services, or third party products and services that KLab thinks may be of interest to you;</li>
							<li>To allow KLab to keep you posted regarding our latest Services announcements, software updates, and upcoming events;</li>
							<li>To send important notices, such as communications about your purchases and changes to our Terms of Use, this Privacy Policy, or other Service-specific policies;</li>
							<li>To audit and analyze data to improve the Services and customer communications; and</li>
							<li>As otherwise permitted under this Privacy Policy.</li>
						  </ul>
						<br />
						<p class="kc-p">2.2 Personal Information and Non-identifying Information may be used alone or in the aggregate with information collected from other KLab users for any purpose permitted under this Privacy Policy and by law. Certain Non-identifying Information would be considered part of your Personal Information if it were combined with other identifiers in a way that enables you to be personally identified or contacts but are considered to be Non-identifying Information when taken alone or combined with other Non-identifying Information.</p>
						<p class="kc-p">2.3 Certain features of the Services may be able to connect to an SNS (e.g., Facebook). If you use such features or otherwise link your KLab account to an SNS, Personal Information and other information that you submit to the SNS may be collected and used by KLab in accordance with this Privacy Policy, and you hereby consent to such collection and use by KLab. KLab’s access to such Personal Information and other information will be in accordance with the privacy settings you have set in your SNS account, if any. KLab may also use Personal Information and other information that you have provided or made accessible to the SNS to help you establish social connections with other KLab users.</p>

						<h3>3. Disclosure and Transfer of Information </h3>
						<p class="kc-p">3.1 Personal Information and Non-identifying Information may be shared or otherwise disclosed or transferred in accordance with applicable law and this Privacy Policy.</p>
						<p class="kc-p">3.2 KLab may disclose Personal Information and Non-identifying Information to its parent companies and its subsidiaries and affiliates for use in accordance with this Privacy Policy.</p>
						<p class="kc-p">3.3 KLab may use agents, contractors or third party service providers (e.g., credit card processors, email service providers, software developers, user account authenticators, cloud storage providers, virtual currency service providers, administrative service providers, crowd-sourcing service providers, and shipping agents). KLab has the right to share your Personal Information and Non-identifying Information as necessary for such third parties to provide their services to KLab. KLab is not liable for the acts and omissions of these third parties, except as mandated by law.</p>
						<p class="kc-p">3.4 KLab may include in the Services tools, which may include tools from third party service providers, which may enable KLab or these third parties to analyze your and other users’ information. This information may include your UDID, Media Access Control address, International Mobile Equipment Identity Number, version of iOS or Android, specific type of device, mobile carrier, IP address and session start and end times. KLab and such third parties may use this information to detect the user’s country and otherwise analyze the user’s engagement. The third parties may have access to Personal Information in connection with their services but they will not be permitted to use such information, other than in an anonymized, aggregated form, for any purpose other than the provision of the services.</p>
						<p class="kc-p">3.5 KLab may disclose Personal Information to third parties as required by law enforcement or other government officials or judicial authorities in connection with litigation, an investigation of fraud, intellectual property infringement, or other activity that is or may be illegal or may expose KLab, any KLab users, you or third parties to legal liability.</p>
						<p class="kc-p">3.6 KLab may also disclose your Personal Information to third parties if KLab believes that doing so is appropriate to comply with the law, to enforce or exercise KLab’s rights, or to protect KLab’s, KLab’s users’ or third parties’ property, rights or safety.</p>
						<p class="kc-p">3.7 KLab may share Non-identifying Information and aggregate and anonymous information about you with advertisers, publishers and other third parties with whom KLab has relationships. The information that KLab shares with these third parties will only be used in the form of Non-identifying Information to support user trend analyses, demographic profiling and similar activities.</p>
						<p class="kc-p">3.8 KLab may also share your username, email address and Device ID (“Specific Shared Data”) with certain third party publishers which have designed applications utilizing the Services and other business partners (“Preferred Partners”) for those parties to assist KLab with providing you with the aspect of the Services for which you provided such Specific Shared Data to KLab, and to use such Specific Shared Data to provide you with information and marketing materials about such third party publisher applications and other products and services in order to further enhance your use of the Services and improve your gaming and social networking experience. In addition, if you make an inquiry to KLab regarding a third party publisher’s application, KLab may send Specific Shared Data as well as the inquiry to the applicable third party publisher.</p>
						<p class="kc-p">3.9 In the event that KLab undergoes a business transition, such as a merger, acquisition by another company, or sale of all or a portion of its assets or undergoes bankruptcy, reorganization, liquidation or analogous proceedings, KLab may transfer all of your information, including Personal Information, to the successor organization in such transition or as a part of such proceedings.</p>
						<p class="kc-p">3.10 In addition to the foregoing, KLab may also disclose your Personal Information with other third parties when we have received your consent to such disclosures.</p>

						<h3>4. Disclosure of Information Within the Services</h3>
						<p class="kc-p">As a user with an account, you may create your own profile, connect with other users of Services, send messages and transmit information through various channels. Please remember that any communications you have via the Services (including through KLab applications or websites or third party publishers utilizing the Services in their own applications or websites) may reveal your screen name and the content of your communications, making this information publicly available to other users. KLab does not assume responsibility, and disclaims liability for, for the activities of other users or other third parties to whom you provide your Personal Information or otherwise interact.</p>

						<h3>5. Location-Based Services</h3>
						<p class="kc-p">For location-based Services, KLab may collect, use, and share precise location data, including the real-time geographic location of your computer or mobile device. Unless you explicitly opt-in to permit the use and sharing of your Personal Information regarding your geographic location, your location data will only be collected and used anonymously in a form that does not personally identify you. When you opt in to location services of a third party publisher’s application which utilizes the Services, KLab will share your geographic location with such third party publisher and related application providers in connection with their location services. Please note that some location-based services may require your Personal Information for the feature to work as intended. </p>

						<h3>6. Cookies, Web Beacons and Other Technologies</h3>
						<p class="kc-p">6.1 A "cookie" is a data file that is transferred to a user's computer and is intended to identify that computer. Cookies are used to maintain information about a user's visit to an application, website or game, such as user names, passwords and individual preferences. The Services may send cookies to your computer when you access the Services. Information contained in a cookie may be linked to your Personal Information. That information may be used for a variety of purposes including improving the quality and ease of use of the Services, managing and sending advertising, studying traffic patterns, and understanding the effectiveness of marketing communications.</p>
						<p class="kc-p">6.2 The Services may also collect information using "web beacons," “pixel tags, "gifs" or similar means (collectively, “Pixel Tags”) in connection with our Services and to collect usage, demographic, geographical location or other data. A Pixel Tag is an electronic image, often a single pixel not visible to the user, and may be associated with cookies on a user’s hard drive. Pixel Tags allow, for example, the counting of users, the delivery of branded services, and the gathering of data to assess the effectiveness of promotional campaigns.</p>
						<p class="kc-p">6.3 The Services may include third party advertisements. These ads may be served by third party advertising companies that deliver cookies to your computer, allowing these companies to track the ads. These companies will consequently be able to recognize your computer when they send you an advertisement. These advertisers may use information about your visits to the Services in order to provide advertisements about goods and services that may be of interest to you.</p>
						<p class="kc-p">6.4 You have the option to disable cookies by changing the options in your browser to stop accepting cookies or to prompt you before accepting a cookie from the pages you visit. Please note, however, that “flash” cookies operate differently from browser cookies and cookie management tools available in a web browser may not remove “flash” cookies. If you do not accept cookies, however, you may not be able to view or use all portions of the Services due to limited functionality.</p>

						<h3>7. Security of Your Information</h3>
						<p class="kc-p">7.1 It is important that you protect and maintain the security of your account and that you immediately notify KLab of any unauthorized use of your account. If you forget the password to your KLab account, the Services allows you to request that instructions be sent to you that explain how to reset your password. When you sign into your account or enter payment information (such as a credit card number), KLab encrypts the transmission of that information using secure socket layer technology ("SSL").</p>
						<p class="kc-p">7.2 KLab takes commercially reasonable precautions against possible security breaches of our Services and customer records but no website or internet transmission is completely secure. KLab does not guarantee that unauthorized access, hacking, data loss, or other breaches will never occur. KLab urges you to take steps to keep your personal information safe (including your account password), and to log out of your account after use. In addition, if your SNS account is hacked, it may lead to unauthorized use of Services you have procured so be careful to keep your account information secure. </p>
						<p class="kc-p">7.3 The transmission of information over the internet is not completely secure. KLab cannot guarantee the security of your data while it is being transmitted and any transmission is at your own risk. </p>

						<h3>8. Changing or Removing Information</h3>
						<p class="kc-p">8.1 You may request that your Personal Information be changed or deleted by following the process provided in the applicable Services and we will take all reasonable measures to promptly comply with such requests. If you delete all of your information, your account may become deactivated. You may opt-out of receiving promotional emails from us by following the instructions in those emails. If you opt-out, KLab may still send you non-promotional emails, such as emails about your accounts or your ongoing business relations with KLab.</p>
						<p class="kc-p">8.2 You may also email KLab at privacy@klab.com. _ to request the deletion of your Personal Information from KLab’s database. KLab will use commercially reasonable efforts to honor your request. KLab may retain an archived copy of your records as required by law or for legitimate business purposes.</p>
						<p class="kc-p">8.3 Specific Shared Data cannot be retrieved from Preferred Partners after its disclosure.</p>
						<p class="kc-p">8.4 Where you share information with other users through the communication channels of the Services, you should generally not expect to have any ability to modify or remove such communications.</p>

						<h3>9. Links to Other Websites and Services</h3>
						<p class="kc-p">The Services may contain links to other websites and online services or offers from third parties, such as third party advertisers. If you choose to participate in offer-linked information requests or “click” an advertisement or other third party link, you may be directed to that third party’s website or service and/or be prompted to provide Personal Information. KLab is not responsible for the privacy practices or the content of such websites or services. If you have any questions about how these other websites or services use your information, you should review their policies and contact them directly.</p>

						<h3>10. International Transfer of Information</h3>
						<p class="kc-p">KLab may transfer information (including Personal Information) that is collected about you to KLab’s affiliates or to other third parties that may be outside of your country. If you are located in the European Union or any other country or jurisdiction, such locations may have laws governing data collection and use that are less strict or otherwise differ from the laws where the information may be transferred or used. Please note that, by accessing or using the Services, you are transferring information and allowing KLab or others to transfer information, including personal information, to a country that does not have the same data protection or privacy laws as your jurisdiction, and you consent to the transfer of information to countries outside of your jurisdiction and the use and disclosure of information about you, including Personal Information, as described in this Privacy Policy.</p>

						<h3>11. Policy Toward Children</h3>
						<p class="kc-p">The Services are not intended for children under the age of 13 and KLab does not knowingly collect any Personal Information from such children. If KLab discovers that it has inadvertently collected Personal Information from children, KLab will take all reasonable measures to erase such information from its systems and records as soon as possible.</p>

						<h3>12. Your California Privacy Rights</h3>
						<p class="kc-p">California Civil Code Section 1798.83 permits customers of KLab who are California residents to request certain information regarding our disclosure of personal information (as defined in the Code) to third parties for their direct marketing purposes. If you are a California resident and have provided KLab with personal information within the last year, you may make such a request by emailing us at the following email address with the email’s subject line titled "California Privacy Rights": privacy@klab.com.</p>

						<h3>13. Contact Information</h3>
						<p class="kc-p">If you have any questions, comments or concerns regarding this Privacy Policy, please send an e-mail to privacy@klab.com.</p>

						<p class="fs20">KLab Global Pte. Ltd. </p>
						<p class="fs20">80 Robinson Road, #10-01a </p>
						<p class="fs20">Singapore 068898 </p>
					</div>
				</div>
			</div>
			<!--プライバシーポリシーここまで-->
			<div class="footer">
				<img id="footer-img" src="/resources/img/help/bg03.png" />
			</div>
		</div>
		<script src="resources/js/tab.js" type="text/javascript"></script>
		<script type="text/javascript">
			var iOSclient = <?php echo $is_ios ? 'true' : 'false'?>;
			if(iOSclient) {
				var engineIF = function() {};
					engineIF.prototype.cmd = function(keyString) {
					location.href = "native://" + keyString;
				};
				var eng = new engineIF();
				var footerImg = document.getElementById("footer-img");
					footerImg.style.width = "912px";
			}  
			
			
			if (eng === undefined || eng === null) {
				eng = {
					cmd : function() {
						//noop
					}
				}
			}
		</script>
	</body>
</html>
