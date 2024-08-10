@if (isset($data))
    <option value=""></option>
    @foreach ($data as $d)
        <option value="{{ $d->id }}">{{ $d->opt }}</option>
    @endforeach
@endif
