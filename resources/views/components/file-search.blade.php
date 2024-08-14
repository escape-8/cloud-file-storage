<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Search</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="d-flex" role="search" action="{{ route('search', ['query' => '']) }}">
                    <input id="file-search" class="form-control me-2" type="search" name="query" value="{{ $query ?? '' }}" placeholder="Search" aria-label="Search">
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <div id="search-result" class="list-group w-100 overflow-x-hidden h-75 overflow-y-auto overflow-hidden" style="max-height: 600px;">
                    @foreach($searchResult as $result)
                        <a href="#" class="list-group-item list-group-item-action text-nowrap">{{ $result }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
