@extends('frontend.layouts.app')

@section('title', 'Blog')
@section('meta_description', 'Entdecke spannende Artikel, Tipps und Neuigkeiten in unserem Blog. Bleibe informiert und inspiriert!')

@section('content')

<livewire:frontend.blog.blog-list />


    @endsection
