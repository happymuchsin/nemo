@extends('layouts.admin', ['page' => $page])
@section('title', $title)

@section('page-content')
    <x-layout.content :name="''">
        <x-slot:body>

        </x-slot:body>
    </x-layout.content>
@endsection
