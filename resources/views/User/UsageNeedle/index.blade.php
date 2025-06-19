@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Usage Needle All Operator'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :tipe="'select'" :label="'Period'" :id="'filter_period'" :colom="'col-sm-auto'" :alloption="false">
                        <x-slot:option>
                            <option value="range">Range Date</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'text'" :label="'Range Date'" :id="'filter_range_date'" :colom="'col-sm-auto filter-range-date'" />
                    <x-filter.filter :tipe="'date'" :label="'Daily'" :id="'filter_daily'" :colom="'col-sm-auto filter-daily'" />
                    <x-filter.filter :tipe="'week'" :label="'Weekly'" :id="'filter_weekly'" :colom="'col-sm-auto filter-weekly'" />
                    <x-filter.filter :tipe="'month'" :label="'Month'" :id="'filter_month'" :colom="'col-sm-auto filter-month'" />
                    <x-filter.filter :tipe="'number'" :label="'Year'" :id="'filter_year'" :colom="'col-sm-auto filter-year'" />
                    <x-filter.filter :tipe="'select'" :label="'Status'" :id="'filter_status'" :colom="'col-sm-auto'">
                        <x-slot:option>
                            <option value="1">Deformed</option>
                            <option value="2">Routine Change</option>
                            <option value="3">Change Style or Material</option>
                            <option value="4">Broken Missing Fragment</option>
                        </x-slot:option>
                    </x-filter.filter>
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'cari()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'tableSummary'">
                <x-slot:thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Total</th>
                        <th colspan="{{ count($master_needle) }}" class="text-center">Needle Type</th>
                    </tr>
                    <tr>
                        @foreach ($master_needle as $d)
                            <th>{{ $d->tipe . ' (' . $d->size . ')' }}</th>
                        @endforeach
                    </tr>
                </x-slot:thead>
            </x-layout.table>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Username</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Division</th>
                        <th rowspan="2">Position</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Location</th>
                        <th rowspan="2">Counter Member</th>
                        <th rowspan="2">Total</th>
                        <th colspan="{{ count($master_needle) }}" class="text-center">Needle Type</th>
                    </tr>
                    <tr>
                        @foreach ($master_needle as $d)
                            <th>{{ $d->tipe . ' (' . $d->size . ')' }}</th>
                        @endforeach
                    </tr>
                </x-slot:thead>
                {{-- <x-slot:tfoot>
                    <tr>
                        <th colspan="8" class="text-center">Total</th>
                        @foreach ($master_needle as $d)
                            <th></th>
                        @endforeach
                    </tr>
                </x-slot:tfoot> --}}
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null,
            tableSummary = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            $('#tableSummary').addClass('nowrap');
            $('#table').addClass('nowrap');
            $('#filter_period').on('change', function() {
                if ($(this).val() == 'range') {
                    $('.filter-range-date').show();
                    $('.filter-daily').hide();
                    $('.filter-weekly').hide();
                    $('.filter-month').hide();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'daily') {
                    $('.filter-range-date').hide();
                    $('.filter-daily').show();
                    $('.filter-weekly').hide();
                    $('.filter-month').hide();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'weekly') {
                    $('.filter-range-date').hide();
                    $('.filter-daily').hide();
                    $('.filter-weekly').show();
                    $('.filter-month').hide();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'monthly') {
                    $('.filter-range-date').hide();
                    $('.filter-daily').hide();
                    $('.filter-weekly').hide();
                    $('.filter-month').show();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'yearly') {
                    $('.filter-range-date').hide();
                    $('.filter-daily').hide();
                    $('.filter-weekly').hide();
                    $('.filter-month').hide();
                    $('.filter-year').show();
                }
            });
            $('#filter_period').val('range').trigger('change');
            $('#filter_daily').val("{{ date('Y-m-d') }}").trigger('change');
            $('#filter_weekly').val("{{ date('Y') . '-W' . date('W') }}").trigger('change');
            $('#filter_month').val("{{ date('Y-m') }}").trigger('change');
            $('#filter_year').val("{{ date('Y') }}").trigger('change');
            $('#filter_range_date').val("{{ date('Y-m-d', strtotime('-1 month')) . ' - ' . date('Y-m-d') }}")

            $("#filter_range_date").daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('#filter_range_date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            setTimeout(() => {
                tableSummary = initDataTable('tableSummary', 'toolbarSummary', '', '', {
                    dom: '<"toolbarSummary"B>flrtip',
                    buttons: [{
                        text: 'Excel',
                        action: function(e, dt, node, config) {
                            unduh();
                        },
                    }, ],
                    ajax: {
                        url: "{{ route('user.usage-needle.data') }}",
                        data: function(d) {
                            d.mode = 'summary';
                            d.filter_period = $('#filter_period').val();
                            d.filter_status = $('#filter_status').val();
                            d.filter_daily = $('#filter_daily').val();
                            d.filter_weekly = $('#filter_weekly').val();
                            d.filter_month = $('#filter_month').val();
                            d.filter_year = $('#filter_year').val();
                            d.filter_range_date = $('#filter_range_date').val();
                        },
                    },
                    columns: [{
                            data: 'x'
                        },
                        {
                            data: 'total'
                        },
                        @foreach ($master_needle as $d)
                            {
                                data: 'x{{ $d->id }}'
                            },
                        @endforeach
                    ],
                    paging: false,
                    info: false,
                    searching: false,
                });
            }, 250);

            setTimeout(() => {
                table = initDataTable('table', '', '', '', {
                    ajax: {
                        url: "{{ route('user.usage-needle.data') }}",
                        data: function(d) {
                            d.mode = 'data';
                            d.filter_period = $('#filter_period').val();
                            d.filter_status = $('#filter_status').val();
                            d.filter_daily = $('#filter_daily').val();
                            d.filter_weekly = $('#filter_weekly').val();
                            d.filter_month = $('#filter_month').val();
                            d.filter_year = $('#filter_year').val();
                            d.filter_range_date = $('#filter_range_date').val();
                        },
                    },
                    columns: [{
                            data: null,
                            name: 'nomor',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'username'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'division'
                        },
                        {
                            data: 'position'
                        },
                        {
                            data: 'tipe'
                        },
                        {
                            data: 'location'
                        },
                        {
                            data: 'counter'
                        },
                        {
                            data: 'total'
                        },
                        @foreach ($master_needle as $d)
                            {
                                data: 'x{{ $d->id }}'
                            },
                        @endforeach
                    ],
                    paging: false,
                    rowCallback: function(row, data, index) {
                        // Ubah isi kolom pertama (index ke-0) jadi nomor urut
                        $('td:eq(0)', row).html(table.page.info().start + index + 1);
                    },
                    // footerCallback: function(tfoot, data, start, end, display) {
                    //     var api = this.api();
                    //     if (end > 0) {
                    //         for (var s = 8; s <= api.columns().count() - 1; s++) {
                    //             var x = api.column(s, {
                    //                 search: 'applied'
                    //             }).data().reduce(function(a, b) {
                    //                 return +a + +b;
                    //             }, 0);
                    //             $(api.column(s).footer()).html(x);
                    //         }
                    //     }
                    // }
                });
            }, 500);
        })

        function cari() {
            tableSummary.ajax.reload();
            table.ajax.reload();
        }

        function unduh() {
            $.ajax({
                url: "{{ route('user.usage-needle.unduh', ['locale' => app()->getLocale()]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    filter_period: $('#filter_period').val(),
                    filter_status: $('#filter_status').val(),
                    filter_daily: $('#filter_daily').val(),
                    filter_weekly: $('#filter_weekly').val(),
                    filter_month: $('#filter_month').val(),
                    filter_year: $('#filter_year').val(),
                    filter_range_date: $('#filter_range_date').val(),
                },
                beforeSend: function() {
                    waitAlert();
                },
                complete: function() {
                    // unwaitAlert();
                },
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 2) {
                            if (xhr.status == 200) {
                                xhr.responseType = "blob";
                            } else {
                                xhr.responseType = "text";
                            }
                        }
                    };
                    return xhr;
                },
                success: function(response, status, xhr) {
                    unwaitAlert();
                    var filename = "";
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) filename = matches[1].replace(
                            /['"]/g, '');
                    }
                    downloadUrl = URL.createObjectURL(response);
                    var a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
