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
            table = initDataTable('table', '', '', 0.4, {
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
                paging: false,
            });
            $('div.toolbar').html(
                `<button class="btn btn-sm btn-danger" onclick="Truncate();"><i class="fa fa-trash-can-xmark"></i> Clear Log</button>`
            );
        })

        function Truncate() {
            customAlert({
                icon: 'question',
                title: "Are you sure?",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                confirmButtonColor: '#dc3545',
                cancelButtonText: "Cancel",
                callback: function() {
                    sendAjax('', {
                        url: "{{ route('admin.tools.activity-log.hapus') }}",
                        type: "GET",
                        success: function(response) {
                            successAlert(response);
                            closeAlert();
                            setTimeout(() => {
                                table.ajax.reload();
                            }, 1000);
                        },
                        error: function(response) {
                            warningAlert(response.responseText);
                        }
                    });
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
