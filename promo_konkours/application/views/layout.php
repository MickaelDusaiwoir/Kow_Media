<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $titre; ?></title>
        <!--[if lt IE 10]>
            <script src="<?= base_url() . JS_DIR ?>html5shiv.js"></script>
            <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style_ie7.css" media="screen" />
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style.css" media="screen" id="design_css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <LINK REL="icon" HREF="<?= base_url() ?>favicon.ico" TYPE="image/x-icon">
        <LINK REL="shortcut icon" HREF="<?= base_url() ?>favicon.ico" TYPE="image/x-icon"> 
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
        <script>
            // Tableau servant à récupère les paramètres. 
            var url_stats = '<?= base_url()."web/ajax/stats.php" ?>';

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
    </body>
</html>