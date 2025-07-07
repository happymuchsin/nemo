@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'WIP Needle'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :tipe="'select'" :label="'Period'" :id="'filter_period'" :colom="'col-sm-auto'" :alloption="false">
                        <x-slot:option>
                            <option value="range">Range Date</option>
                            {{-- <option value="daily">Daily</option> --}}
                            {{-- <option value="weekly">Weekly</option> --}}
                            {{-- <option value="monthly">Monthly</option> --}}
                            {{-- <option value="yearly">Yearly</option> --}}
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'text'" :label="'Range Date'" :id="'filter_range_date'" :colom="'col-sm-auto filter-range-date'" />
                    <x-filter.filter :tipe="'date'" :label="'Daily'" :id="'filter_daily'" :colom="'col-sm-auto filter-daily'" />
                    <x-filter.filter :tipe="'week'" :label="'Weekly'" :id="'filter_weekly'" :colom="'col-sm-auto filter-weekly'" />
                    <x-filter.filter :tipe="'month'" :label="'Month'" :id="'filter_month'" :colom="'col-sm-auto filter-month'" />
                    <x-filter.filter :tipe="'number'" :label="'Year'" :id="'filter_year'" :colom="'col-sm-auto filter-year'" />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'cari()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <div class="row">
                <div class="col-sm-10">
                    <x-layout.table :id="'tableKiri'">
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
                                <th>Date Issued</th>
                                <th>Type Needle</th>
                                <th>Size</th>
                                <th>Machine</th>
                                <th>Cumulative Days<br>After Issued</th>
                                <th>Qty</th>
                            </tr>
                        </x-slot:thead>
                    </x-layout.table>
                </div>
                <div class="col-sm-2">
                    <x-layout.table :id="'tableKanan'">
                        <x-slot:thead>
                            <tr>
                                <th>Date</th>
                                <th>WIP Needle</th>
                            </tr>
                        </x-slot:thead>
                    </x-layout.table>
                </div>
            </div>
        </x-slot:body>
    </x-layout.content>

    <script>
        var tableKiri = null,
            tableKanan = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            $('#tableKiri').addClass('nowrap');
            $('#tableKanan').addClass('nowrap');
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
                maxDate: moment(), // batas maksimal adalah hari ini
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('#filter_range_date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
            });

            setTable();
        })

        function cari() {
            setTable();
        }

        function setTable() {
            return new Promise((resolve, reject) => {
                try {
                    waitAlert();
                    const promiseKiri = new Promise((res, rej) => {
                        if ($.fn.DataTable.isDataTable("#tableKiri")) {
                            $('#tableKiri').html('');
                            $('#tableKiri').DataTable().clear().destroy();
                        }
                        setTimeout(() => {
                            tableKiri = initDataTable('tableKiri', 'toolbarKiri', '', '', {
                                ajax: {
                                    url: "{{ route('user.report.wip-needle.data') }}",
                                    data: function(d) {
                                        d.mode = 'kiri';
                                        d.filter_period = $('#filter_period').val();
                                        d.filter_daily = $('#filter_daily').val();
                                        d.filter_weekly = $('#filter_weekly').val();
                                        d.filter_month = $('#filter_month').val();
                                        d.filter_year = $('#filter_year').val();
                                        d.filter_range_date = $('#filter_range_date').val();
                                    },
                                    beforeSend: function() {

                                    },
                                    complete: function() {

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
                                        data: 'date'
                                    },
                                    {
                                        data: 'needle'
                                    },
                                    {
                                        data: 'size'
                                    },
                                    {
                                        data: 'machine'
                                    },
                                    {
                                        data: 'cum'
                                    },
                                    {
                                        data: 'qty'
                                    },
                                ],
                                order: [
                                    ['8', 'asc']
                                ],
                                paging: false,
                                rowCallback: function(row, data, index) {
                                    $('td:eq(0)', row).html(tableKiri.page.info().start + index + 1);
                                },
                                initComplete: function() {
                                    res(true);
                                },
                            });
                            $('div.toolbarKiri').html(`
                                <button class="btn btn-secondary" type="button" onclick="unduh();"><span>Excel</span></button>
                            `);
                        }, 250);
                    });

                    const promiseKanan = new Promise((res, rej) => {
                        if ($.fn.DataTable.isDataTable("#tableKanan")) {
                            $('#tableKanan').html('');
                            $('#tableKanan').DataTable().clear().destroy();
                        }
                        setTimeout(() => {
                            tableKanan = initDataTable('tableKanan', 'toolbarKanan', '', '', {
                                ajax: {
                                    url: "{{ route('user.report.wip-needle.data') }}",
                                    data: function(d) {
                                        d.mode = 'kanan';
                                        d.filter_period = $('#filter_period').val();
                                        d.filter_daily = $('#filter_daily').val();
                                        d.filter_weekly = $('#filter_weekly').val();
                                        d.filter_month = $('#filter_month').val();
                                        d.filter_year = $('#filter_year').val();
                                        d.filter_range_date = $('#filter_range_date').val();
                                    },
                                    beforeSend: function() {

                                    },
                                    complete: function() {

                                    },
                                },
                                columns: [{
                                        data: 'date'
                                    },
                                    {
                                        data: 'wip'
                                    },
                                ],
                                paging: false,
                                searching: false,
                                initComplete: function() {
                                    res(true);
                                },
                            });
                            $('div.toolbarKanan').html(`
                                <h5>Summary - WIP by Date</h5>
                            `);
                        }, 250);
                    });

                    // Menunggu kedua promise selesai
                    Promise.all([promiseKiri, promiseKanan])
                        .then((results) => {
                            resolve({
                                message: true,
                            });
                            unwaitAlert();
                        })
                        .catch((error) => {
                            unwaitAlert();
                            reject(error);
                        });
                } catch (e) {
                    unwaitAlert();
                    reject(e);
                }
            });
        }

        function unduh() {
            $.ajax({
                url: "{{ route('user.report.wip-needle.unduh', ['locale' => app()->getLocale()]) }}",
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
            setTable();
        })
    </script>
    <style>
        .toolbarKiri {
            float: left;
        }

        .toolbarKanan {
            float: left;
        }
    </style>
@endsection
