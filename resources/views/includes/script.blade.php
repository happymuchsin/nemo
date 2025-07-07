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
<link rel="stylesheet" href="{{ asset('plugins/fullcalendar/main.css') }}">

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

    /* Menghilangkan tombol panah pada input type number */
    .no-spin::-webkit-inner-spin-button,
    .no-spin::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .no-spin {
        -moz-appearance: textfield;
    }

    .toolbar {
        float: left;
    }

    .custom-dropdown-item:hover {
        background-color: blue;
        color: white;
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
<script src="{{ asset('plugins/popper/popper.js') }}"></script>
<script src="{{ asset('plugins/tippy/tippy.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/main.js') }}"></script>

<script src="{{ asset('plugins/socket/socket.js') }}"></script>

<script>
    const socket = io("{{ env('APP_URL') }}:3000");
</script>

<script>
    function sendAjax(modal, options) {
        const defaultOptions = {
            url: '', // URL endpoint
            type: 'POST', // HTTP method: GET, POST, PUT, DELETE
            data: {}, // Data to send
            processData: true, // Set to false for FormData
            contentType: 'application/json', // Content type
            dataType: 'json', // Expected response type
            async: true, // Asynchronous or not
            beforeSend: null, // Callback before sending request
            success: null, // Callback for success response
            error: null, // Callback for error response
            complete: null // Callback when request finishes
        };

        const config = $.extend({}, defaultOptions, options);

        // Check if data is FormData and adjust settings
        if (config.data instanceof FormData) {
            config.processData = false; // Prevent jQuery from processing data
            config.contentType = false; // Allow FormData to set its own Content-Type
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: config.url,
            type: config.type,
            data: config.contentType === 'application/json' ? JSON.stringify(config.data) : config.data,
            processData: config.processData,
            contentType: config.contentType,
            dataType: config.dataType,
            async: config.async,
            beforeSend: function() {
                waitAlert();
            },
            complete: function() {
                // unwaitAlert();
            },
            success: function(response) {
                if (typeof config.success === 'function') {
                    config.success(response);
                }
            },
            error: function(xhr, status, error) {
                if (typeof config.error === 'function') {
                    config.error(xhr, status, error);
                }
            },
        });
    }

    function customAlert(options) {
        // Default configuration
        const defaultOptions = {
            title: 'Alert', // Default title
            text: '', // Default message
            icon: 'info', // Type of alert: 'success', 'error', 'info', 'question'
            iconHtml: '',
            html: '',
            showConfirmButton: true,
            showCancelButton: false, // Show cancel button
            confirmButtonText: 'OK', // Text on confirm button
            cancelButtonText: 'Cancel', // Text on cancel button
            input: null, // Input type: 'text', 'email', 'password', etc.
            inputPlaceholder: '', // Placeholder for input
            inputValue: '', // Default value for input
            backdrop: true,
            allowOutsideClick: true, // Allow dismissing by clicking outside
            allowEscapeKey: true, // Allow dismissing with Escape key
            callback: null // Callback function for confirm action
        };

        // Merge options with defaults
        const config = {
            ...defaultOptions,
            ...options
        };

        // Call SweetAlert2
        Swal.fire({
            title: config.title,
            text: config.text,
            icon: config.icon,
            iconHtml: config.iconHtml,
            html: config.html,
            showConfirmButton: config.showConfirmButton,
            showCancelButton: config.showCancelButton,
            confirmButtonText: config.confirmButtonText,
            cancelButtonText: config.cancelButtonText,
            input: config.input,
            inputPlaceholder: config.inputPlaceholder,
            inputValue: config.inputValue,
            backdrop: config.backdrop,
            allowOutsideClick: config.allowOutsideClick,
            allowEscapeKey: config.allowEscapeKey
        }).then((result) => {
            if (result.isConfirmed && typeof config.callback === 'function') {
                config.callback(result.value); // Pass the input value to the callback if confirmed
            } else if (result.isDismissed && config.showCancelButton) {
                console.log('User canceled');
            }
        });
    }

    function warningAlert(text) {
        customAlert({
            title: 'Warning!',
            text: text,
            icon: 'warning'
        });
    }

    function successAlert(text) {
        customAlert({
            title: 'Success!',
            text: text,
            icon: 'success'
        });
    }

    function waitAlert() {
        Swal.fire({
            iconHtml: '<i class="fa-light fa-hourglass-clock fa-beat text-warning"></i>',
            title: 'Please Wait',
            html: 'Fetching your data..',
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
        Swal.showLoading();
    }

    function unwaitAlert() {
        Swal.close();
    }

    function closeAlert() {
        setTimeout(() => {
            Swal.close();
        }, 1000);
    }

    function initSelect(id, placeHolder, modal) {
        $('#' + id).select2({
            placeholder: placeHolder,
            dropdownParent: $('#' + modal),
            width: '100%',
            allowClear: true,
        });
    }

    function initDataTable(id, toolbar, modal, h, options = {}) {
        var xtoolbar = '';
        if (toolbar == '') {
            xtoolbar = 'toolbar';
        } else {
            xtoolbar = toolbar;
        }

        var xh = '';
        if (h == '') {
            xh = 0.6;
        } else {
            xh = h;
        }

        // Default configuration
        const defaultOptions = {
            dom: '<"' + xtoolbar + '">flrtip',
            scrollY: screen.height * xh,
            scrollX: true,
            scrollCollapse: true,
            // autoWidth: true,
            // responsive: true,
            searching: true,
            ordering: true,
            order: [],
            paging: true,
            pageLength: 50,
            lengthMenu: [
                [50, 100, 500, -1],
                [50, 100, 500, "All"]
            ],
        };

        // Merge user-defined options with defaults
        const config = $.extend(true, {}, defaultOptions, options);

        if (config.ajax) {
            const userAjax = config.ajax;
            config.ajax = $.extend(true, {
                beforeSend: function() {
                    waitAlert();
                },
                complete: function() {
                    unwaitAlert();
                }
            }, userAjax);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable with merged configuration
        return $('#' + id).DataTable(config);
    }

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
