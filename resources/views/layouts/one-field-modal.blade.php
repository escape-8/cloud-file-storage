<div class="modal fade" id="{{ $formModalName }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ $modalTitle }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="{{ $formName }}" method="POST" action="{{ $action }}">
                @method($method)
                @csrf
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">{{ $fieldLabel }}</span>
                        <input type="text" class="form-control" name="name" value="" placeholder="" aria-label="name" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="{{$submitButtonId}}" class="btn btn-primary">{{ $submitButtonName }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
