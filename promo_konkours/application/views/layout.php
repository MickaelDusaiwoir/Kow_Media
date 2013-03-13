<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $titre; ?></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style.css" media="screen" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0">
    </head>
    <body>
        <div id="wrap">
            <div class="container-fluid">
                                                        
                <?php
                    if ( $this->session->userdata('Connected') )
                    {
                        $admin_navBar  = '<div class="row-fluid"><div id="connexion" class="span12">';
                        $admin_navBar .='<ul class="nav nav-pills span4">';
                        $admin_navBar .= '<li>'.anchor('admin/afficher', 'Accueil', array('title' => 'Retourner sur la page d\'accueil')).'</li>'; 
                        $admin_navBar .= '<li>'.anchor('admin/addContestView', 'Ajouter un concours', array('title' => 'Ajouter un concours')).'</li>'; 
                        $admin_navBar .= '<li>'.anchor('admin/disconnect', 'Se d&eacute;connecter', array('title' => 'Se d&eacute;connecter'));
                        $admin_navBar .= '</ul>';
                        $admin_navBar .= '</div></div>';
                        echo($admin_navBar);
                    }                     
                ?>                        
                    
                <h1 id="no_show"><?php echo $titre; ?></h1>
                <div class="row-fluid">
                    
                    <?php echo $vue; ?>
                        
                </div>
                <div id="push"></div>
            </div>
        </div>
        <script src="<?= base_url() . JS_DIR?>jquery.min.js"></script>
        <script src="<?= base_url() . JS_DIR?>script.js"></script>
        <script src="<?= base_url() . JS_DIR?>bootstrap.min.js"></script>
    </body>
</html>