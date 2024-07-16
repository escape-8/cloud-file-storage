$(document).ready(function() {
    const app = $('#app');

    function callMainPage() {
        $.ajax({
            url: '/',
            type: "GET",
            dataType: "html",
            success: function (response) {
                getPageFromResponse(response);
            }
        })
    }

    function getPageFromResponse(response) {
        app.html([$(response).find('nav'), $(response).find('main')]);
    }

    function getValidationErrorInFields(response) {
        $.each(JSON.parse(response.responseText).errors, function (field, messages) {
            const fieldError = `#${field}`;
            $(fieldError).nextAll().remove();
            for (const message of messages) {
                $(fieldError).after(`<span class="invalid-feedback d-inline-block" role="alert"><strong>${message}</strong></span>`);
            }
        });
    }

    app.on('click', '#login', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/login',
            type: "POST",
            dataType: "json",
            data: {
              email: $('#email').val(),
              password: $('#password').val(),
              _token: $('input[name=_token]').val()
            },
            success: function () {
                callMainPage();
            },
            error: function (response) {
                getValidationErrorInFields(response);
            }
        })
    });

    app.on('click', '#logout-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/logout',
            type: "POST",
            dataType: "json",
            data: {
                _token: $('input[name=_token]').val()
            },
            success: function () {
                callMainPage();
            }
        })
    })

    app.on('click', '#login-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/login',
            type: "GET",
            dataType: "html",
            success: function (response) {
                getPageFromResponse(response);
            }
        })
    })

    app.on('click', '#register-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/register',
            type: "GET",
            dataType: "html",
            success: function (response) {
                getPageFromResponse(response);
            }
        })
    })

    app.on('click', '#register', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/api/register',
            type: "POST",
            dataType: "json",
            data: {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                password_confirmation: $('#password-confirm').val(),
                _token: $('input[name=_token]').val()
            },
            success: function () {
                callMainPage();
            },
            error: function (response) {
                getValidationErrorInFields(response);
            }
        })
    })
})


