<?php

/**
 * Classe com as funções para admin
 * @since 0.1
 */
class IAUULF
{

    protected $upload_update_list_files_admin_class;

    function init()
    {

        /** Instanciar classe de admin */
        require_once('class-uulf-admin.php');
        $this->upload_update_list_files_admin_class = new IAUULF_Admin();

        /* Show notices */
        add_action('admin_notices', array($this->upload_update_list_files_admin_class, 'upload_update_list_files_notices'));

        //Add menu page in options
        add_action('admin_menu', array($this->upload_update_list_files_admin_class, 'upload_update_list_files_add_admin_menu'));

        //load scripts js to frontend
        add_action('admin_enqueue_scripts', array($this, 'upload_update_list_files_add_assets'));

        /* Requisições Ajax */
        add_action('wp_ajax_upload_update_list_files_query_pages', array($this, 'upload_update_list_files_query_pages'));

        //Se formulário enviado realiza as atualizações
        add_action('admin_init', array($this, 'upload_update_list_files_update_data_database'));

        //Registrando shortcodes
        add_shortcode('iauulf_page_list_files', array($this, 'upload_update_list_files_shortcode'));

        //Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'upload_update_list_files_activate_plugin'));
        register_deactivation_hook(__FILE__, array($this, 'upload_update_list_files_deactivate_plugin'));
        register_theme_directory(plugin_dir_path(__FILE__) . '/includes');

    }


    /**
     * Adicionar arquivos de scripts, css e assets em geral
     */
    function upload_update_list_files_add_assets($hook)
    {
        wp_enqueue_style("uulf-main-style", PLUGIN_IAULLF_URL . "assets/style.css", [], "0.1", 'all');
        wp_enqueue_script("uulf-main-js", PLUGIN_IAULLF_URL . "assets/main.js", "jquery", "0.1", true);
    }


    /**
     * Activate plugin
     *
     * @since  0.1
     */
    function upload_update_list_files_activate_plugin()
    {
    }

    /**
     * Desactivate plugin
     *
     * @since  0.1
     */
    function upload_update_list_files_deactivate_plugin()
    {
    }


    /**
     * Produces cleaner filenames for uploads
     *
     * @param  string $filename
     * @return string
     */
    private function upload_update_list_files_sanitize_file_name($filename)
    {
        return sanitize_file_name($filename);
    }

