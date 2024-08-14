$(document).ready(function () {
    const app = $('#app');

    app.on('click', '#upload-files', function () {
        app.find('#directory').remove();
        app.find('#file').remove();
        app.find('#dropzone').attr('id', 'drop-file');
        app.find('#upload-file').append(`<input type="file" id="file" name="files[]" multiple hidden />`);
        app.find('#drop-file .drop-zone-prompt').text('Drop file here or click to upload');
    });

    app.on('click', '#upload-dirs', function () {
        app.find('#file').remove();
        app.find('#directory').remove();
        app.find('#dropzone').attr('id', 'drop-dir');
        app.find('#upload-file').append(`<input type="file" id="directory" name="files[]" multiple webkitdirectory hidden />`);
        app.find('#drop-dir .drop-zone-prompt').text('Drop directory here or click to upload');
    });

    app.on('click', '#drop-files .drop-zone', function () {
        if (app.find('#file').length) {
            app.find('#directory').remove();
            app.find('#file').remove();
            app.find('#upload-file').append(`<input type="file" id="file" name="files[]" multiple hidden />`);

            app.on('change', '#file', function (e) {
                const fileList = e.originalEvent.target.files;
                $('.drop-zone').removeClass('border-black');
                $('.drop-zone-prompt').removeClass('text-dark');
                getListOfFile();
                $.each(fileList, function (idx, file) {
                    uploadFile(file);
                });
            });

            $("#file").click();
        }
    });

});
