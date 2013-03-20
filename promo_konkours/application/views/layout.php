<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $titre; ?></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style.css" media="screen" id="design_css" />
        <!--[if lt IE 9]>
            <script src="<?= base_url() . JS_DIR ?>html5shiv.js"></script>
        <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div id="wrap"<?php if ( $this->session->userdata('Connected') ) echo ' class="connected"'; ?>>                                                        
            <?php
                if ( $this->session->userdata('Connected') )
                {
                    $admin_navBar  = '<div id="connexion">';
                    $admin_navBar .= '<ul>';
                    $admin_navBar .= '<li>'.anchor('admin/afficher', 'Accueil', array('title' => 'Retourner sur la page d\'accueil', 'class' => 'icon-home')).'</li>'; 
                    $admin_navBar .= '<li>'.anchor('admin/addContestView', 'Ajouter un concours', array('title' => 'Ajouter un concours', 'class' => 'icon-doc-new')).'</li>'; 
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
            var settings = new Array();
            <?php 
                foreach ($param as $key => $value) :
            ?>
                settings[<?php echo "'".$key."'"; ?>] = <?= $value ?>;
            <?php      
                endforeach;
            ?>

        </script>
        <script src="<?= base_url() . JS_DIR?>script.js"></script>
        <script src="<?= base_url() . JS_DIR?>script2.js"></script>
    </body>
</html>