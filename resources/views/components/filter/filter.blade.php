<div class="{{ $colom }}">
    <div class="form-group floating">
        @if ($tipe == 'select')
            <select name="{{ $name }}" id="{{ $id }}" class="form-control floating">
                @if ($alloption)
                    <option value="all">ALL</option>
                @endif
                {{ $option }}
            </select>
        @elseif ($tipe == 'date')
            <input type="date" required class="form-control floating" id="{{ $id }}" autocomplete="off">
        @elseif ($tipe == 'week')
            <input type="week" required class="form-control floating" id="{{ $id }}" autocomplete="off">
        @elseif ($tipe == 'month')
            <input type="month" required class="form-control floating" id="{{ $id }}" autocomplete="off">
        @elseif ($tipe == 'text')
            <input type="text" required class="form-control floating" id="{{ $id }}" autocomplete="off">
        @elseif ($tipe == 'number')
            <input type="number" required class="form-control floating" id="{{ $id }}" autocomplete="off">
        @elseif ($tipe == 'range')
            <input type="text" required class="form-control floating" id="{{ $id }}" autocomplete="off">
        @endif
        <label for="{{ $id }}"><b>{{ $label }}</b></label>
    </div>
</div>
