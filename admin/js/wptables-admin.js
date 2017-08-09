(function($) {
    'use strict';

    window.wpt_admin = {};
    window.wpt_admin.selectText = function(node) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(node);
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNodeContents(node);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        }
    }
})(jQuery);
