@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <livewire:user.all-transaction type="deposit" />
                </div>
            </div>
        </div>
    </div>
@endsection