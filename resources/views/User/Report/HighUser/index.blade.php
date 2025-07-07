@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'High User'">
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
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Division</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Counter</th>
                        <th>Total</th>
                        <th>Deformed</th>
                        <th>Routine Change</th>
                        <th>Change Style or Material</th>
                        <th>Broken Missing Fragment</th>
                        <th>Graph</th>
                        <th>Average Use / Days</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
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
                table = initDataTable('table', '', '', '', {
                    dom: '<"toolbar"B>flrtip',
                    buttons: [{
                        text: 'Excel',
                        action: function(e, dt, node, config) {
                            unduh();
                        },
                    }, ],
                    ajax: {
                        url: "{{ route('user.report.high-user.data') }}",
                        data: function(d) {
                            d.filter_period = $('#filter_period').val();
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
                        {
                            data: 'deformed'
                        },
                        {
                            data: 'routine'
                        },
                        {
                            data: 'change'
                        },
                        {
                            data: 'broken'
                        },
                        {
                            data: 'graph'
                        },
                        {
                            data: 'average'
                        },
                    ],
                    paging: false,
                    rowCallback: function(row, data, index) {
                        $('td:eq(0)', row).html(table.page.info().start + index + 1);
                    },
                });
            }, 250);
        })

        function unduh() {
            $.ajax({
                url: "{{ route('user.report.high-user.unduh', ['locale' => app()->getLocale()]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    filter_period: $('#filter_period').val(),
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
