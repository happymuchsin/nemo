<link rel="stylesheet" href="{{ asset('assets/font/font.css') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.png') }}">

<link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-select/css/select.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-keytable/css/keyTable.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-rowgroup/css/rowGroup.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/dropzone/min/dropzone.min.css') }}">

<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

<style>
    .form-group.floating>label {
        bottom: 34px;
        left: 8px;
        position: relative;
        background-color: white;
        padding: 0px 5px 0px 5px;
        font-size: 1.1em;
        transition: 0.1s;
        pointer-events: none;
        font-weight: 500 !important;
        transform-origin: bottom left;
    }

    .form-control.floating:focus~label {
        transform: translate(1px, -85%) scale(0.80);
        opacity: .8;
        color: #005ebf;
    }

    .form-control.floating:valid~label {
        transform-origin: bottom left;
        transform: translate(1px, -85%) scale(0.80);
        opacity: .8;
    }

    .form-control.floating:disabled~label {
        transform-origin: bottom left;
        transform: translate(1px, -85%) scale(0.80);
        opacity: .8;
    }

    .modal-fullscreen {
        max-width: 100%;
        margin: 0;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh;
        display: flex;
    }

    .toolbar {
        float: left;
    }
</style>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-fixedcolumns/js/fixedColumns.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-select/js/select.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-keytable/js/keyTable.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-rowgroup/js/dataTables.rowGroup.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-rowgroup/js/rowGroup.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('dist/js/EmptyColumns.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('plugins/dropzone/min/dropzone.min.js') }}"></script>

<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

<script src="{{ asset('plugins/amchart4/core.js') }}"></script>
<script src="{{ asset('plugins/amchart4/charts.js') }}"></script>
<script src="{{ asset('plugins/amchart4/animated.js') }}"></script>

<script src="{{ asset('plugins/multidatespicker/jquery-ui.multidatespicker.js') }}"></script>

<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"
    integrity="sha384-2huaZvOR9iDzHqslqwpR87isEmrfxqyWOF7hr7BY6KG0+hVKLoEXMPUJw3ynWuhO" crossorigin="anonymous">
</script>
<script>
    const socket = io("{{ env('APP_URL') }}:3000");
</script>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $(document).ready(function() {
        notif();
    })

    toastr.options = {
        closeButton: false,
        debug: false,
        newestOnTop: true,
        progressBar: false,
        positionClass: 'toast-top-right',
        preventDuplicates: false,
        showDuration: '300',
        hideDuration: '1000',
        timeOut: '5000',
        extendedTimeOut: '1000',
        showEasing: 'easeOutBounce',
        hideEasing: 'easeInBack',
        closeEasing: 'easeInBack',
        showMethod: 'slideDown',
        hideMethod: 'slideUp',
        closeMethod: 'slideUp',
    }

    toastr.options.onclick = function() {
        window.open(this.data.link, "_self")
    }

    function notif() {
        $.get("{{ route('notif') }}", function(x) {
            $('#bellCountNotif').text(x.jml);
            $('#countNotif').text(x.jml + ' Notifications');
            $('#divNotif').html('');
            $.each(x.notif, function(k, v) {
                $('#divNotif').append(`
                    <a href="` + v.data.link + `" class="dropdown-item" style="text-wrap:wrap">
                        ` + v.data.title + `<br>
                        <span class="text-muted text-sm">` + v.data.message + `</span>
                    </a>
                `);
            });
        });
    }

    socket.on('nemoNewNotification', (x) => {
        toastr.info(x.message, x.title, {
            data: {
                link: x.link,
            }
        });
        notif();
    });

    socket.on('connect', () => {
        socket.emit('register', {
            username: '{{ auth()->user()->username }}',
            division: '{{ auth()->user()->master_division_id }}',
            position: '{{ auth()->user()->master_position_id }}',
            type: 'website',
            app: 'nemo',
        });
    })
</script>
