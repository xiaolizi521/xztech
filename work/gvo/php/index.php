<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <title>Global Virtual Opportunities - Office Portal</title>
        <link rel="stylesheet" href="includes/css/blueprint/screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="includes/css/blueprint/print.css" type="text/css" media="print">
        <!--[if lt IE 8]>
          <link rel="stylesheet" href="includes/css/blueprint/ie.css" type="text/css" media="screen, projection">
        <![endif]-->
    </head>
    <body>
    <?php

        set_include_path(get_include_path() . PATH_SEPARATOR . "/home/gvoutil/public_html/includes");

        require_once 'includes/Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance();

        $config = new Zend_Config_Ini("includes/config/forms.ini", "change");

        $form = new Zend_Form($config->change);
        $form->setView(new Zend_View());

        echo $form;

    ?>
    </body>
</html>