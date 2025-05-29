@extends('layouts.user', ['page' => $page, 'sidebar' => true])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="''">
        <x-slot:body>
            <x-filter.user-filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                </x-slot:thead>
                <x-slot:tfoot>
                </x-slot:tfoot>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var page = '',
            table = null;
        $(document).ready(function() {
            setSidebar('report_daily');
        })

        function setSidebar(id) {
            $('.user_report').removeClass('active');
            $('#' + id).addClass('active');
            page = id;

            var judul = '';
            if (page.replace('report_', '').trim() == 'daily') {
                judul = 'Daily';
            } else if (page.replace('report_', '').trim() == 'weekly') {
                judul = 'Weekly';
            } else if (page.replace('report_', '').trim() == 'monthly') {
                judul = 'Monthly';
            } else if (page.replace('report_', '').trim() == 'quarterly') {
                judul = 'Quarterly';
            } else if (page.replace('report_', '').trim() == 'half') {
                judul = 'Half Yearly';
            } else if (page.replace('report_', '').trim() == 'yearly') {
                judul = 'Yearly';
            } else {
                judul = '';
            }
            $('.card-title').html('<i class="text-info fas fa-chalkboard"></i> ' + judul);

            setFilter(id);

            setTable(id);
        }

        function setFilter(id) {
            $('#filterMe').html('');
            if (id == 'report_daily') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'date'" :label="'Date'" :id="'filter_date'" :colom="'col-sm-auto'" />`
                );

                $('#filter_date').val("{{ date('Y-m-d') }}")
            } else if (id == 'report_weekly') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'week'" :label="'Week'" :id="'filter_week'" :colom="'col-sm-auto'" />`
                );

                $('#filter_week').val("{{ date('Y') . '-W' . date('W') }}")
            } else if (id == 'report_monthly') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'month'" :label="'Month'" :id="'filter_month'" :colom="'col-sm-auto'" />`
                );

                $('#filter_month').val("{{ date('Y-m') }}")
            } else if (id == 'report_quarterly') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'select'" :label="'Quarter'" :id="'filter_quarter'" :colom="'col-sm-auto'" :alloption="false">
                        <x-slot:option>
                            <option value="Q1">Q1 (January, February, March)</option>
                            <option value="Q2">Q2 (April, May, June)</option>
                            <option value="Q3">Q3 (July, August, September)</option>
                            <option value="Q4">Q4 (October, Novemeber, December)</option>
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'number'" :label="'Year'" :id="'filter_year'" :colom="'col-sm-auto'" />
                    `
                );

                $('#filter_year').val("{{ date('Y') }}")
                $('#filter_quarter').val("{{ $quarter }}").trigger('change');
            } else if (id == 'report_half') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'select'" :label="'Half Year'" :id="'filter_half'" :colom="'col-sm-auto'" :alloption="false">
                        <x-slot:option>
                            <option value="H1">Semester 1 (January - June)</option>
                            <option value="H2">Semester 2 (July - December)</option>
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'number'" :label="'Year'" :id="'filter_year'" :colom="'col-sm-auto'" />
                    `
                );

                $('#filter_year').val("{{ date('Y') }}")
                $('#filter_half').val("{{ $half }}").trigger('change');
            } else if (id == 'report_yearly') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'number'" :label="'Year'" :id="'filter_year'" :colom="'col-sm-auto'" />`
                );

                $('#filter_year').val("{{ date('Y') }}")
            }
            if ($('#filterMe').html() != '') {
                $('#filterMe').append(`
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'"
                            :name="'SEARCH'" />
                    </div>`);
            }
        }

        function setTable(id) {
            if ($.fn.DataTable.isDataTable("#table")) {
                $('#table').html('');
                $('#table').DataTable().clear().destroy();
            }
            $('#tableHead').html('');
            $('#tableFoot').html('');
            if (id == 'report_daily') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Time</th>
                            <th>Line</th>
                            <th>User</th>
                            <th>Buyer</th>
                            <th>Style</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Remark</th>
                        </tr>
                        `)
                    $('#tableFoot').html(`
                        <tr style="display:none">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        `);
                    table = initDataTable('table', '', '', '', {
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Daily ' + $('#filter_date').val(),
                        }, ],
                        ajax: {
                            url: "{{ route('user.report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_date = $('#filter_date').val();
                            },
                        },
                        columns: [{
                                data: 'time'
                            },
                            {
                                data: 'line'
                            },
                            {
                                data: 'user'
                            },
                            {
                                data: 'buyer'
                            },
                            {
                                data: 'style'
                            },
                            {
                                data: 'brand'
                            },
                            {
                                data: 'tipe'
                            },
                            {
                                data: 'size'
                            },
                            {
                                data: 'remark'
                            },
                        ],
                        paging: false,
                    });
                }, 500);
            } else if (id == 'report_weekly') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Date</th>
                            @foreach ($master_status as $d)
                            <th>{{ $d->name }}</th>
                            @endforeach
                            <th>Last Stock</th>
                        </tr>
                    `)
                    $('#tableFoot').html(`
                        <tr>
                            <th>Total</th>
                            @foreach ($master_status as $d)
                            <th></th>
                            @endforeach
                            <th></th>
                        </tr>
                    `)
                    table = initDataTable('table', '', '', '', {
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Weekly ' + $('#filter_week').val(),
                        }, ],
                        ajax: {
                            url: "{{ route('user.report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_week = $('#filter_week').val();
                            },
                        },
                        columns: [{
                                data: 'date'
                            },
                            @foreach ($master_status as $d)
                                {
                                    data: '{{ $d->kolom }}'
                                },
                            @endforeach {
                                data: 'stock'
                            },
                        ],
                        paging: false,
                        footerCallback: function(tfoot, data) {
                            var api = this.api();
                            var d = "{{ count($master_status) }}";
                            var f = 0;
                            for (var x = 1; x <= +d + +1; x++) {
                                f++;
                                $(api.column(x).footer()).html(api
                                    .column(x)
                                    .data()
                                    .reduce(function(a, b) {
                                        return +a + +b;
                                    }, 0));
                            }
                        }
                    });
                }, 500);
            } else if (id == 'report_monthly') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Date</th>
                            @foreach ($master_status as $d)
                            <th>{{ $d->name }}</th>
                            @endforeach
                            <th>Last Stock</th>
                        </tr>
                    `)
                    $('#tableFoot').html(`
                        <tr>
                            <th>Total</th>
                            @foreach ($master_status as $d)
                            <th></th>
                            @endforeach
                            <th></th>
                        </tr>
                    `)
                    table = initDataTable('table', '', '', '', {
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Monthly ' + $('#filter_month').val(),
                        }, ],
                        ajax: {
                            url: "{{ route('user.report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_month = $('#filter_month').val();
                            },
                        },
                        columns: [{
                                data: 'date'
                            },
                            @foreach ($master_status as $d)
                                {
                                    data: '{{ $d->kolom }}'
                                },
                            @endforeach {
                                data: 'stock'
                            },
                        ],
                        paging: false,
                        footerCallback: function(tfoot, data) {
                            var api = this.api();
                            var d = "{{ count($master_status) }}";
                            var f = 0;
                            for (var x = 1; x <= +d + +1; x++) {
                                f++;
                                $(api.column(x).footer()).html(api
                                    .column(x)
                                    .data()
                                    .reduce(function(a, b) {
                                        return +a + +b;
                                    }, 0));
                            }
                        }
                    });
                }, 500);
            } else if (id == 'report_quarterly') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Month</th>
                            @foreach ($master_status as $d)
                            <th>{{ $d->name }}</th>
                            @endforeach
                            <th>Last Stock</th>
                        </tr>
                    `)
                    $('#tableFoot').html(`
                        <tr>
                            <th>Total</th>
                            @foreach ($master_status as $d)
                            <th></th>
                            @endforeach
                            <th></th>
                        </tr>
                    `)
                    table = initDataTable('table', '', '', '', {
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Quarterly ' + $('#filter_quarter').val() + ' ' + $(
                                '#filter_year').val(),
                        }, ],
                        ajax: {
                            url: "{{ route('user.report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_quarter = $('#filter_quarter').val();
                                d.filter_year = $('#filter_year').val();
                            },
                        },
                        columns: [{
                                data: 'date'
                            },
                            @foreach ($master_status as $d)
                                {
                                    data: '{{ $d->kolom }}'
                                },
                            @endforeach {
                                data: 'stock'
                            },
                        ],
                        paging: false,
                        footerCallback: function(tfoot, data) {
                            var api = this.api();
                            var d = "{{ count($master_status) }}";
                            var f = 0;
                            for (var x = 1; x <= +d + +1; x++) {
                                f++;
                                $(api.column(x).footer()).html(api
                                    .column(x)
                                    .data()
                                    .reduce(function(a, b) {
                                        return +a + +b;
                                    }, 0));
                            }
                        }
                    });
                }, 500);
            } else if (id == 'report_half') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Month</th>
                            @foreach ($master_status as $d)
                            <th>{{ $d->name }}</th>
                            @endforeach
                            <th>Last Stock</th>
                        </tr>
                    `)
                    $('#tableFoot').html(`
                        <tr>
                            <th>Total</th>
                            @foreach ($master_status as $d)
                            <th></th>
                            @endforeach
                            <th></th>
                        </tr>
                    `)
                    table = initDataTable('table', '', '', '', {
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Half Yearly ' + $('#filter_half').val() + ' ' + $(
                                '#filter_year').val(),
                        }, ],
                        ajax: {
                            url: "{{ route('user.report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_half = $('#filter_half').val();
                                d.filter_year = $('#filter_year').val();
                            },
                        },
                        columns: [{
                                data: 'date'
                            },
                            @foreach ($master_status as $d)
                                {
                                    data: '{{ $d->kolom }}'
                                },
                            @endforeach {
                                data: 'stock'
                            },
                        ],
                        paging: false,
                        footerCallback: function(tfoot, data) {
                            var api = this.api();
                            var d = "{{ count($master_status) }}";
                            var f = 0;
                            for (var x = 1; x <= +d + +1; x++) {
                                f++;
                                $(api.column(x).footer()).html(api
                                    .column(x)
                                    .data()
                                    .reduce(function(a, b) {
                                        return +a + +b;
                                    }, 0));
                            }
                        }
                    });
                }, 500);
            } else if (id == 'report_yearly') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Month</th>
                            @foreach ($master_status as $d)
                            <th>{{ $d->name }}</th>
                            @endforeach
                            <th>Last Stock</th>
                        </tr>
                    `)
                    $('#tableFoot').html(`
                        <tr>
                            <th>Total</th>
                            @foreach ($master_status as $d)
                            <th></th>
                            @endforeach
                            <th></th>
                        </tr>
                    `)
                    table = initDataTable('table', '', '', '', {
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Yearly ' + $('#filter_year').val(),
                        }, ],
                        ajax: {
                            url: "{{ route('user.report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_year = $('#filter_year').val();
                            },
                        },
                        columns: [{
                                data: 'date'
                            },
                            @foreach ($master_status as $d)
                                {
                                    data: '{{ $d->kolom }}'
                                },
                            @endforeach {
                                data: 'stock'
                            },
                        ],
                        paging: false,
                        footerCallback: function(tfoot, data) {
                            var api = this.api();
                            var d = "{{ count($master_status) }}";
                            var f = 0;
                            for (var x = 1; x <= +d + +1; x++) {
                                f++;
                                $(api.column(x).footer()).html(api
                                    .column(x)
                                    .data()
                                    .reduce(function(a, b) {
                                        return +a + +b;
                                    }, 0));
                            }
                        }
                    });
                }, 500);
            }
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
