@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Timing Log'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'text'" :label="'Range Date'" :id="'filter_range_date'" />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th rowspan="3">No</th>
                        <th rowspan="3">Name</th>
                        <th colspan="2" class="text-center">RFID</th>
                        <th rowspan="3">Duration</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-center">Data Time Scan</th>
                    </tr>
                    <tr>
                        <th>Scan RFID Operator</th>
                        <th>Scan Box Needle</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null,
            tableSummary = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            $('#table').addClass('nowrap');

            $('#filter_range_date').val("{{ date('Y-m-d') . ' - ' . date('Y-m-d') }}")

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
                        url: "{{ route('user.timing-log.data') }}",
                        data: function(d) {
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
                            data: 'name'
                        },
                        {
                            data: 'rfid'
                        },
                        {
                            data: 'box'
                        },
                        {
                            data: 'duration'
                        },
                    ],
                    paging: false,
                    rowCallback: function(row, data, index) {
                        // Ubah isi kolom pertama (index ke-0) jadi nomor urut
                        $('td:eq(0)', row).html(table.page.info().start + index + 1);
                    }
                });
            }, 250);
        })

        function unduh() {
            $.ajax({
                url: "{{ route('user.timing-log.unduh', ['locale' => app()->getLocale()]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    filter_tanggal: $('#filter_tanggal').val(),
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
