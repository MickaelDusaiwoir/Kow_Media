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
                <div class="row-fluid">
                    <div id="connexion" class="span12">                                             
                        <?php
                            if ( !$this->session->userdata('Connected') )
                            {
                                //echo anchor('admin/connect', 'Se connecter', array('title' => 'Se connecter'));

                                echo form_open('admin/connect', array('id' => 'login', 'class' => 'form-inline span9')); ?>
                                    <label for="username"  >Nom d'utilisateur</label>
                                    <input type="text" name="username" id="username" placeholder="Ex: Rambo" value="<?php echo set_value('username'); ?>" />
                                    <?= form_error('username'); ?>
                                    <label for="password" >Mot de passe</label>
                                    <input type="password" name="password" id="password" />
                                    <?= form_error('password'); ?>
                                    <input type="submit" value="Se connecter" class="btn btn-primary span2" />
                                <?php echo form_close(); 
                            }                     
                            else
                            { 
                                echo '<ul class="nav nav-pills span3">';
                                echo '<li>'.anchor('admin/addContestView', 'Ajouter un concours', array('title' => 'Ajouter un concours')).'</li>'; 
                                echo '<li>'.anchor('admin/disconnect', 'Se d&eacute;connecter', array('title' => 'Se d&eacute;connecter'));
                                echo '</ul>';
                            }                     
                        ?>                        
                    </div>
                </div>
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