<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $titre; ?></title>
        <!--[if lt IE 10]>
            <script src="<?= base_url() . JS_DIR ?>html5shiv.js"></script>
            <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style_ie7.css" media="screen" />
        <![endif]-->
        
        <?php if ( isset($css) && $css == 'index_v2' ) : ?>

            <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>v2_style.css" media="screen" id="v2_design_css" />
        <?php elseif ( isset($css) && $css == 'index_v3' ) : ?>
            <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>v3_style.css" media="screen" id="v3_design_css" />
        <?php else : ?> 

            <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style.css" media="screen" id="design_css" />
        <?php endif; ?>


	    <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>modal.css" media="screen" id="modal_css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <LINK REL="icon" HREF="<?= base_url() ?>favicon.ico" TYPE="image/x-icon">
        <LINK REL="shortcut icon" HREF="<?= base_url() ?>favicon.ico" TYPE="image/x-icon"> 


        <?php if ( isset($param) && $param['redi'] == 0 ) : ?>
        <!-- Google Analytics Content Experiment code -->
        <script>function utmx_section(){}function utmx(){}(function(){var
        k='71748295-0',d=document,l=d.location,c=d.cookie;
        if(l.search.indexOf('utm_expid='+k)>0)return;
        function f(n){if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.
        indexOf(';',i);return escape(c.substring(i+n.length+1,j<0?c.
        length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;d.write(
        '<sc'+'ript src="'+'http'+(l.protocol=='https:'?'s://ssl':
        '://www')+'.google-analytics.com/ga_exp.js?'+'utmxkey='+k+
        '&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='+new Date().
        valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
        '" type="text/javascript" charset="utf-8"><\/sc'+'ript>')})();
        </script><script>utmx('url','A/B');</script>
        <!-- End of Google Analytics Content Experiment code -->
        <?php endif; ?>


    </head>
    <body>

        <div id="wrap"<?php if ( $this->session->userdata('Connected') ) echo ' class="connected"'; ?>>                                                        
            <?php
                if ( $this->session->userdata('Connected') )
                {
                    $admin_navBar  = '<div id="connexion">';
                    $admin_navBar .= '<ul>';
                    $admin_navBar .= '<li>'.anchor('admin/afficher', 'Accueil', array('title' => 'Retourner sur la page d\'accueil', 'class' => 'icon-home')).'</li>'; 
                    $admin_navBar .= '<li>'.anchor('admin/addContestView', 'Ajoutez un concours', array('title' => 'Ajouter un concours', 'class' => 'icon-doc-new')).'</li>';
                    $admin_navBar .= '<li>'.anchor('admin/stats', 'Statistiques', array('title' => 'Voir les statistiques', 'class' => 'icon-chart-bar')).'</li>';
                    $admin_navBar .= '<li>'.anchor('admin/disconnect', 'Se d&eacute;connecter', array('title' => 'Se d&eacute;connecter', 'class' => 'icon-logout'));
                    $admin_navBar .= '</ul>';
                    $admin_navBar .= '</div>';
                    echo($admin_navBar);
                }                     
            ?>                        
                
            <h1 id="no_show"><?php echo $titre; ?></h1>
                
            <?= $vue; ?>

        </div>
        <script src="<?= base_url() . JS_DIR?>jquery.js"></script>
        <script src="<?= base_url() . JS_DIR?>jquery.ui.js"></script>
        <script src="<?= base_url() . JS_DIR?>bootstrap.js"></script>
        <script src="<?= base_url() . JS_DIR?>cookie.js"></script>
        <script>
            // Tableau servant à récupère les paramètres. 
            var url_stats = '<?= base_url()."web/ajax/stats.php" ?>',
                url_referer = '<?= base_url()."web/ajax/referer.php" ?>';

            var settings = new Array();

            <?php 
                if ( isset($param) ) :
                    foreach ($param as $key => $value) :
            ?>
                        settings[<?php echo "'".$key."'"; ?>] = <?= $value ?>;
            <?php      
                    endforeach;
                endif;
            ?>

        </script>
        <?php if ( isset($type) == 'stast' ) : ?>
            <script src="<?= base_url() . JS_DIR?>highcharts.js"></script>
            <script src="<?= base_url() . JS_DIR?>exporting.js"></script>
        <?php endif; ?>
        <script src="<?= base_url() . JS_DIR?>script.js"></script>
        <script type="text/javascript">
           var _gaq = _gaq || [];
           _gaq.push(['_setAccount', 'UA-7988677-8']);
           _gaq.push(['_trackPageview']);

           (function() {
             var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
             ga.src = ('https:' == document.location.protocol ? 'https://ssl' :'http://www') + '.google-analytics.com/ga.js';
             var s = document.getElementsByTagName('script')[0]; 
             s.parentNode.insertBefore(ga, s);
           })();

        </script>
    </body>
</html>