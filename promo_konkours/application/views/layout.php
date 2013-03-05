<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?= $titre ?></title>
        <link rel="stylesheet" type="text/css" href="<?= site_url() . CSS_DIR?>style.css" media="screen" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0">
    </head>
    <body>
        <div id="container">
            
            <h1><?= $titre ?></h1>
            
            <?= $vue?>
            
        </div>
        <script src="<?= site_url() . JS_DIR?>jquery.js"></script>
        <script src="<?= site_url() . JS_DIR?>script.js"></script>
    </body>
</html>