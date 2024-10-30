<?php 
/**
 * Classe com as funções para admin
 * @since 0.1
 */
class IAUULF_Admin
{

    protected   $upload_update_list_files;
    public      $response;

    /**
    *  Register a menu in Page Menu
    *  Registrando menu na aba de "Páginas"
    *  @since 0.1
    */
    public function upload_update_list_files_add_admin_menu() {

        add_submenu_page(
            'edit.php?post_type=page',
            _x(PLUGIN_IAUULF_NAME.' Options', '', 'upload_update_list_files'),
            _x(PLUGIN_IAUULF_NAME, '','upload_update_list_files'),
            'manage_options',
            'upload_update_list_files',
            array(
                $this, 'upload_update_list_files_options_page'
            )
        );
    }


    /**
     * Defining Settings Page
    *  Definindo a página de configurações
    *  @since 0.1
    */
    public function upload_update_list_files_options_page() {

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( _x( 'Você não tem permissão para acessar essa página.', '', 'upload_update_list_files' ) );
        }

        //Prefixo utilizado para registrar as configurações na database Wordpress
        $class_prefix = 'upload_update_list_files_';

?>
        <div class="wrap">

            <div class="configuration">
                
                <h1><?= _x(PLUGIN_IAUULF_NAME, '', 'upload_update_list_files') ?></h1>

                <?php 
                    include_once('templates/dashboard.php');
                ?>                    

                <div class="about" style="margin-top:5em;">
                    
                    <hr />

                    <h1><?= _x(PLUGIN_IAUULF_NAME, '', 'upload_update_list_files'); ?> - v<?= PLUGIN_IAUULF_VERSION ?></h1>

                    <div style="display: block;padding:10px;background-color: #deebf1;position:relative;">

                        <p style="margin-top:0px;"><?= _x('Desenvolvido por','', 'upload_update_list_files'); ?> Iran Alves [https://github.com/iranalves85], <?= _x('obrigado por usar meu plugin!', '', 'upload_update_list_files'); ?>
                        <strong><?= _x('Wordpress é amor!','', 'upload_update_list_files') ?></strong>
                        <br />
                        <span style="color: #999;">
                            <small class=""><?= _x('Procura um desenvolvedor para seu projeto?','','upload_update_list_files'); ?> <strong>iranjosealves@gmail.com</strong> | <a target="_blank" href="https://makingpie.com.br">makingpie.com.br</a></small>
                        </span>

                        </p>

                    </div>

                </div><!-- about -->
                    

            </div><!-- configuration -->

        </div><!-- wrap -->

<?php

    }

    /**
    *  Função que mostra mensagens ao submeter
    *  @since 0.1
    */
    public function upload_update_list_files_notices(){

        //Verifica se página corrente é do plugin, senão retorna
        $page = get_current_screen();
        if($page->base != 'pages_page_'.'upload_update_list_files') {
            return;
        }

        //Submete os campos para BD
        $responseData = $this->response;

        /* For PHP 5.4> */
        $empty = true;

        //String
        if(is_string($responseData) && $responseData != ''){
            $empty = false;
        }

        //Nulo
        if( is_null($responseData)){
            $empty = false;
        }
      
        //array
        if( is_array($responseData) && count($responseData) > 0){
            $empty = false;
        }

        //Show notice
        if(!$empty):
            echo $responseData;
        endif;

        
    }//upload_update_list_files_notices

    
}