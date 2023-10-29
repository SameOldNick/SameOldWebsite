@extends('errors::layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Internal Server Error! Something went wrong on my server.'))
