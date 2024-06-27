$(".dropdown-menu a").click(
    function () {
        var selText = $(this).text();
        $(this).parents('.dropdown').find('.dropdown-toggle').html('Order by: ' + selText);
    }
);


$('.sidebar-item').click(
    function () {
        // Remove "active" class from all list items
        $('.sidebar-item').removeClass('active');
        // Add "active" class to the clicked list item
        $(this).addClass('active');
    }
);

$("textarea").each(
    function () {
        this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
    }
).on(
    "input", function () {
            this.style.height = 0;
            this.style.height = (this.scrollHeight) + "px";
    }
);


$('a, button').on(
    'click', function () {
        var sidebarScrollPos = $('#sidebar').scrollTop();
        var mainScrollPos = $(window).scrollTop();
        localStorage.setItem('sidebarScrollPos', sidebarScrollPos);
        localStorage.setItem('mainScrollPos', mainScrollPos);
    }
);

$('#sidebar').scrollTop(localStorage.getItem('sidebarScrollPos'));
$(window).scrollTop(localStorage.getItem('mainScrollPos'));




// prevent enter from submitting the assign users form due to a bug
$('#form_username').keydown(
    function (e) {
        if (e.which == 13) {
            return false;
        } 
    }
);


$('.user-unassign').on(
    'click', function () {
        $('#assign_toDelete').val($(this).attr("data"));
        $('#assignForm').submit();
    }
);
$('#assign_toDelete').val("admin");

$('.status-button').on(
    'click', function () {
        $('#status_value').val($(this).attr("data"));
        $('#statusForm').submit();
    }
);

$("a[rel~='keep-params']").click(function(e) {
    e.preventDefault();

    var params = window.location.search;
    params = params.replace('confirm=1', '');
    var dest = $(this).attr('href') + params;

    // in my experience, a short timeout has helped overcome browser bugs
    window.setTimeout(function() {
        window.location.href = dest;
    }, 100);
});