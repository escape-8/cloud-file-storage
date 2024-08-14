@component('layouts.one-field-modal', [
    'formModalName' => 'rename-file-form',
    'modalTitle' => 'Rename',
    'method' => 'PATCH',
    'formName' => 'rename-file',
    'action' => route('rename-file', ['path' => $path]),
    'fieldLabel' => 'New Name',
    'submitButtonName' => 'Rename',
    'submitButtonId' => 'rename',
    ])
@endcomponent
