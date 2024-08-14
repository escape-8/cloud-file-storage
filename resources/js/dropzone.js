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

    app.on('click', '#drop-files .drop-zone', function () {
        if (app.find('#directory').length) {
            app.find('#file').remove();
            app.find('#directory').remove();
            app.find('#upload-file').append(`<input type="file" id="directory" name="files[]" multiple webkitdirectory hidden />`);

            $('#directory').on('change', function (e) {
                const fileList = e.originalEvent.target.files;
                $('.drop-zone').removeClass('border-black');
                $('.drop-zone-prompt').removeClass('text-dark');
                getListOfFile();
                $.each(fileList, function (idx, file) {
                    uploadFile(file);
                });
            });

            $("#directory").click();
        }

    });

    app.on('dragenter', '#drop-files .drop-zone', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $('.drop-zone').addClass('border-black');
        $('.drop-zone-prompt').addClass('text-dark');
    });

    app.on('dragleave dragend drop', '#drop-files .drop-zone', function (e) {
        e.preventDefault();
        $('.drop-zone').removeClass('border-black');
        $('.drop-zone-prompt').removeClass('text-dark');
    });

    app.on('dragover', '#drop-files .drop-zone', function (e) {
        e.stopPropagation();
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'copy';
    });

    app.on('hidden.bs.modal', '#drop-files', function () {
        app.find('#drop-dir').attr('id', 'dropzone');
        app.find('#drop-file').attr('id', 'dropzone');
        app.find('#upload-items').empty();
        app.find('#modal-upload').addClass('d-none');
    });

    app.on('drop', '#drop-file', async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const length = e.originalEvent.dataTransfer.items.length;

        for (let i = 0; i < length; i++) {
            let entry = e.originalEvent.dataTransfer.items[i].webkitGetAsEntry();

            if (entry.isFile) {
                entry.file(f => {
                    const newFile = new File([f], entry.fullPath.substring(1), {});
                    getListOfFile();
                    uploadFile(newFile);
                });
            }

            if (entry.isDirectory) {
                $('#drop-file').append(`<span class="invalid-feedback d-inline-block drop-zone-prompt" role="alert"><strong class="text-wrap">Only files not directories</strong></span>`)
                setTimeout(function() {
                    $('.invalid-feedback').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 1500);
            }
        }
    });

    app.on('drop', '#drop-dir', async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const length = e.originalEvent.dataTransfer.items.length;

        for (let i = 0; i < length; i++) {
            let entry = e.originalEvent.dataTransfer.items[i].webkitGetAsEntry();

            if (entry.isFile) {
                $('#drop-dir').append(`<span class="invalid-feedback d-inline-block drop-zone-prompt" role="alert"><strong class="text-wrap">Only directories not files</strong></span>`)
                setTimeout(function() {
                    $('.invalid-feedback').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 1500);
            }

            if (entry.isDirectory) {
                async function readDirectory(directory) {
                    const dirReader = directory.createReader();
                    const entries = [];

                    while (true) {
                        const results = await new Promise((resolve, reject) => {
                            dirReader.readEntries(resolve, reject);
                        });

                        if (!results.length) {
                            break;
                        }

                        for (const entry of results) {
                            if (entry.isFile) {
                                entries.push(entry);
                            } else if (entry.isDirectory) {
                                entries.push(entry);
                                const subRes = await readDirectory(entry)
                                entries.push(...subRes);
                            }
                        }
                    }
                    return entries;
                }

                const entries = await readDirectory(entry);
                entries.forEach((file) => {
                    if (file.isFile) {
                        file.file(f => {
                            const newFile = new File([f], file.fullPath, {
                            });
                            getListOfFile();
                            uploadFile(newFile);
                        });
                    }
                })
            }
        }
    });

    function getListOfFile() {
        app.find('#modal-upload').removeClass('d-none');
    }

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('files[]', file);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('path', new URL(app.find('#upload-file').attr('action')).searchParams.get('path'));

        const id = Math.random().toString(36).substring(2);
        const fileName = file.webkitRelativePath ? '/' + file.webkitRelativePath : file.name

        $.ajax({
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(event) {
                    if (event.lengthComputable) {
                        const percent = event.loaded > 0 ? (event.loaded / event.total) * 100 : 100;
                        const BYTES_IN_MB = 1048576;
                        const loadedMb = (event.total / BYTES_IN_MB).toFixed(2);

                        const fileItem = app.find('#upload-items').find(`#${id}`);

                        const html = `
                            <div id="${id}" class="d-flex flex-row align-items-center bg-light w-100 rounded-3 p-3 mb-1" >
                                <div class="d-flex align-self-center me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="min-icon" fill="currentcolor" width="30px" viewBox="0 0 256 256"><g transform="translate(-13.993 -4638.241)"><g transform="translate(41.993 4638.241)"><g><path d="M137.775,74.688a10.333,10.333,0,0,1-10.366-10.243V0H25.916A25.839,25.839,0,0,0,0,25.607V230.393A25.839,25.839,0,0,0,25.916,256H177.084A25.839,25.839,0,0,0,203,230.393V74.688Z" fill="#295595"></path><path d="M83.343,74.614H149.3L73.787,0V65.158a10.275,10.275,0,0,0,9.556,9.456" transform="translate(53.697)" fill="#4a74b1"></path><path d="M118.8,112.994H34.133a2.47,2.47,0,0,0-2.481,2.451v9.806a2.473,2.473,0,0,0,2.481,2.451H118.8a2.477,2.477,0,0,0,2.481-2.451v-9.806a2.474,2.474,0,0,0-2.481-2.451" transform="translate(25.033 70.751)" fill="#fff"></path><path d="M118.8,94.244H34.133A2.47,2.47,0,0,0,31.652,96.7V106.5a2.474,2.474,0,0,0,2.481,2.451H118.8a2.477,2.477,0,0,0,2.481-2.451V96.7a2.474,2.474,0,0,0-2.481-2.452" transform="translate(25.033 58.859)" fill="#fff"></path><path d="M31.651,77.945v9.806A2.475,2.475,0,0,0,34.132,90.2H118.8a2.476,2.476,0,0,0,2.481-2.451V77.945a2.472,2.472,0,0,0-2.481-2.451H34.132a2.471,2.471,0,0,0-2.481,2.451" transform="translate(25.032 46.967)" fill="#fff"></path></g></g></g></svg>
                                </div>
                                <div class="d-flex flex-column w-100" style="max-width: 90%">
                                    <div id="header-${id}" class="d-flex flex-row align-items-center justify-content-between mb-2">
                                        <div class="d-flex w-75 flex-row align-items-center">
                                            <div class="text-truncate w-50 me-2">${fileName}</div>
                                            <span class="me-3 text-nowrap">${loadedMb} MB</span>
                                        </div>
                                        <div class="d-flex flex-row">${Math.round(percent)}%</div>
                                    </div>
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="progress w-100" style="height: 10px">
                                            <div class="progress-bar" role="progressbar" style="width: ${Math.round(percent)}%" aria-valuenow="${Math.round(percent)}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        fileItem.length ? fileItem.replaceWith(html) : app.find('#upload-items').append(html);
                    }
                }, false);
                return xhr;
            },
            url: '/api/storage/upload',
            method: 'POST',
            dataType: 'json',
            enctype: "multipart/form-data",
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                callMainPage();
                const percentDiv = $(`#header-${id} > div:last-child`);
                const newDiv = $('<div class="me-2"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">\n' +
                    '<path fill="#4caf50" d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path><path fill="#ccff90" d="M34.602,14.602L21,28.199l-5.602-5.598l-2.797,2.797L21,33.801l16.398-16.402L34.602,14.602z"></path>\n' +
                    '</svg></div>');
                percentDiv.prepend(newDiv);
            },
            error: function(error) {
                if (JSON.parse(error.responseText)) {
                    $(`#${id}`).after(`<span class="invalid-feedback d-inline-block" role="alert"><strong class="text-wrap">${JSON.parse(error.responseText).message}</strong></span>`);
                }
            }
        });
    }

    function callMainPage() {
        $.ajax({
            url: window.location.href,
            type: "GET",
            dataType: "html",
            success: function (response) {
                getPageFromResponse(response);
                window.history.replaceState({}, '', window.location.href);
            }
        });
    }

    function getPageFromResponse(response) {
        app.find('.card-body').replaceWith($(response).find('.card-body'));
    }

    app.on('dragover', '#drop-files.modal.fade', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    app.on('drop', '#drop-files.modal.fade', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });
});
