<div id="file-action" class="d-flex justify-content-between align-items-center mt-4 w-100 hiding d-none">
    <div class="card w-100">
        <div class="card-header text-truncate">
            <div class="text-truncate">
                <b class="me-1">Actions:</b>
                <span>{{ last(explode('/', $path)) }}</span>
            </div>
        </div>
        <ul class="list-group list-group-flush">
            <li id="download-file" class="list-group-item action-item d-flex align-items-center">
                <form method="POST" action="{{ route('download', ['path' => $path]) }}">
                    @csrf
                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2 opacity-50" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.2 15c.7-1.2 1-2.5.7-3.9-.6-2-2.4-3.5-4.4-3.5h-1.2c-.7-3-3.2-5.2-6.2-5.6-3-.3-5.9 1.3-7.3 4-1.2 2.5-1 6.5.5 8.8M12 19.8V12M16 17l-4 4-4-4"/></svg>
                </form>
                <span>Download</span>
            </li>
            <li class="list-group-item action-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#rename-file-form">
                <svg xmlns="http://www.w3.org/2000/svg" class="me-2 opacity-50" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
                <span class="me-2">Rename</span>
            </li>
            <li class="list-group-item action-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#delete-modal">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2 opacity-50" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    <span class="me-2">Delete</span>
                </div>
            </li>
        </ul>
    </div>
</div>
