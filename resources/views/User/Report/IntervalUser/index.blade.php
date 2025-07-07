@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Interval User'">
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
                        <x-layout.button :class="'btn-primary'" :id="'cari'" :onclick="'cari()'" :icon="'fa fa-search'" :name="'SEARCH'" />
                    </div>
                </x-slot:filter>
            </x-filter.user-filter>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'graph'" :ukuran="'modal-fullscreen'">
        <x-slot:body>
            <div class="row">
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Username'" :id="'username'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Name'" :id="'name'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Division'" :id="'division'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Position'" :id="'position'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Type'" :id="'tipe'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Location'" :id="'location'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Counter'" :id="'counter'" :readonly="'readonly'" />
                </div>
                <div class="col-sm-3">
                    <x-modal.body :tipe="'text'" :label="'Total'" :id="'total'" :readonly="'readonly'" />
                </div>
            </div>
            <div id="chart" style="width: 100%; height: 500pt;"></div>
        </x-slot:body>
    </x-modal.modal>

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
        })

        function graph(d) {
            $('#graphJudul').html('<h5 class="modal-title"><i class="fa fa-chart-line"></i> Graph</h5>');
            $('#graphHeader').addClass('bg-primary');

            var z = JSON.parse(d);

            $('#username').val(z.username);
            $('#name').val(z.name);
            $('#division').val(z.division);
            $('#position').val(z.position);
            $('#tipe').val(z.tipe);
            $('#location').val(z.location);
            $('#counter').val(z.counter);
            $('#total').val(z.total);

            am4core.ready(function() {
                am4core.useTheme(am4themes_animated);
                var chart = am4core.create("chart", am4charts.XYChart);
                var x = [];
                $.each(z.date, function(k, v) {
                    x.push({
                        'date': v,
                        'out': z['x' + v.replaceAll('-', '')],
                    });
                })
                console.log(x);

                chart.data = x;
                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "date";
                categoryAxis.renderer.labels.template.rotation = 270;
                categoryAxis.renderer.grid.template.location = 0;
                categoryAxis.renderer.minGridDistance = 20;
                categoryAxis.renderer.labels.template.horizontalCenter = "left";
                categoryAxis.renderer.labels.template.verticalCenter = "middle";
                categoryAxis.renderer.labels.template.wrap = true;
                categoryAxis.renderer.labels.template.maxWidth = 200;

                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.title.text = "Transaction";
                valueAxis.renderer.opposite = false;
                valueAxis.renderer.minGridDistance = 100;

                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueY = "out";
                series.dataFields.categoryX = "date";
                series.name = "Transaction";
                series.tooltipText = "{name}: [b]{valueY}[/]";
                series.strokeWidth = 2;

                // label bullet
                var labelBullet = new am4charts.LabelBullet();
                series.bullets.push(labelBullet);
                labelBullet.label.text = "{valueY}";
                labelBullet.dy = -10;

                chart.cursor = new am4charts.XYCursor();
            })

            $('#graphModal').modal('toggle');
        }

        function cari() {
            sendAjax('', {
                url: "{{ route('user.report.interval-user.data') }}",
                type: "POST",
                data: {
                    filter_period: $('#filter_period').val(),
                    filter_status: $('#filter_status').val(),
                    filter_daily: $('#filter_daily').val(),
                    filter_weekly: $('#filter_weekly').val(),
                    filter_month: $('#filter_month').val(),
                    filter_year: $('#filter_year').val(),
                    filter_range_date: $('#filter_range_date').val(),
                },
                success: async function(response) {
                    unwaitAlert();
                    setTable(response);
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        function setTable(response) {
            return new Promise((resolve, reject) => {
                try {
                    waitAlert();
                    const promiseTable = new Promise((res, rej) => {
                        if ($.fn.DataTable.isDataTable("#table")) {
                            $('#table').html('');
                            $('#table').DataTable().clear().destroy();
                        }

                        var header = `<tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Division</th>
                            <th>Position</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Counter</th>
                            <th>Total</th>
                        `;
                        $.each(response.date, function(k, v) {
                            header += `<th>${v}</th>`;
                        });
                        header += `<th>Graph</th></tr>`;

                        $('#tableHead').html(header);

                        setTimeout(() => {
                            table = initDataTable('table', '', '', '', {
                                dom: '<"toolbar"B>flrtip',
                                buttons: [{
                                    text: 'Excel',
                                    action: function(e, dt, node, config) {
                                        unduh();
                                    },
                                }, ],
                                fixedColumns: {
                                    leftColumns: 0,
                                    rightColumns: 1
                                },
                                paging: false,
                                rowCallback: function(row, data, index) {
                                    // Ubah isi kolom pertama (index ke-0) jadi nomor urut
                                    $('td:eq(0)', row).html(table.page.info().start + index +
                                        1);
                                },
                            });
                            $.each(response.data, function(k, v) {
                                var x = [];
                                x.push(v.nomor);
                                x.push(v.username);
                                x.push(v.name);
                                x.push(v.division);
                                x.push(v.position);
                                x.push(v.tipe);
                                x.push(v.location);
                                x.push(v.counter);
                                x.push(v.total);
                                $.each(response.date, function(k1, v1) {
                                    x.push(v['x' + v1.replaceAll('-', '')]);
                                });
                                x.push(v.graph);
                                table.row.add(x).draw(false);
                            });
                            table.columns.adjust().draw();

                            res(true);
                        }, 250);
                    });

                    // Menunggu kedua promise selesai
                    Promise.all([promiseTable])
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
                url: "{{ route('user.report.interval-user.unduh', ['locale' => app()->getLocale()]) }}",
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
            cari();
        })
    </script>
@endsection
