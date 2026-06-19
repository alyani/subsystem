@extends('subsystem::errors.minimal')
@section('title', $error ?? '')
@section('code', '404')
@section('message', $error ?? '')