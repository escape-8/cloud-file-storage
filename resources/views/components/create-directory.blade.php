@component('layouts.one-field-modal', [
    'formModalName' => 'directory-form',
    'modalTitle' => 'Create Directory',
    'method' => 'POST',
    'formName' => 'create-directory',
    'action' => route('create-directory', ['path' => $path]),
    'fieldLabel' => 'Directory Name',
    'submitButtonName' => 'Add directory',
    'submitButtonId' => 'create-dir'
    ])
@endcomponent
