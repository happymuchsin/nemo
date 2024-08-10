@if (isset($detail))
    <a href="#" class="text-center" title="Detail" onclick="detail('{!! $detail !!}')">
        <i class="fa fa-file-circle-info mr-3"></i>
    </a>
@endif

@if (isset($sync))
    <a href="#" class="text-center" title="Sync" onclick="sync('{!! $sync !!}')">
        <i class="fa fa-rotate text-warning mr-3"></i>
    </a>
@endif

@if (isset($edit))
    <a href="#" class="text-center" title="Edit" onclick="edit('{!! $edit !!}')">
        <i class="fa fa-edit text-info mr-3"></i>
    </a>
@endif


@if (isset($hapus))
    <a href="#" class="text-center" title="Delete" onclick="hapus('{!! $hapus !!}')">
        <i class="fa fa-trash-alt text-danger mr-3"></i>
    </a>
@endif

@if (isset($trip))
    <a href="#" class="text-center action-trip" title="Trip" onclick="trip(this, '{!! $trip !!}')">
        <i class="fa fa-list text-info mr-3"></i>
    </a>
@endif

@if (isset($report))
    <a href="#" class="text-center action-report" title="Trip"
        onclick="report(this, '{!! $report !!}')">
        <i class="fa fa-book-open-cover text-info mr-3"></i>
    </a>
@endif

@if (isset($expenses))
    <a href="#" class="text-center action-expenses" title="Expenses"
        onclick="expenses(this, '{!! $expenses !!}')">
        <i class="fa fa-money-check-dollar text-warning mr-3"></i>
    </a>
@endif

@if (isset($gambar))
    <a href="#" class="text-center action-gambar" title="Images"
        onclick="data_uploaded(this, '{!! $gambar !!}', 'gambar')">
        <i class="fa fa-camera text-success mr-3"></i>
    </a>
@endif
@if (isset($berkas))
    <a href="#" class="text-center action-berkas" title="Files"
        onclick="data_uploaded(this, '{!! $berkas !!}', 'berkas')">
        <i class="fa fa-folder text-success mr-3"></i>
    </a>
@endif
@if (isset($unduh))
    <a href="#" class="text-center action-unduh" title="Download"
        onclick="data_uploaded(this, '{!! $unduh !!}', 'unduh')">
        <i class="fa fa-download text-success mr-3"></i>
    </a>
@endif
