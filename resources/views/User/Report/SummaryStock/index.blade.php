@extends('layouts.user', ['page' => $page, 'sidebar' => false])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Summary Stock'">
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
                        <th>Date</th>
                        <th>Stock Opening</th>
                        <th>Out (Issue to Operator)</th>
                        <th>In (Add Stock)</th>
                        <th>Closing Balance</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null;

        $(document).ready(function() {
            $('#collSidebar').attr('hidden', true);
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
                        extend: 'excelHtml5',
                        title: function() {
                            var x = '';
                            if ($('#filter_period').val() == 'yearly') {
                                x = $('#filter_year').val();
                            } else if ($('#filter_period').val() == 'monthly') {
                                x = $('#filter_month').val();
                            } else if ($('#filter_period').val() == 'weekly') {
                                x = $('#filter_weekly').val();
                            } else if ($('#filter_period').val() == 'daily') {
                                x = $('#filter_daily').val();
                            } else if ($('#filter_period').val() == 'range') {
                                x = $('#filter_range_date').val();
                            }
                            return 'Summary Stock ' + x;
                        },
                    }, ],
                    ajax: {
                        url: "{{ route('user.report.summary-stock.data') }}",
                        data: function(d) {
                            d.filter_period = $('#filter_period').val();
                            d.filter_daily = $('#filter_daily').val();
                            d.filter_weekly = $('#filter_weekly').val();
                            d.filter_month = $('#filter_month').val();
                            d.filter_year = $('#filter_year').val();
                            d.filter_range_date = $('#filter_range_date').val();
                            d.filter_status = $('#filter_status').val();
                        },
                    },
                    columns: [{
                            data: 'tanggal'
                        },
                        {
                            data: 'opening'
                        },
                        {
                            data: 'out'
                        },
                        {
                            data: 'in'
                        },
                        {
                            data: 'closing'
                        },
                    ],
                    paging: false,
                });
            }, 250);
        })

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
