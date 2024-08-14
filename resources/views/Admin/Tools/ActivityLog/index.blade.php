@extends('layouts.admin', ['page' => $page, 'ngapain' => $ngapain])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Activity Log'">
        <x-slot:body>
            <form method="POST">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <h2><b>Start Date : </b></h2>
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="start" name="start" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <h2><b>End Date : </b></h2>
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="end" name="end" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <button type="button" name="submit" class="btn btn-primary">Process</button>
                        </div>
                    </div>
                </div>
            </form>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>Username</th>
                        <th>Activity</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <script>
        var table = null;
        $(document).ready(function() {
            table = $('#table').DataTable({
                scrollY: '50vh',
                scrollX: '80vh',
                scrollCollapse: true,
                paging: false,
                order: [],
                dom: '<"toolbar">frtip',
                ajax: {
                    url: "{{ route('admin.tools.activity-log.data') }}",
                    data: function(d) {
                        d.start = $('#start').val();
                        d.end = $('#end').val();
                    }
                },
                columns: [{
                        data: 'username',
                    },
                    {
                        data: "activity",
                    },
                    {
                        data: "ip_address",
                    },
                    {
                        data: "created_at",
                    },
                ],
            });
            $('div.toolbar').html(
                `<button class="btn btn-sm btn-danger" onclick="Truncate();"><i class="fa fa-trash-can-xmark"></i> Clear Log</button>`
            );
        })

        function Truncate() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('admin.tools.activity-log.hapus') }}",
                        beforeSend: function() {
                            Swal.fire({
                                iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
                                title: 'Please Wait',
                                html: 'Fetching your data..',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            });
                            Swal.showLoading();
                        },
                        complete: function() {
                            // Swal.close();
                        },
                        success: function(response) {
                            Swal.fire('Success!', response, 'success');
                            setTimeout(() => {
                                Swal.close();
                            }, 1000);
                            table.ajax.reload();
                        },
                        error: function(response) {
                            Swal.fire('Warning!', response, 'warning');
                        }
                    })
                }
            })
        }
    </script>
    <style>
        th,
        td {
            white-space: nowrap;
        }

        .toolbar {
            float: left;
        }
    </style>
@endsection
