(function($, window, document, undefined){
    $.fn.extend({
        'joindIn_tabs': function() {
            var $tabContainer = $(this);
            $tabContainer.find('ul li a').bind('click.joindIn_tabs',function(e){
                e.preventDefault();
                var $t = $(this),$tp = $t.parent(), $r = $t.attr('rel'), $e = $('#'+$r);
                if ($e.length != 0) {
                    $tabContainer.find('.ui-tabs-panel').not('.ui-tabs-hide').addClass('ui-tabs-hide');
                    $e.removeClass('ui-tabs-hide');
                }

                $tp.siblings('.ui-tabs-selected').removeClass('ui-tabs-selected ui-state-active ui-state-focus');
                $tp.addClass('ui-tabs-selected ui-state-active ui-state-focus');
            });      
        }
    });
})(jQuery, window, document)