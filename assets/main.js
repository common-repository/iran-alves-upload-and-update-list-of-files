jQuery(document).ready(function(){
    
    //Dom Elementos
    btn_add = jQuery("#uulf-add-more");
    table   = jQuery("#uulf-table");
    
    //Verifique se é objeto 
    if (typeof btn_add == 'object') {
        btn_add.on('click', function(){
            count = jQuery(table).find("tbody tr");
            if (count.length >= 10) return;
            row_model  = jQuery(table).find("tbody tr:last-child");
            row_number = Number(row_model.attr('data').match(/[0-9]+/)[0]) + 1;
            cloned = row_model.clone(true); //clona elemento
            cloned.attr({"data": "row-" + row_number}); //Atribuindo numero da linha
            cloned.find('input').val('').removeAttr('checked').attr('name', function(index, attr){
                return attr.replace(/[0-9]+/g, row_number);
            }); //remove valores de input
            cloned.find('td:last-child').text(''); //remove conteúdo de link

            cloned.appendTo(table); //insere no elemento tabela
        });
    }

});