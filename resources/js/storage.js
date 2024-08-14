$(document).ready(function() {
    const app = $('#app');

    app.on('click', '#download-file', function() {
        $('#download-file form').submit();
    });

});
