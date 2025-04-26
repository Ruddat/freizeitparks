@extends('frontend.layouts.app')

@section('title', $park->title)

@section('content')

<livewire:frontend.statistic.crowd-calendar :park="$park" />

<livewire:frontend.statistic.crowd-chart :park="$park" :year="now()->year" :month="now()->month" />



@endsection
