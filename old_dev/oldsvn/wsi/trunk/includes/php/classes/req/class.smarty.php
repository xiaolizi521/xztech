<?php

    require_once("Smarty.class.php");

    class Template extends Smarty {

        function __construct() {

            $this->Smarty();

            $this->template_dir=SMARTY_TEMPLATE_PATH;
            $this->compile_dir=SMARTY_COMPILE_PATH;
            $this->config_dir=SMARTY_CONFIG_PATH;
            $this->cache_dir=SMARTY_CACHE_PATH;

            $this->caching=TRUE;

            $this->cache_lifetime=300;
        }

    }

?>
