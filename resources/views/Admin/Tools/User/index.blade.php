@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="'Data Users'">
        <x-slot:body>
            <x-layout.table :id="'table'">
                <x-slot:thead>
                    <tr>
                        <th>NIK / Username</th>
                        <th>Name</th>
                        <th>RFID</th>
                        <th>Division</th>
                        <th>Position</th>
                        <th>Skill</th>
                        <th>Role</th>
                        <th>Join Date</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-layout.content>

    <x-modal.modal :name="'crup'">
        <x-slot:body>
            <input type="hidden" name="key" id="key">
            <x-modal.body :tipe="'text'" :label="'NIK / Username'" :id="'username'" :upper="false" />
            <x-modal.body :tipe="'text'" :label="'Name'" :id="'name'" />
            <x-modal.body :tipe="'text'" :label="'RFID'" :id="'rfid'" />
            <x-modal.body :tipe="'select'" :label="'Division'" :id="'master_division_id'">
                <x-slot:option>
                    @foreach ($divisi as $d)
                        <option value="{{ $d->id }}">
                            {{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Position'" :id="'master_position_id'">
                <x-slot:option>
                    @foreach ($position as $d)
                        <option value="{{ $d->id }}">
                            {{ $d->name }}</option>
                    @endforeach
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'select'" :label="'Skill'" :id="'skill'">
                <x-slot:option>
                    <option value="MULTI">MULTI</option>
                    <option value="SINGLE">SINGLE</option>
                </x-slot:option>
            </x-modal.body>
            <x-modal.body :tipe="'date'" :label="'Join Date'" :id="'join_date'" />
            <x-modal.body :tipe="'password'" :label="'Password'" :id="'password'" />
        </x-slot:body>
        <x-slot:footer>
            <x-layout.button :class="'btn-primary'" :id="'save'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Save'" />
            <x-layout.button :class="'btn-primary'" :id="'update'" :onclick="'crup()'" :icon="'fa fa-save'"
                :name="'Update'" />
        </x-slot:footer>
    </x-modal.modal>

    <x-modal.modal :name="'detail'" :ukuran="'modal-xl'">
        <x-slot:body>
            <input type="hidden" id="user_id">
            <x-layout.table :id="'tableDetail'">
                <x-slot:thead>
                    <tr>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </x-slot:thead>
            </x-layout.table>
        </x-slot:body>
    </x-modal.modal>

    <script>
        var table = null,
            tableDetail = null;
        $(document).ready(function() {
            initSelect('master_division_id', 'Select Division', 'crupModal');
            initSelect('master_position_id', 'Select Position', 'crupModal');
            initSelect('skill', 'Select Skill', 'crupModal');
            table = initDataTable('table', '', '', '', {
                fixedColumns: {
                    rightColumns: 1
                },
                ajax: {
                    url: "{{ route('admin.tools.user.data') }}",
                },
                columns: [{
                        data: "username"
                    },
                    {
                        data: "name"
                    },
                    {
                        data: "rfid"
                    },
                    {
                        data: "division"
                    },
                    {
                        data: "position"
                    },
                    {
                        data: "skill"
                    },
                    {
                        data: "role"
                    },
                    {
                        data: "join_date"
                    },
                    {
                        data: "action",
                        sortable: false,
                    }
                ],
                paging: false,
            });
            $('div.toolbar').html(
                '<button class="btn btn-sm btn-success" onclick="add();"><i class="fa fa-circle-plus"url: "ajax/></i> New</button>'
            );

            $('#detailModal').on('hidden.bs.modal', function(e) {
                setTimeout(() => {
                    table.ajax.reload();
                }, 1000);
            })
        })

        function add() {
            $('#crupJudul').html('<h5 class="modal-title"><i class="fa fa-file-plus"></i> New</h5>');
            $('#crupHeader').addClass('bg-success');
            $('#crupHeader').removeClass('bg-info');
            $('#save').show();
            $('#update').hide();
            $('#username').val('');
            $('#name').val('');
            $('#rfid').val('');
            $('#master_division_id').val('').trigger('change');
            $('#master_position_id').val('').trigger('change');
            $('#skill').val('').trigger('change');
            $('#join_date').val('');
            $('#password').val('');
            $('#key').val(0);
            $('#crupModal').modal('toggle');
        }

        function crup() {
            var c = 0;
            if ($('#username').val() == '') {
                warningAlert('Please insert NIK / Username');
            } else if ($('#name').val() == '') {
                warningAlert('Please insert Name');
            } else if ($('#master_division_id').val() == '') {
                warningAlert('Please select Division');
            } else if ($('#master_position_id').val() == '') {
                warningAlert('Please select Position');
            } else if ($('#skill').val() == '') {
                warningAlert('Please select Skill');
            } else if ($('#join_date').val() == '') {
                warningAlert('Please select Join Date');
            } else if ($('#password').val() == '') {
                if ($('#key').val() == 0) {
                    warningAlert('Please insert Password');
                } else {
                    c = 1;
                }
            } else {
                c = 1;
            }

            if (c == 1) {
                sendAjax('crupModal', {
                    url: "{{ route('admin.tools.user.crup') }}",
                    type: "POST",
                    data: {
                        id: $('#key').val(),
                        username: $('#username').val(),
                        name: $('#name').val(),
                        rfid: $('#rfid').val(),
                        master_division_id: $('#master_division_id').val(),
                        master_position_id: $('#master_position_id').val(),
                        skill: $('#skill').val(),
                        join_date: $('#join_date').val(),
                        password: $('#password').val(),
                    },
                    success: function(response) {
                        $('#crupModal').modal('toggle');
                        successAlert(response);
                        closeAlert();
                        setTimeout(() => {
                            table.ajax.reload();
                        }, 1000);
                    },
                    error: function(response) {
                        warningAlert(response.responseText);
                    }
                })
            }
        }

        function edit(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#crupJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Edit</h5>');
                    $('#crupHeader').removeClass('bg-success');
                    $('#crupHeader').addClass('bg-info');
                    $('#save').hide();
                    $('#update').show();
                    $('#username').val(response.username);
                    $('#name').val(response.name);
                    $('#rfid').val(response.rfid);
                    $('#master_division_id').val(response.master_division_id).trigger('change');
                    $('#master_position_id').val(response.master_position_id).trigger('change');
                    $('#skill').val(response.skill).trigger('change');
                    $('#join_date').val(response.join_date);
                    $('#password').val('');
                    $('#password').val('');
                    $('#key').val(response.id);
                    $('#crupModal').modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function check(url) {
            sendAjax('', {
                url: url,
                type: "GET",
                success: function(response) {
                    unwaitAlert();
                    if (response.tipe == 'not') {
                        hapus(response.id);
                    } else {
                        customerAlert({
                            icon: 'warning',
                            title: response.message,
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            confirmButtonColor: '#dc3545',
                            callback: function() {
                                hapus(response.id);
                            }
                        })
                    }
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })

        }

        function hapus(id) {
            customAlert({
                icon: 'question',
                title: "Are you sure want to permanent Delete this Data?",
                showCancelButton: true,
                confirmButtonText: "Confirm Delete",
                confirmButtonColor: '#dc3545',
                cancelButtonText: "Cancel",
                callback: function() {
                    sendAjax('', {
                        url: "{{ route('admin.tools.user.hapus') }}",
                        type: "POST",
                        data: {
                            'id': id,
                        },
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

        function detail(url) {
            sendAjax('', {
                url: url,
                type: "get",
                success: function(response) {
                    unwaitAlert();
                    $('#detailJudul').html(
                        '<h5 class="modal-title"><i class="fa fa-file-pen"></i> Detail User ' +
                        response.username + '</h5>');
                    $('#detailHeader').removeClass('bg-success');
                    $('#detailHeader').addClass('bg-info');

                    if ($.fn.DataTable.isDataTable("#tableDetail")) {
                        $('#tableDetail').html('');
                        $('#tableDetail').DataTable().clear().destroy();
                    }

                    setTimeout(() => {
                        tableDetail = initDataTable('tableDetail', 'toolbarDetail', '', 0.4, {
                            ajax: {
                                url: "{{ route('admin.tools.user.data-role') }}",
                                data: function(d) {
                                    d.user_id = $('#user_id').val();
                                }
                            },
                            columns: [{
                                    data: 'name',
                                },
                                {
                                    data: 'action',
                                }
                            ],
                            paging: false,
                        });
                        $('div.toolbarDetail').html(
                            '<div><label>Role</label><div class="form-group" onchange="crupRole()"><select name="role" id="role" class="form-select" required></select></div></div>'
                        );
                        $('#role').select2({
                            placeholder: "Select Role",
                            dropdownParent: $('#detailModal'),
                            width: '100%',
                            ajax: {
                                url: "{{ route('admin.tools.user.spinner') }}",
                                dataType: 'json',
                                data: function(params) {
                                    const query = {
                                        user_id: $('#user_id').val(),
                                        q: $.trim(params.term)
                                    };
                                    return query;
                                },
                                processResults: function(data) {
                                    return {
                                        results: $.map(data, function(item) {
                                            return {
                                                text: item.description,
                                                id: item.id,
                                            };
                                        }),
                                    };
                                },
                                cache: false
                            }
                        });
                    }, 250);

                    $('#user_id').val(response.user_id);

                    $("#detailModal").modal('toggle');
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            });
        }

        function crupRole() {
            sendAjax('crupModal', {
                url: "{{ route('admin.tools.user.crup-role') }}",
                type: "POST",
                data: {
                    'user_id': $('#user_id').val(),
                    'role_id': $('#role').val(),
                },
                success: function(response) {
                    successAlert(response);
                    closeAlert();
                    setTimeout(() => {
                        tableDetail.ajax.reload();
                    }, 1000);
                },
                error: function(response) {
                    warningAlert(response.responseText);
                }
            })
        }

        function hapusDetail(url) {
            customAlert({
                icon: 'question',
                title: "Are you sure want to permanent Delete this Data?",
                showCancelButton: true,
                confirmButtonText: "Confirm Delete",
                confirmButtonColor: '#dc3545',
                cancelButtonText: "Cancel",
                callback: function() {
                    sendAjax('', {
                        url: url,
                        type: "GET",
                        success: function(response) {
                            successAlert(response);
                            closeAlert();
                            setTimeout(() => {
                                tableDetail.ajax.reload();
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
        .toolbarDetail {
            float: left;
        }
    </style>
@endsection
