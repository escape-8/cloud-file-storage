<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm delete</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-truncate">Are you sure you want to delete '{{ last(explode('/', $path)) }}' ?</p>
                <form id="delete-file-form" method="POST" action="{{ route('delete', ['path' => $path]) }}">
                    @method('DELETE')
                    @csrf
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="delete-file" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#delete-modal">Delete</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
