@extends('layouts.app')

@section('title', 'StellarTech Explorers Club — Robotics, AI, Space Science & Heritage Technology')
@section('description', 'A student-led engineering organisation working across robotics, artificial intelligence, space science and heritage-preservation technology.')

@section('content')
    @include('public.sections.hero')
    @include('public.sections.mission')
    @include('public.sections.disciplines')
    @include('public.sections.news',       ['articles' => $articles])
    @include('public.sections.leadership', ['founder' => $founder, 'committee' => $committee])
    @include('public.sections.rakhandar')
    @include('public.sections.media',      ['media' => $media])
    @include('public.sections.pathways')
    @include('public.sections.cta')
@endsection
