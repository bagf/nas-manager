
function showFiles(id, item) {
    var $files = $('tr.js-show-files-'+ id);
    if ($files.length > 0) {
        $files.toggle();
        return null;
    }
    
    $.get('/files?item_id='+id, function (data) {
        $('.js-item-size[data-id='+id+']').html(data.size);
        $.each(data.files, function (ix, ob) {
            var $tr = $('<tr class="js-show-files-'+id+'"></tr>');
            $tr.append('<td class="info">'+ob.file+'</td>');
            $tr.append('<td class="info">'+ob.path+'</td>');
            $tr.append('<td class="info">'+ob.size+'</td>');
            $(item).after($tr);
        });
    }, 'json');
}

$(document).ready(function(){
    
    var searchActive = false;
    $('.js-change-category').click(function() {
        var text = $(this).find('.js-category-text').html();
        $('.js-category').html(text);
    });
    
    $('.js-search-term').keypress(function(event) {
        if (event.which === 13) {
            if (searchActive) return null;
            event.preventDefault();
            var val = $(this).val();
            var category = $('.js-category').html();
            if (category !== 'All') {
                val = category+':'+val;
            }
            if (val.length <= 0) return null;
            searchActive = true;
            var $trs = $('.js-search-results tr').remove();
            $.post('/search', { term: val }, function (data) {
                $('.js-search-table:hidden').show();
                $.each(data.files, function (ix, ob) {
                    var $tr = $('<tr onclick="showFiles('+ob.id+', this)"></tr>');
                    $tr.append('<td>'+ob.file+'</td>');
                    $tr.append('<td>'+ob.path+'</td>');
                    $tr.append('<td class="js-item-size" data-id="'+ob.id+'"> -- </td>');

                    $('.js-search-results').append($tr);
                });
                searchActive = false;
            }, 'json');
        }
    });
    
});