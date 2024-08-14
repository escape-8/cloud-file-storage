<div class="modal fade" id="drop-files" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="d-flex flex-column align-items-center w-100">
            <form id="upload-file" class="modal-content w-100 p-3 border-0" method="POST" enctype="multipart/form-data" action="{{ route('upload', ['path' => $path ?? '']) }}">
                @csrf
                <div id="dropzone" class="drop-zone w-100 border-2 border-dashed d-flex flex-column">
                    <span class="drop-zone-prompt">Drop file here or click to upload</span>
                </div>
            </form>
            <div class="w-100 mt-3 modal-content border-0">
                @include('components.list-upload-files')
            </div>
        </div>
    </div>
</div>
