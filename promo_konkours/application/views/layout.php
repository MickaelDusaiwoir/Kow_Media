<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $titre; ?></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url() . CSS_DIR ?>style.css" media="screen" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0">
    </head>
    <body>
        <div>
            <?php
                if ( !$this->session->userdata('Connected') )
                    echo anchor('admin/connect', 'Se connecter', array('title' => 'Se connecter')); 
                else
                    echo anchor('admin/disconnect', 'Se d&eacute;connecter', array('title' => 'Se d&eacute;connecter')); 
            ?>
        </div>
        <div id="container">
            
            <h1 id="no_show"><?php echo $titre; ?></h1>
            
            <?php echo $vue; ?>
            
        </div>
        <script src="<?= base_url() . JS_DIR?>jquery.js"></script>
        <script src="<?= base_url() . JS_DIR?>script.js"></script>
    </body>
</html>