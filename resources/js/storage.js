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

    app.on('click', '#rename', function(e) {
        e.preventDefault();

        $.ajax({
            url: '/api/storage/rename',
            method: "PATCH",
            type: "json",
            data: {
                path: new URL($('#delete-file-form').attr('action')).searchParams.get('path'),
                name: $('#rename-file').find('input[name=name]').val(),
                _token: $('input[name=_token]').val(),
            },
            success: function (response) {
                getPage(window.location.href);
                setTimeout(function() {
                    setFlash(successFlashMessage(response.status));
                }, 400);
                const renameForm = Bootstrap.Modal.getOrCreateInstance(document.getElementById('rename-file-form'));
                renameForm.hide();
            },
            error: function (response) {
                const toJson = JSON.parse(response.responseText);
                const input = $('#rename-file .modal-body .input-group');
                if (toJson) {
                    displayErrors(toJson, input);
                }
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

    app.on('keypress', '#file-search', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    })

    app.on('keyup', '#file-search', function (e) {

        e.preventDefault();

        $.ajax({
            url: '/api/storage/search',
            method: "GET",
            type: "json",
            data: {
                query: $('#file-search').val()
            },
            success: function (response) {
                app.find('#search-result').empty();
                $.each(response.result, function(type, files) {
                    if (type === 'files') {
                        $.each(files, function(name, link) {
                            app.find('#search-result')
                                .append(`
                                    <a href=${link} class="list-group-item list-group-item-action text-nowrap" data-bs-toggle="modal" data-bs-target="#searchModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="min-icon me-1" fill="currentcolor" width="20px" viewBox="0 0 256 256"><g transform="translate(-13.993 -4638.241)"><g transform="translate(41.993 4638.241)"><g><path d="M137.775,74.688a10.333,10.333,0,0,1-10.366-10.243V0H25.916A25.839,25.839,0,0,0,0,25.607V230.393A25.839,25.839,0,0,0,25.916,256H177.084A25.839,25.839,0,0,0,203,230.393V74.688Z" fill="#295595"></path><path d="M83.343,74.614H149.3L73.787,0V65.158a10.275,10.275,0,0,0,9.556,9.456" transform="translate(53.697)" fill="#4a74b1"></path><path d="M118.8,112.994H34.133a2.47,2.47,0,0,0-2.481,2.451v9.806a2.473,2.473,0,0,0,2.481,2.451H118.8a2.477,2.477,0,0,0,2.481-2.451v-9.806a2.474,2.474,0,0,0-2.481-2.451" transform="translate(25.033 70.751)" fill="#fff"></path><path d="M118.8,94.244H34.133A2.47,2.47,0,0,0,31.652,96.7V106.5a2.474,2.474,0,0,0,2.481,2.451H118.8a2.477,2.477,0,0,0,2.481-2.451V96.7a2.474,2.474,0,0,0-2.481-2.452" transform="translate(25.033 58.859)" fill="#fff"></path><path d="M31.651,77.945v9.806A2.475,2.475,0,0,0,34.132,90.2H118.8a2.476,2.476,0,0,0,2.481-2.451V77.945a2.472,2.472,0,0,0-2.481-2.451H34.132a2.471,2.471,0,0,0-2.481,2.451" transform="translate(25.032 46.967)" fill="#fff"></path></g></g></g></svg>
                                        ${name}</a>`);
                        });
                    } else {
                        $.each(files, function(name, link) {
                            app.find('#search-result')
                                .append(`
                                <a href=${link} class="list-group-item list-group-item-action text-nowrap" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="min-icon me-1" fill="currentcolor" width="20px" viewBox="0 0 256 256"><g transform="translate(0 22)"><g transform="translate(0 0)"><path d="M240.073,47.755A29.485,29.485,0,0,0,210.541,18.79h-81.5l-1.116-1.571A33.623,33.623,0,0,0,101.723,0H49.545A29.486,29.486,0,0,0,20.013,29.372a20.759,20.759,0,0,0,.238,3.135V45.954A29.748,29.748,0,0,0,0,74.012a13.652,13.652,0,0,0,.079,1.8L9.8,182.443A29.813,29.813,0,0,0,39.67,211H216.079a29.815,29.815,0,0,0,29.875-28.544l9.967-106.611c0-.611.079-1.236.079-1.847a29.726,29.726,0,0,0-15.927-26.244" fill="#ceb87c"></path><path d="M240.073,8.268c-.007-.407-.112-.781-.139-1.182H18.805A29.651,29.651,0,0,0,0,34.492a13.619,13.619,0,0,0,.079,1.8L9.8,142.791A29.8,29.8,0,0,0,39.67,171.314H216.079A29.808,29.808,0,0,0,245.954,142.8l9.967-106.481c0-.61.079-1.234.079-1.845A29.686,29.686,0,0,0,240.073,8.268" transform="translate(0 39.686)" fill="#e8d289"></path><path d="M234.976,8.274c-.007-.409-.112-.785-.139-1.188H13.709A30.13,30.13,0,0,0,2.844,14.545l-.092.112A28.244,28.244,0,0,0,.91,16.967H245a29.767,29.767,0,0,0-10.026-8.693" transform="translate(5.097 39.476)" fill="#b7a16a"></path></g></g></svg>
                                ${name}</a>`);
                        });
                    }
                });
            }
        });
    });

    app.on('click', '#search-result > a', function (e) {
        e.preventDefault();
        const next = $(this).attr('href');
        getPage(next);
    });

    function displayErrors(errorResponse, blockForErrors) {
        $.each(errorResponse.errors, function (field, messages) {
            for (const message of messages) {
                blockForErrors.nextAll().remove();
                blockForErrors.after(`<span class="invalid-feedback d-inline-block" role="alert"><strong>${message}</strong></span>`);
            }
        });
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