    /** Organiza diretório de arquivos via upload  */
    private function upload_update_list_files_removeDirectory($path)
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->upload_update_list_files_removeDirectory($file) : unlink($file);
        }
        rmdir($path);
        return;
    }

    /** Retorna Id da página quando selecionado */
    function upload_update_list_files_pageid() {
        return isset($_POST['uulf_page_id']) ? (int) filter_var($_POST['uulf_page_id'], FILTER_SANITIZE_NUMBER_INT) : null;
    }

    /* Retorna as páginas identificadas com o meta_key 'uulf-pages' */
    function upload_update_list_files_query_pages()
    {
        /** Exibir todas as páginas como opção para inserir */
        $get_pages = new WP_Query(array('posts_per_page' => -1, 'post_type' => 'page'));
        wp_reset_query();

        return $get_pages->posts;
    }

    /* Retorna os meta values de acordo com o ID selecionado */
    function upload_update_list_files_current_files($id)
    {
        //Percorre array e pega meta_data do BD
        $data = get_post_meta($id, 'uulf_page_list_files', true);
        return $data;
    }

    /* Verifica se variavel contém conteúdo ou nulo */
    function upload_update_list_files_print_filename(array $meta_value)
    {

        if (empty($meta_value)  || count($meta_value) <= 0) {
            return _e('Não cadastrado', 'upload_update_list_files');
        }

        if (array_key_exists('file', $meta_value)) {
            $name = (array_key_exists('name', $meta_value))? _x($meta_value['name'], 'singular', 'upload_update_list_files') : _x('Download', 'singular', 'upload_update_list_files');

            return "<a class='button-link' target='_blank' href='" . esc_attr($meta_value['file']) . "'>" . esc_html($name) . "</a>";
        }

        if (array_key_exists('url', $meta_value)) {
            return esc_url($meta_value['url']);
        }
    }

    /* Check se checkbox ativo ou não */
    function upload_update_list_files_hide_show(array $meta_value)
    {

        if (empty($meta_value) || count($meta_value) <= 0) {
            return;
        }

        if (array_key_exists('hide', $meta_value) && $meta_value['hide'] == 'on') {
            echo " checked='checked'";
            return;
        }
    }

    /* Adiciona ou Atualiza os dados no BD */
    function upload_update_list_files_update_data_database()
    {

        //Verifica se houve envio de http
        if (!isset($_POST['uulf_page_list_files'])) {
            return false;
        }

        //Validar o formulário de envio
        if (!wp_verify_nonce($_POST['uulf_save_page'], 'upload_update_list_files')) {
            return false;
        }

        //Se não existir ID, retorna erro
        if (is_null($page_id = $this->upload_update_list_files_pageid())) {
            return false;
        }

        //Verifica lista de arquivos enviados
        if (!isset($_POST['uulf_page_list_files']) || !is_array($page_list_files = $_POST['uulf_page_list_files'])) {
            return false;
        }

        //Pega dados da database e verifica se existe
        $getData    = get_post_meta($page_id, 'uulf_page_list_files', true);
        $meta_value = [];

        //Percorre array de arquivos enviados e adiciona os arquivos a página
        foreach ($page_list_files as $key => $context) {

            //Verifica e insere nome a exibir para arquivo
            $meta_value[$key]['name'] = (isset($context['name']))? (string) filter_var($context['name'], FILTER_SANITIZE_STRING) : 'Download';

            //Verifica e insere visibilidade de arquivo
            $meta_value[$key]['hide'] = (isset($context['hide']) && !empty($context['hide'])) ? (string) filter_var($context['hide'], FILTER_SANITIZE_STRING) : '';

            //Adiciona array a variável
            if (!is_null($context) && array_key_exists('url', $context)) {
                //Valores a serem inseridos no banco
                $meta_value[$key]['url'] = (string) filter_var($context['url'], FILTER_SANITIZE_URL);
            }
            
            //Verifica se houve envio de arquivos pela requisição
            $uulf_page_list_files = (isset($_FILES) && key_exists("uulf_page_list_files", $_FILES) && is_array($_FILES["uulf_page_list_files"]))? $_FILES["uulf_page_list_files"] : [];

            //Verificando se existem dados necessários na variavel de requisição
            if (!key_exists("name", $uulf_page_list_files) || !key_exists($key, $uulf_page_list_files["name"]) || !key_exists("file", $uulf_page_list_files["name"][$key]) ) continue;

            //Verificando se existem dados de arquivo temporario necessários na variavel de requisição
            if (!key_exists("tmp_name", $uulf_page_list_files) || !key_exists($key, $uulf_page_list_files["name"]) || !key_exists("file", $uulf_page_list_files["name"][$key]) ) continue;

            //Sanitizando nome do arquivo utilizando método nativo wordpress
            $file_name = $this->upload_update_list_files_sanitize_file_name($uulf_page_list_files["name"][$key]['file']); 

            //Atribuindo caminho de arquivo temporário
            $file_temp = $uulf_page_list_files["tmp_name"][$key]['file'];

            //Adicionar arquivo de array, verifica valor e vai proxima $key
            if (empty($file_name) || empty($file_temp)) {
                //Verificar se existe informação previamente cadastrada
                if (key_exists($key, $getData) && key_exists('file', $getData[$key])) {
                    $meta_value[$key]['file'] = $getData[$key]['file'];
                }
                continue;
            }

            //Atribuindo caminhos para upload
            $current_date = date('Y/m/d');
            $path_array = wp_upload_dir($current_date, true, true);
            $dir_path = $path_array['path'];
            $pathInsert = $path_array['url'];

            //Se diretório não existir, cria-lo
            if (!file_exists($dir_path)) {
                wp_mkdir_p($dir_path);
            }

            //Mover arquivo para diretório renomeando de forma sanitizada
            if (move_uploaded_file($file_temp, $dir_path . "/" . $file_name)) {
                @chmod($dir_path . "/" . $file_name, 0666);
                $caminhoBanco = $pathInsert . "/" . $file_name;
                $caminho = substr($caminhoBanco, 0);
                $meta_value[$key]['file'] = $caminho;
            }               

        }

        //Atualiza dados
        $this->upload_update_list_files_insert_data($page_id, 'uulf_page_list_files', $meta_value);
    }

    /* Função para atualizar os dados existentes no banco */
    private function upload_update_list_files_insert_data($id, $meta_key, $newValue)
    {

        try {
            if ($res = add_post_meta($id, $meta_key, $newValue, true)) {            
                //Adiciona novos dados
                $this->upload_update_list_files_admin->response = $this->upload_update_list_files_notice_success();
            } else {
    
                //Atualiza dados no BD
                if ($res = update_post_meta($id, $meta_key, $newValue)) {
                    $this->upload_update_list_files_admin_class->response = $this->upload_update_list_files_notice_success();
                } else {
                    $this->upload_update_list_files_admin_class->response = $this->upload_update_list_files_notice_info();
                }
            }

        } catch (\Throwable $th) {
            throw $th;
            $this->upload_update_list_files_admin->response = $this->upload_update_list_files_notice_error();
        }            

    }

    function upload_update_list_files_notice_success() {
        return  '<div class="notice notice-success is-dismissible"><p>'. PLUGIN_IAUULF_NAME .  __(': Lista de arquivos salvo com sucesso.', 'upload_update_list_files') .'</p></div>';
    }

    function upload_update_list_files_notice_info() {
        return '<div class="notice notice-info is-dismissible"><p>'. PLUGIN_IAUULF_NAME . __(': Não houve nenhuma alteração na lista de arquivos da página.', 'upload_update_list_files') .'</p></div>';
    }

    function upload_update_list_files_notice_error() {
        return '<div class="notice notice-error is-dismissible"><p>'. PLUGIN_IAUULF_NAME . __(': Houve erro ao salvar listagem de arquivos. Tente novamente.', 'upload_update_list_files') .'</p></div>';
    }


    /* Register shortcode to show list of Files in page */
    function upload_update_list_files_shortcode()
    {
        /** Atribui Id da página em templates usados pelo woocommerce, corrigindo atribuir id de posts de produtos na query */
        if (function_exists('is_shop') && is_shop()) {
            $id = get_option( 'woocommerce_shop_page_id' );
        } elseif (function_exists('is_checkout') && is_checkout() && !empty( is_wc_endpoint_url(get_option('woocommerce_checkout_order_received_endpoint')) )) {
            $id = get_option( 'woocommerce_thanks_page_id' );
        } elseif (function_exists('is_checkout') && is_checkout() && !empty( is_wc_endpoint_url(get_option('woocommerce_checkout_pay_endpoint')) )) {
            $id = get_option( 'woocommerce_checkout_page_id' );;
        } else {
            $id = get_the_ID();
        }        

        //Retorna os valores do BD se vazio para execução
        if (empty($meta = get_metadata('post', $id, 'uulf_page_list_files'))) return;

        //Inicializa o html para imprimir
        $html = '<ul class="uulf-list-files">';

        foreach ($meta[key($meta)] as $key => $value) {

            //Se for setado para esconder, pula a interação para próximo indice do array
            if (!key_exists("file", $value) || @empty($value['file']) || key_exists("hide", $value) && $value['hide'] == 'on' || key_exists("name", $value) && @empty($value['name']) ) {
                continue;
            }

            $html .= '<li>'. @$this->upload_update_list_files_print_filename($value) . '</li>';
        }

        $html .= '</ul><!-- lista-de-links -->';

        return $html;
    }
}
