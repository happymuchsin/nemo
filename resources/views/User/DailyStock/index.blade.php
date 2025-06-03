@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Daily Stock'">
        <x-slot:body>
            <x-filter.user-filter>
                <x-slot:filter>
                    <x-filter.filter :tipe="'select'" :label="'Period'" :id="'filter_period'" :colom="'col-sm-auto'"
                        :alloption="false">
                        <x-slot:option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </x-slot:option>
                    </x-filter.filter>
                    <x-filter.filter :tipe="'date'" :label="'Daily'" :id="'filter_daily'" :colom="'col-sm-auto filter-daily'" />
                    <x-filter.filter :tipe="'week'" :label="'Weekly'" :id="'filter_weekly'" :colom="'col-sm-auto filter-weekly'" />
                    <x-filter.filter :tipe="'month'" :label="'Month'" :id="'filter_month'" :colom="'col-sm-auto filter-month'" />
                    <x-filter.filter :tipe="'number'" :label="'Year'" :id="'filter_year'" :colom="'col-sm-auto filter-year'" />
                    <div class="form-group">
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'cari()'" :icon="'fa fa-search'"
                            :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
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
        var table = null,
            tableSummary = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
            $('#table').addClass('nowrap');
            $('#filter_period').on('change', function() {
                if ($(this).val() == 'daily') {
                    $('.filter-daily').show();
                    $('.filter-weekly').hide();
                    $('.filter-month').hide();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'weekly') {
                    $('.filter-daily').hide();
                    $('.filter-weekly').show();
                    $('.filter-month').hide();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'monthly') {
                    $('.filter-daily').hide();
                    $('.filter-weekly').hide();
                    $('.filter-month').show();
                    $('.filter-year').hide();
                } else if ($(this).val() == 'yearly') {
                    $('.filter-daily').hide();
                    $('.filter-weekly').hide();
                    $('.filter-month').hide();
                    $('.filter-year').show();
                }
            });
            $('#filter_period').val('daily').trigger('change');
            $('#filter_daily').val("{{ date('Y-m-d') }}").trigger('change');
            $('#filter_weekly').val("{{ date('Y') . '-W' . date('W') }}").trigger('change');
            $('#filter_month').val("{{ date('Y-m') }}").trigger('change');
            $('#filter_year').val("{{ date('Y') }}").trigger('change');
        })

        function cari() {
            sendAjax('', {
                url: "{{ route('user.daily-stock.data') }}",
                type: "POST",
                data: {
                    filter_period: $('#filter_period').val(),
                    filter_status: $('#filter_status').val(),
                    filter_daily: $('#filter_daily').val(),
                    filter_weekly: $('#filter_weekly').val(),
                    filter_month: $('#filter_month').val(),
                    filter_year: $('#filter_year').val(),
                },
                success: function(response) {
                    unwaitAlert();

                    if (response.found == 'not') {
                        warningAlert('No data found');
                        return;
                    }

                    if ($.fn.DataTable.isDataTable("#table")) {
                        $('#table').html('');
                        $('#table').DataTable().clear().destroy();
                    }

                    var header = `<tr>
                            <th rowspan="3">No</th>
                            <th rowspan="3">Brand</th>
                            <th rowspan="3">Type</th>
                            <th rowspan="3">Size</th>
                            <th rowspan="3">Code</th>
                            <th>A</th>
                        `;
                    if (response.issue.length > 0) {
                        header += `<th colspan="${response.issue.length}">B</th>`;
                    }
                    if (response.add.length > 0) {
                        header += `<th colspan="${response.add.length}">C</th>`;
                    }
                    header += `
                            <th>(A - B + C)
                        </tr>
                        <tr>
                            <th rowspan="2">QTY Opening Stock</th>`;
                    if (response.issue.length > 0) {
                        header += `<th colspan="${response.issue.length}">Issue to Operator</th>`;
                    }
                    if (response.add.length > 0) {
                        header += `<th colspan="${response.add.length}">Add</th>`;
                    }
                    header += `
                            <th rowspan="2">QTY Closing Stock</th>
                        </tr>
                        <tr>
                        `;
                    $.each(response.issue, function(ki, vi) {
                        header += `<th>${vi}</th>`;
                    });
                    $.each(response.add, function(ka, va) {
                        header += `<th>${va}</th>`;
                    });
                    header += `</tr>`;

                    var footer = `<tr>
                            <th colspan="5" class="text-center">Total</th>
                            <th></th>
                        `;
                    $.each(response.issue, function(ki, vi) {
                        footer += `<th></th>`;
                    });
                    $.each(response.add, function(ka, va) {
                        footer += `<th></th>`;
                    });
                    footer += `<th></th>
                        </tr>`;

                    $('#tableHead').html(header);
                    $('#tableFoot').html(footer);

                    setTimeout(() => {
                        table = initDataTable('table', '', '', '', {
                            dom: '<"toolbarSummary"B>flrtip',
                            buttons: [{
                                text: 'Excel',
                                action: function(e, dt, node, config) {
                                    unduh();
                                },
                            }, ],
                            paging: false,
                            rowCallback: function(row, data, index) {
                                // Ubah isi kolom pertama (index ke-0) jadi nomor urut
                                $('td:eq(0)', row).html(table.page.info().start + index +
                                    1);
                            },
                            footerCallback: function(tfoot, data, start, end, display) {
                                var api = this.api();
                                if (end > 0) {
                                    for (var s = 5; s <= api.columns().count() - 1; s++) {
                                        var x = api.column(s, {
                                            search: 'applied'
                                        }).data().reduce(function(a, b) {
                                            return +a + +b;
                                        }, 0);
                                        $(api.column(s).footer()).html(x);
                                    }
                                }
                            }
                        });
                        $.each(response.data, function(k, v) {
                            var x = [];
                            x.push(v.nomor);
                            x.push(v.brand);
                            x.push(v.tipe);
                            x.push(v.size);
                            x.push(v.code);
                            x.push(v.opening);
                            $.each(response.issue, function(ki, vi) {
                                x.push(v['xout' + vi.replaceAll('-', '')]);
                            });
                            $.each(response.add, function(ka, va) {
                                x.push(v['xin' + va.replaceAll('-', '')]);
                            });
                            x.push(v.closing);
                            table.row.add(x).draw(false);
                        });
                        table.columns.adjust().draw();
                    }, 250);
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        function unduh() {
            $.ajax({
                url: "{{ route('user.daily-stock.unduh', ['locale' => app()->getLocale()]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    filter_period: $('#filter_period').val(),
                    filter_status: $('#filter_status').val(),
                    filter_daily: $('#filter_daily').val(),
                    filter_weekly: $('#filter_weekly').val(),
                    filter_month: $('#filter_month').val(),
                    filter_year: $('#filter_year').val(),
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
