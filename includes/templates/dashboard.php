<?php

if (!isset($upload_update_list_files) || !is_a($upload_update_list_files, 'IAUULF') || !class_exists('IAUULF')) {
    $upload_update_list_files = new IAUULF();
}

//Retorna ID da página selecionada
$page_id = $upload_update_list_files->upload_update_list_files_pageid();

//Retorna meta_values com arquivos cadastrados
$currentFiles = $upload_update_list_files->upload_update_list_files_current_files($page_id);

//Retorna as páginas dos fundos existentes
$pages = $upload_update_list_files->upload_update_list_files_query_pages();

?>

<main class="uulf-dashboard">

    <div class="container-fluid">

        <div class="row">

            <section class="col content">

                <div class="col-12">

                    <?php if (count($pages) > 0) : ?>

                        <h2><?php _e('Selecionar Página', 'upload_update_list_files'); ?></h2>

                        <form action="" class="form-inline" name="uulf_select_page" method="post">

                            <select name="uulf_page_id" class="form-control">

                                <?php foreach ($pages as $key => $value) : ?>

                                    <option value="<?php echo $value->ID; ?>" <?php if ($value->ID == $page_id) {
                                                                                    echo "selected='selected'";
                                                                                } ?>>
                                        <?php echo $value->post_title; ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <input type="submit" value="<?php _e('Selecionar', 'upload_update_list_files') ?>" class="button" />
                            <?php wp_nonce_field('upload_update_list_files', 'uulf_select_page'); ?>

                        </form>

                    <?php endif; ?>

                    <p>
                        <?php _e('Incluir na página shortcode para que seja exibido no local que definir:', 'upload_update_list_files'); ?> <code>[iauulf_page_list_files]</code>
                    </p>

                    <?php if (isset($page_id)) { ?>

                        <form action="" method="post" name="uulf_add_edit_files" enctype="multipart/form-data">

                            <table id="uulf-table" class="wp-list-table widefat fixed striped posts">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="manage-column"><?php _e('Esconder', 'upload_update_list_files') ?></th>
                                        <th class="manage-column"><?php _e('Nome de exibição', 'upload_update_list_files') ?></th>
                                        <th class="manage-column"><?php _e('Adicionar ou Substituir', 'upload_update_list_files') ?></th>
                                        <th class="manage-column"><?php _e('Visualização Atual', 'upload_update_list_files') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (is_array($currentFiles) && count($currentFiles) > 0):
                                        $n = 0; //contable
                                        foreach ($currentFiles as $key => $value):
                                    ?>
                                        <tr data="row-<?php echo esc_attr($n) ?>">
                                            <td>
                                                <input 
                                                type="checkbox" 
                                                name="<?php echo esc_attr( 'uulf_page_list_files[' .$n.'][hide]') ?>"
                                                class="input" 
                                                <?php $upload_update_list_files->upload_update_list_files_hide_show($value); ?>>
                                                <?php _e('Sim', 'upload_update_list_files') ?>
                                            </td>
                                            <td>
                                                <input 
                                                type="text" 
                                                name="<?php echo esc_attr( 'uulf_page_list_files[' .$n.'][name]') ?>" 
                                                value="<?php echo (key_exists('name', $value)) ? $value['name'] : _e('Download', 'upload_update_list_files'); ?>" class="input" />
                                            </td>
                                            <td>
                                                <input 
                                                type="file" 
                                                name="<?php echo esc_attr( 'uulf_page_list_files[' .$n.'][file]') ?>" 
                                                class="button" 
                                                style="padding:3px;">
                                            </td>
                                            <td>
                                                <?php echo $upload_update_list_files->upload_update_list_files_print_filename($value); ?>
                                            </td>
                                        </tr>
                                    <?php
                                        $n++; //inscrease
                                        endforeach;
                                    else: 
                                    ?>
                                        <tr data="row-0">
                                            <td>
                                                <input 
                                                type="checkbox" 
                                                name="<?php echo esc_attr( 'uulf_page_list_files[0][hide]') ?>"  
                                                class="input">
                                                <?php _e('Sim', 'upload_update_list_files') ?>
                                            </td>
                                            <td>
                                                <input 
                                                type="text" 
                                                name="<?php echo esc_attr( 'uulf_page_list_files[0][name]') ?>" 
                                                value="" 
                                                class="input" />
                                            </td>
                                            <td colspan="2">
                                                <input 
                                                type="file" 
                                                name="<?php echo esc_attr( 'uulf_page_list_files[0][file]') ?>" 
                                                class="button" 
                                                style="padding:3px;">
                                            </td>
                                        </tr>

                                    <?php endif; ?>
                                    
                                </tbody>
                            </table>

                            <p class="inline-edit-save">

                                <?php _e('Máximo permitido de 10 linhas por página', 'upload_update_list_files') ?>

                                <input type="submit" class="button-primary right" style="margin-left:10px;" value="<?php _e(esc_attr('Salvar Modificações'), 'upload_update_list_files') ?>">

                                <button id="uulf-add-more" class="button right" type="button"><?php _e('Adicionar Linha', 'upload_update_list_files') ?></button>

                                <input type="hidden" name="uulf_page_id" value="<?= esc_attr($page_id); ?>">
                                <?php wp_nonce_field('upload_update_list_files', 'uulf_save_page'); ?>
                            </p>

                        </form>

                    <?php } ?>
                </div>

            </section>

        </div><!-- row -->

    </div><!-- container -->

</main>

<?php
