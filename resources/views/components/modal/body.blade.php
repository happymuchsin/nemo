<label>{{ $label }} </label>
<div class="form-group">
    @if ($tipe == 'text')
        <input type="text" {{ $readonly }} {{ $disable }} required name="{{ $name }}" id="{{ $id }}" class="form-control"
            style="{{ $upper ? 'text-transform:uppercase' : '' }}" autocomplete="off">
    @elseif ($tipe == 'textarea')
        <textarea {{ $readonly }} {{ $disable }} required name="{{ $name }}" id="{{ $id }}" style="{{ $upper ? 'text-transform:uppercase' : '' }}" autocomplete="off"
            class="form-control" rows="{{ $row }}"></textarea>
    @elseif ($tipe == 'number')
        <input type="number" {{ $disable }} required min="{{ $min }}" max="{{ $max }}" name="{{ $name }}" id="{{ $id }}" class="form-control"
            autocomplete="off">
    @elseif ($tipe == 'select')
        <select id="{{ $id }}" {{ $multiple }} {{ $disable }}>
            @if ($defaultOption)
                <option value=""></option>
            @endif
            @if (isset($option))
                {{ $option }}
            @endif
        </select>
    @elseif ($tipe == 'checkbox')
        <input type="checkbox" {{ $disable }} required name="{{ $name }}" id="{{ $id }}" class="form-check" style="zoom:2;">
    @elseif ($tipe == 'file')
        <input type="file" class="form-control" {{ $disable }} name="{{ $name }}" id="{{ $id }}" accept="{{ $accept }}" {{ $multiple }}>
    @elseif ($tipe == 'date')
        <input type="date" required id="{{ $id }}" class="form-control" autocomplete="off">
    @elseif ($tipe == 'month')
        <input type="month" required id="{{ $id }}" class="form-control" autocomplete="off">
    @elseif ($tipe == 'email')
        <input type="email" required id="{{ $id }}" class="form-control" autocomplete="off">
    @elseif ($tipe == 'password')
        <input type="password" required id="{{ $id }}" class="form-control" autocomplete="off">
    @endif
</div>
