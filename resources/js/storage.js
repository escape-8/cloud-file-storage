$(document).ready(function() {
    const app = $('#app');

    app.on('click', '#download-file', function() {
        $('#download-file form').submit();
    });

    app.on('click', '#delete-file', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/api/storage/delete',
            method: "DELETE",
            type: "json",
            data: {
                path: new URL($('#delete-file-form').attr('action')).searchParams.get('path'),
                _token: $('input[name=_token]').val(),
            },
            success: function (response) {
                getPage(window.location.href);
                setTimeout(function() {
                    setFlash(successFlashMessage(response.status));
                }, 300);
            }
        });
    });

    function getPage(nextHref) {
        $.ajax({
            url: nextHref,
            type: "GET",
            dataType: "html",
            success: function (response) {
                getPageFromResponse(response);
                window.history.replaceState({}, '', nextHref);
            }
        });
        checkScroll();
    }

    function getPageFromResponse(response) {
        $('#storage').replaceWith($(response).find('#storage'));
    }

    function checkAlertSuccess () {
        setTimeout(function() {
            $('.alert-success').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 2000);
    }

    function checkScroll() {
        setTimeout(function() {
            const breadcrumbsNav = $('.bc > nav');
            if (breadcrumbsNav && breadcrumbsNav.length > 0) {
                breadcrumbsNav.scrollLeft(breadcrumbsNav[0].scrollWidth ? breadcrumbsNav[0].scrollWidth : 0);
            }
        }, 1000);
    }

    function successFlashMessage(text) {
        return `<div class="alert alert-success align-items-center justify-content-center w-100" role="alert">${text}</div>`;
    }

    function setFlash(html) {
        $('.card-body').prepend(html);
        checkAlertSuccess();
    }
});
