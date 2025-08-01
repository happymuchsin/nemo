@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Daily Broken Needle Sample Room'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :colom="'col-sm-auto'" :tipe="'date'" :label="'Date'" :id="'filter_date'" />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'table.ajax.reload()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th rowspan="3">Needle Code</th>
                        <th rowspan="3">Needle Type</th>
                        <th rowspan="3">Size</th>
                        <th colspan="2">Stock</th>
                        <th rowspan="3">Morning Stock Update</th>
                        <th rowspan="3">Incoming Stock</th>
                        <th colspan="3" class="text-center">Sample Line 1 (LINE ACENG)</th>
                        <th colspan="3" class="text-center">Sample Line 2 (LINE OTONG)</th>
                        <th colspan="3" class="text-center">Sample Line 3 (LINE YANTI)</th>
                        <th colspan="3" class="text-center">Sample Line 4 (LINE SUNARI)</th>
                        <th colspan="3" class="text-center">Sample Line 5 (LINE SMS MURNI)</th>
                        <th colspan="3" class="text-center">Sample Line 6 (LINE SMS YANTI)</th>
                        <th colspan="3" class="text-center">Sample Line 7 (LINE SRIWIJAYANING)</th>
                        <th colspan="3" class="text-center">Sample Line 8 (CNC)</th>
                        <th colspan="3" class="text-center">Sample Line 9 (FINISHING)</th>
                        <th rowspan="3">Sub Total</th>
                        <th colspan="2" class="text-center">Broken Missing Fragment</th>
                        <th rowspan="3">Grand Total</th>
                        <th rowspan="3">Broken Total</th>
                        <th rowspan="3">Tumpul Total</th>
                        <th rowspan="3">Missing Fragment Total</th>
                        <th rowspan="3">End of Stock Update</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Min</th>
                        <th rowspan="2">Max</th>
                        @for ($i = 1; $i <= 9; $i++)
                            <th>Deformed</th>
                            <th rowspan="2">Operator</th>
                            <th data-tippy-content="Routing Change, Change Style or Material">Change</th>
                        @endfor
                        <th rowspan="2">Line Sample</th>
                        <th rowspan="2">Operator</th>
                    </tr>
                    <tr>
                        @for ($i = 1; $i <= 9; $i++)
                            <th>Broken</th>
                            <th>Tumpul</th>
                        @endfor
                    </tr>
                </x-slot:thead>
                <x-slot:tfoot>
                    <tr>
                        <th colspan="3" class="text-center">TOTAL</th>
                        @for ($i = 1; $i <= 39; $i++)
                            <th></th>
                        @endfor
                    </tr>
                </x-slot:tfoot>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null,
            tableSummary = null,
            averageDuration = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            // $('#table').addClass('nowrap');

            $('#filter_date').val("{{ date('Y-m-d') }}");

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
                        url: "{{ route('user.report.daily-broken-needle.data') }}",
                        data: function(d) {
                            d.filter_date = $('#filter_date').val();
                        },
                    },
                    columns: [{
                            data: 'code'
                        },
                        {
                            data: 'tipe'
                        },
                        {
                            data: 'size'
                        },
                        {
                            data: 'min_stock'
                        },
                        {
                            data: 'max_stock'
                        },
                        {
                            data: 'morning_stock'
                        },
                        {
                            data: 'incoming_stock'
                        },
                        {
                            data: 'deformed_1'
                        },
                        {
                            data: 'operator_1'
                        },
                        {
                            data: 'change_1'
                        },
                        {
                            data: 'deformed_2'
                        },
                        {
                            data: 'operator_2'
                        },
                        {
                            data: 'change_2'
                        },
                        {
                            data: 'deformed_3'
                        },
                        {
                            data: 'operator_3'
                        },
                        {
                            data: 'change_3'
                        },
                        {
                            data: 'deformed_4'
                        },
                        {
                            data: 'operator_4'
                        },
                        {
                            data: 'change_4'
                        },
                        {
                            data: 'deformed_5'
                        },
                        {
                            data: 'operator_5'
                        },
                        {
                            data: 'change_5'
                        },
                        {
                            data: 'deformed_6'
                        },
                        {
                            data: 'operator_6'
                        },
                        {
                            data: 'change_6'
                        },
                        {
                            data: 'deformed_7'
                        },
                        {
                            data: 'operator_7'
                        },
                        {
                            data: 'change_7'
                        },
                        {
                            data: 'deformed_8'
                        },
                        {
                            data: 'operator_8'
                        },
                        {
                            data: 'change_8'
                        },
                        {
                            data: 'deformed_9'
                        },
                        {
                            data: 'operator_9'
                        },
                        {
                            data: 'change_9'
                        },
                        {
                            data: 'sub_total'
                        },
                        {
                            data: 'line_sample'
                        },
                        {
                            data: 'operator_line_sample'
                        },
                        {
                            data: 'grand_total'
                        },
                        {
                            data: 'total_broken'
                        },
                        {
                            data: 'total_tumpul'
                        },
                        {
                            data: 'missing_fragment_total'
                        },
                        {
                            data: 'end_of_stock_update'
                        },
                    ],
                    paging: false,
                    drawCallback: function(settings) {
                        $('th[data-tippy-content]').each(function() {
                            tippy($(this).get(0), {
                                content: $(this).attr('data-tippy-content'),
                            });
                        });
                    },
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api();
                        if (end > 0) {
                            for (var s = 3; s <= api.columns().count() - 1; s++) {
                                var x = api.column(s, {
                                    search: 'applied'
                                }).data().reduce(function(a, b) {
                                    return +a + +b;
                                }, 0);
                                $(api.column(s).footer()).html(x);
                            }
                        }
                    },
                });
            }, 250);
        })

        function unduh() {
            $.ajax({
                url: "{{ route('user.report.daily-broken-needle.unduh', ['locale' => app()->getLocale()]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    filter_date: $('#filter_date').val(),
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
