<table class="table table-bordered table-striped table-hover table-sm" style="width: 100%;" id="{{ $id }}">
    <thead id="{{ $id }}Head">
        {{ $thead }}
    </thead>
    @if (isset($tfoot))
        <tfoot id="{{ $id }}Foot">
            {{ $tfoot }}
        </tfoot>
    @endif
</table>
