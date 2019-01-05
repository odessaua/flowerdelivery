// Check node
;(function($) {
    $.fn.checkNode = function(id) {
        $(this).bind('loaded.jstree', function () {
            $(this).jstree('checkbox').check_node('#StoreDiscountCategoryTreeNode_' + id);
        });
    };
})(jQuery);