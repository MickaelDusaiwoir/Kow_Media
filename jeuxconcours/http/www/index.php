<?php
	
	require_once('./w_config/config.php');
	require_once('./r_includes/class.mbus.php');
	require_once('./r_includes/class.dbaccess.php');
	require_once('./r_includes/functions.main.php');
	
	$mBus = new mbus();
	$db = new dbaccess($mBus);
	
	$contestCount = 0;
	$totalGain = 0;
	
	$HTMLList = NULL;
	if ( $contests = $db->get_array('SELECT `id`, `title`, `text`, `url`, `url_alt` FROM `jc_contests` WHERE `status` = 1 AND `position` > 0 ORDER BY `position` ASC LIMIT 20;') )
	{
		$contestCount = $db->get_count();
		
		$num = 1;
		foreach ( $contests as $contest )
		{
			$HTMLList .= "\t\t\t".'<div class="contest_content">'."\n";
			$HTMLList .= "\t\t\t\t".'<div class="contest_content_left">'."\n";
			$HTMLList .= "\t\t\t\t\t".'<h3 class="contest_title">Concours '.$num.' : '.$contest['title'].'</h3>'."\n";
			if ( $prizes = $db->get_array('SELECT `title`, `screenshot_url`, `count`, `value` FROM `jc_prizes` WHERE `contest_id` = '.$contest['id'].' AND `position` > 0 ORDER BY `position` ASC LIMIT 3;') )
			{
				$HTMLList .= "\t\t\t\t\t".'<div class="contest_visuals">'."\n";
				foreach ( $prizes as $prize )
				{
					$pCount = intval($prize['count']);
					$pValue = intval($prize['value']);
					
					$HTMLList .= "\t\t\t\t\t\t".'<div class="contest_prize_box">'."\n";
					$HTMLList .= "\t\t\t\t\t\t\t".'<h4 class="contest_prize_title">'.$prize['title'].'</h4>'."\n";
					$HTMLList .= "\t\t\t\t\t\t\t".'<div class="contest_prize_img">'."\n";
					$HTMLList .= "\t\t\t\t\t\t\t\t".'<img src="'.$prize['screenshot_url'].'" width="128" height="128" alt="" />'."\n";
					$HTMLList .= "\t\t\t\t\t\t\t".'</div>'."\n";
					if ( $pCount > 1 )
						$HTMLList .= "\t\t\t\t\t\t\t".'<p class="contest_prize_description">'.$pCount.' x '.number_format($pValue, 0, NULL, ' ').' €</p>'."\n";
					else
						$HTMLList .= "\t\t\t\t\t\t\t".'<p class="contest_prize_description">'.number_format($pValue, 0, NULL, ' ').' €</p>'."\n";
					$HTMLList .= "\t\t\t\t\t\t".'</div>'."\n";
					
					$totalGain += ($pCount * $pValue);
				}
				$HTMLList .= "\t\t\t\t\t\t".'<div class="float_breaker"></div>'."\n";
				$HTMLList .= "\t\t\t\t\t".'</div>'."\n";
			}
			$HTMLList .= "\t\t\t\t".'</div>'."\n";
			$HTMLList .= "\t\t\t\t".'<div class="contest_content_right">'."\n";
			$HTMLList .= "\t\t\t\t\t".'<div class="contest_content_right_top">'."\n";
			$HTMLList .= "\t\t\t\t\t\t".'<div id="hint_box">'."\n";
			$HTMLList .= "\t\t\t\t\t\t\t".'<p>'.str_replace("\n", '<br />', $contest['text']).'</p>'."\n";
			$HTMLList .= "\t\t\t\t\t\t".'</div>'."\n";
			$HTMLList .= "\t\t\t\t\t".'</div>'."\n";
			$HTMLList .= "\t\t\t\t\t".'<div class="contest_content_right_bottom">'."\n";
			$HTMLList .= "\t\t\t\t\t\t".'<div class="contest_play_button_box">'."\n";
			$HTMLList .= "\t\t\t\t\t\t\t".'<p class="button_text"><a href="'.( ALT_SITE ? $contest['url_alt'] : $contest['url']).'" title="Jouer au concours" target="_blank">Cliquez ICI pour jouer à ce concours</a></p>'."\n";
			$HTMLList .= "\t\t\t\t\t\t".'</div>'."\n";
			$HTMLList .= "\t\t\t\t\t".'</div>'."\n";
			$HTMLList .= "\t\t\t\t".'</div>'."\n";
			$HTMLList .= "\t\t\t\t".'<div class="float_breaker"></div>'."\n";
			$HTMLList .= "\t\t\t".'</div>'."\n";
			
			$num++;
		}
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Jeux concours GRATUITS : gagner cadeaux, gagner argent, gagner voiture, gagner Iphone...</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		
		<!-- facebook special -->
		<meta name="title" content="Je viens de jouer gratuitement pour <?php echo number_format($totalGain, 0, NULL, ' ').' €'; ?> de cadeaux en 8 min <?php echo rand(1,50); ?> via cette page." />
		<meta name="description" content="On y trouve des concours 100% gratuits et rapides où il suffit de s'inscrire pour participer. A gagner : voitures, argent, voyages,  Iphone 3GS..." />
		<meta name="medium" content="image" />
		<link rel="image_src" href="http://www.konkours.com/facebook/img/inedit.jpg" />
		<!-- facebook special -->
		
		<!-- Cascade Style Sheets -->
		<link rel="stylesheet" type="text/css" href="./r_css/main.css" media="screen" />

		<script type="text/javascript">
			function replace_facebook ()
			{
				document.getElementById('facebook').innerHTML = '<p class="button_text" style="color:#F00">Vérification du partage FACEBOOK en cours <img src="./r_css/loading.gif" /></p>';
				
				var timer = setTimeout(function (){
					document.location.href = 'http://www.jeuxconcours.be/redirection.php';
				}, 15000);
			}
		</script>
	</head>
	<body>
<?php
	
	if ( strstr($_SERVER['HTTP_HOST'], 'france') )
	{
		
?>
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-2347532-38']);
			_gaq.push(['_trackPageview']);

			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
<?php
		
	}
	else
	{
		
?>
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-2347532-36']);
			_gaq.push(['_trackPageview']);

			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
<?php
		
	}
	
?>
		<div id="document">
			<div id="document_header">
				<h1>La page ULTIME des Jeux Concours 100% GRATUITS</h1>
				<h2>Gagner des cadeaux, Gagner une voiture, Gagner un iPhone, Gagner de l'argent... C'est SIMPLE !</h2>
				<blockquote><p>En moins de 8 minutes, vous pouvez jouer GRATUITEMENT pour <?php echo number_format($totalGain, 0, NULL, ' ').' €'; ?> de cadeaux grâce à des jeux concours totalement gratuits. Ce sont souvent les même qui gagnent car ils participent à tous ces jeux concours. <b>Pourquoi pas vous ?</b><br />
				Vous n'y croyez pas ? Regardez cette <a href="http://www.konkours.com/temoignages.html" target="_blank"><b>liste de témoignages</b></a> ou encore <a href="http://www.youtube.com/watch?v=25_VGTDxIH8" target="_blank"><b>cette vidéo</b></a>.</p></blockquote>
				<p id="description">Voici <b><?php echo $contestCount; ?></b> jeux concours à participation 100% gratuite et rapide.</p>
			</div>
			
<?php echo $HTMLList; ?>
			
			<div id="facebook_share_box">
				<h3>Pénible de remplir ses coordonnées à chaque fois non ? ... Il existe une solution 100% gratuite !</h3>
				<div class="contest_content_left">
					<div class="contest_visuals">
						<!-- Cadeaux 1 -->
						<div class="contest_prize_box">
							<h4 class="contest_prize_title">Un voyage avec 10 amis</h4>
							<div class="contest_prize_img">
								<img src="http://www.konkours.com/images/9000/8952/8952_128.jpg" width="128" height="128" alt="" />
							</div>
							<p class="contest_prize_description">43 010 €</p>
						</div>

						<!-- Cadeaux 2 -->
						<div class="contest_prize_box">
							<h4 class="contest_prize_title">Un an d'essence</h4>
							<div class="contest_prize_img">
								<img src="http://www.konkours.com/images/8900/8868/8868_128.jpg" width="128" height="128" alt="" />
							</div>
							<p class="contest_prize_description">2000 €</p>
						</div>

						<!-- Cadeaux 3 -->
						<div class="contest_prize_box">
							<h4 class="contest_prize_title">Un Ipad Apple</h4>
							<div class="contest_prize_img">
								<img src="http://www.konkours.com/images/10200/10182/10182_128.jpg" width="128" height="128" alt="" />
							</div>
							<p class="contest_prize_description">499 €</p>
						</div>
						<div class="float_breaker"></div>
					</div>
				</div>
				<div class="contest_content_right">
					<div class="contest_content_right_top" style="height:80px;">
						<div id="hint_box">
							<p><font color="red"><b>IMPORTANT : En plus de remplir les formulaires, ce logiciel 100% gratuit et sans publicité fournit une liste de plus de plus de 300 concours ! <br /><a href="http://www.youtube.com/watch?v=25_VGTDxIH8" target="_blank">Vous n'y croyez pas ? Cliquez ICI pour la vidéo du passage à la RTBF !</a></font></p>

						</div>
					</div>

					<div class="contest_content_right_bottom">
						<div class="contest_play_button_box">
							<p class="button_text"><a href="http://www.konkours.com/index-administrateur-<?php echo ( strstr($_SERVER['HTTP_HOST'], 'france') ) ? 'jcfr' : 'jcbe'; ?>.html" title="découvrir LA solution">Cliquez ICI pour découvrir LA solution</a></p>
						</div>
					</div>
					
					<!--<div class="contest_content_right_bottom">
						<div class="contest_play_button_box" id="facebook">
							<p class="button_text">
								<span onclick="javascript:replace_facebook();"><a name="fb_share" type="icon_link" share_url="http://www.jeuxconcours.be/jeuxconcours.php" href="http://www.facebook.com/sharer.php">Cliquez ICI pour partager sur FACEBOOK</a></span>
								<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
							</p>
						</div>
					</div>-->
				</div>
				<div class="float_breaker"></div>
			</div>
			
			<div id="document_footer">
				<!--p>Stigmatix &copy; 2010</p-->
			</div>
		</div>
	</body>
</html>
