<x-filter.filter :label="'Year'" :id="'tahun'" :tipe="'select'" :all-option="false">
    <x-slot:option>
        @for ($x = date('Y'); $x >= 2023; $x--)
            <option value="{{ $x }}">{{ $x }}</option>
        @endfor
    </x-slot:option>
</x-filter.filter>
<x-filter.filter :label="'Month'" :id="'bulan'" :tipe="'select'">
    <x-slot:option>
        @for ($mm = 1; $mm <= 12; $mm++)
            <option value="{{ $mm }}">
                {{ date('F', mktime(0, 0, 0, $mm, 1)) }}</option>;
        @endfor
    </x-slot:option>
</x-filter.filter>
