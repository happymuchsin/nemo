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

    <x-modal.modal :name="'poto'">
        <x-slot:body>
            <div class="form-group" id="tempatpoto"></div>
        </x-slot:body>
    </x-modal.modal>

    <script>
        var page = '',
            table = null;
        $(document).ready(function() {
            setSidebar('needle_report_exchange');
        })

        function setSidebar(id) {
            $('.user_needle_report').removeClass('active');
            $('#' + id).addClass('active');
            page = id;

            var judul = '';
            if (page.replace('needle_report_', '').trim() == 'exchange') {
                judul = 'Exchange';
            } else if (page.replace('needle_report_', '').trim() == 'counter') {
                judul = 'Counter';
            } else {
                judul = '';
            }
            $('.card-title').html('<i class="text-info fas fa-chalkboard"></i> ' + judul);

            setFilter(id);

            setTable(id);
        }

        function setFilter(id) {
            $('#filterMe').html('');
            if (id == 'needle_report_exchange') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'date'" :label="'Date'" :id="'filter_date'" :colom="'col-sm-auto'" />
                    <x-filter.filter :tipe="'select'" :label="'Line'" :id="'filter_line'" :colom="'col-sm-auto'">
                        <x-slot:option>
                            @foreach ($line as $d)
                                <option value="{{ $d->id }}">{{ $d->area->name . ' - ' . $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>
                    `
                );

                $('#filter_date').val("{{ date('Y-m-d') }}")
            } else if (id == 'needle_report_counter') {
                $('#filterMe').html(
                    `<x-filter.filter :tipe="'select'" :label="'Counter'" :id="'filter_counter'" :colom="'col-sm-auto'" :alloption="false">
                        <x-slot:option>
                            @foreach ($counter as $d)
                                <option value="{{ $d->id }}">{{ $d->area->name . ' - ' . $d->name }}</option>
                            @endforeach
                        </x-slot:option>
                    </x-filter.filter>`
                );

                $('#filter_week').val("{{ date('Y') . '-W' . date('W') }}")
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
                table.destroy();
            }
            $('#tableHead').html('');
            if (id == 'needle_report_exchange') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Time</th>
                            <th>Line</th>
                            <th>User</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Remark</th>
                            <th>Image</th>
                        </tr>
                        `)
                    $('#tableFoot').html('<tr></tr>');
                    table = $('#table').DataTable({
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Daily ' + $('#filter_date').val(),
                        }, ],
                        scrollY: screen.height * 0.6,
                        scrollX: true,
                        scrollCollapse: true,
                        ajax: {
                            url: "{{ route('user.needle-report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_date = $('#filter_date').val();
                                d.filter_line = $('#filter_line').val();
                            }
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
                            {
                                data: 'gambar'
                            },
                        ],
                        order: [],
                        paging: false,
                    });
                }, 250);
            } else if (id == 'needle_report_counter') {
                setTimeout(() => {
                    $('#tableHead').html(`
                        <tr>
                            <th>Box</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Stock</th>
                        </tr>
                    `)
                    $('#tableFoot').html(`
                        <tr>
                            <th colspan="4">Total</th>
                            <th></th>
                        </tr>
                    `)
                    table = $('#table').DataTable({
                        dom: '<"toolbar"B>flrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            title: 'Stock ' + $('#filter_counter').text().trim(),
                        }, ],
                        scrollY: screen.height * 0.6,
                        scrollX: true,
                        scrollCollapse: true,
                        ajax: {
                            url: "{{ route('user.needle-report.data') }}",
                            data: function(d) {
                                d.id = id;
                                d.filter_counter = $('#filter_counter').val();
                            }
                        },
                        columns: [{
                                data: 'boxName'
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
                                data: 'qty'
                            },
                        ],
                        order: [],
                        paging: false,
                        footerCallback: function(tfoot, data) {
                            var api = this.api();
                            $(api.column(4).footer()).html(api
                                .column(4)
                                .data()
                                .reduce(function(a, b) {
                                    return +a + +b;
                                }, 0));
                        }
                    });
                }, 250);
            }
        }

        function poto(data) {
            $('#potoModal').modal('show');
            $('#tempatpoto').html('<img width="100%" height="100%" src="' + data + '" />')
        }

        socket.on('nemoReload', () => {
            table.ajax.reload();
        })
    </script>
@endsection
