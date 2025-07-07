<x-mail::message>
Dear Admin Sample,<br>
Tolong order needle item ini :

<x-mail::table>
| No | Brand | Type | Size | Code | Min Stock | Stock | 
| :-: | :---: | :--: | :--: | :--: | :-------: | :---: |
@foreach ($data as $k => $d)
| {{ $k + 1 }} | {{ $d->brand }} | {{ $d->tipe }} | {{ $d->size }} | {{ $d->code }} | {{ $d->min }} | <span style="color:red;">{{ $d->stock }}</span> |
@endforeach
</x-mail::table>
{{-- <x-mail::button :url="''">
Button Text
</x-mail::button> --}}

Send by system. Needle Mobile.
<br>
# PT. ANGGUN KREASI GARMEN
</x-mail::message>
