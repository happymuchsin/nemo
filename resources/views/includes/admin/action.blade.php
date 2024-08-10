@if (isset($cetak))
    <a href="#" class="text-center" title="Print" onclick="cetak('{!! $cetak !!}')">
        <i class="fa fa-print text-warning mr-3"></i>
    </a>
@endif

@if (isset($copyto))
    <a href="#" class="text-center" title="Copy" onclick="copyto('{!! $copyto !!}')">
        <i class="fa fa-copy text-success mr-3"></i>
    </a>
@endif

@if (isset($detail))
    <a href="#" class="text-center" title="Detail" onclick="detail('{!! $detail !!}')">
        <i class="fa fa-file-circle-info mr-3"></i>
    </a>
@endif

@if (isset($reset))
    <a href="#" class="text-center" title="Reset" onclick="reset('{!! $reset !!}')">
        <i class="fa fa-rotate text-warning mr-3"></i>
    </a>
@endif

@if (isset($edit))
    <a href="#" class="text-center" title="Edit" onclick="edit('{!! $edit !!}')">
        <i class="fa fa-edit text-info mr-3"></i>
    </a>
@endif

@if (isset($sync))
    <a href="#" class="text-center" title="Sync" onclick="single_sync('{!! $sync !!}')">
        <i class="fa fa-rotate text-warning mr-3"></i>
    </a>
@endif

@if (isset($hapus))
    <a href="#" class="text-center" title="Delete" onclick="hapus('{!! $hapus !!}')">
        <i class="fa fa-trash-alt text-danger mr-3"></i>
    </a>
@endif

@if (isset($editDetail))
    <a href="#" class="text-center" title="Edit" onclick="editDetail('{!! $editDetail !!}')">
        <i class="fa fa-edit text-info mr-3"></i>
    </a>
@endif

@if (isset($saveDetail))
    <a href="#" class="text-center" title="Save" onclick="saveDetail('{!! $saveDetail !!}')">
        <i class="fa fa-save text-warning mr-3"></i>
    </a>
@endif

@if (isset($hapusDetail))
    <a href="#" class="text-center" title="Delete" onclick="hapusDetail('{!! $hapusDetail !!}')">
        <i class="fa fa-trash-alt text-danger mr-3"></i>
    </a>
@endif
