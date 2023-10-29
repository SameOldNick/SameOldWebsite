@extends('errors::layout')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized access! You do not have permission to view this page.'))

