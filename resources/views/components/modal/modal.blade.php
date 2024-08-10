<div class="modal fade" id="{{ $name }}Modal" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable {{ $ukuran }}">
        <div class="modal-content">

            <div class="modal-header" id="{{ $name }}Header">
                <div id="{{ $name }}Judul"></div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{ $body }}
            </div>
            @if (isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
