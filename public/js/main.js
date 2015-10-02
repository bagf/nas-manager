
function showFiles(id) {
    $('tr.js-show-files-'+ id).toggle();
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
            var wait = 300;
            var $trs = $('.js-search-results tr');
            $trs.each(function(ix, ob) {
                $(ob).delay(wait+(ix*10)).fadeOut('fast', function() {
                    $(this).remove();
                });
            });
            setTimeout(function() {
                $.post('/search', { term: val }, function (data) {
                    $('.js-search-table:hidden').show();
                    $.each(data.files, function (ix, ob) {
                        var $tr = $('<tr onclick="showFiles('+ob.id+')"></tr>');
                        $tr.append('<td>'+ob.file+'</td>');
                        $tr.append('<td>'+ob.path+'</td>');
                        $tr.append('<td>'+ob.size+'</td>');
                        $tr.hide();
                        
                        $('.js-search-results').append($tr);
                        for (var i in ob.files) {
                            var file = ob.files[i];
                            var $trFile = $('<tr class="js-show-files-'+ob.id+'" style="display:none;"></tr>');
                            $trFile.append('<td class="info">'+file.file+'</td>');
                            $trFile.append('<td class="info">'+file.path+'</td>');
                            $trFile.append('<td class="info">'+file.size+'</td>');
                            $('.js-search-results').append($trFile);
                        }
                        
                        $tr.delay(wait+(ix*10)).fadeIn('fast');
                    });
                    setTimeout(function() {
                        searchActive = false;
                    }, wait + data.count);
                }, 'json');
            }, wait + ($trs.length * 10 ));
        }
    });
    
});