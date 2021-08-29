/* eslint-disable no-undef */
(function ($) {
    'use strict';

    /**
     * Wednesday, February 20, 2019 - 12:12:21 PM
     * https://webhd.vn
     */
    $(function () {

        var login = $("#login");
        login.find('.forgetmenot').remove();
        login.find('#backtoblog').remove();
        login.find('#nav').remove();
        login.find('.privacy-policy-page-link').remove();

        // plugin
        var rank_math_dashboard_widget = $("#rank_math_dashboard_widget");
        rank_math_dashboard_widget.find('.rank-math-blog-title').remove();
        rank_math_dashboard_widget.find('.rank-math-blog-post').remove();

        var toplevel_page_mlang = $("#toplevel_page_mlang");
        toplevel_page_mlang.find('a[href="admin.php?page=mlang_wizard"]').closest('li').remove();

        var acf_textarea = $(".acf-editor-wrap.html-active").find('textarea.wp-editor-area');
        acf_textarea.removeAttr("style");

        var createuser = $("#createuser");
        createuser.find("#send_user_notification").removeAttr("checked").attr("disabled", true);

        $("tr[data-slug=\"duplicate-page\"]").find(".row-actions>span + span:not(':last-child')").remove();
        $("#element-pack-notice-id-license-issue").remove();
        $("#element-pack-notice-id-license-error").remove();
    });
})(jQuery);
