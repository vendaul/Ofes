@if(request('role'))
<div class="alert alert-info text-center">
    Logging in as: <strong>{{ ucfirst(request('role')) }}</strong>
</div>
@endif
@extends('adminlte::auth.login')
