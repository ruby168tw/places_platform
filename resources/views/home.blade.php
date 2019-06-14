@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <form action="{{ route('organizations.search')}}" method="get">
                    @csrf
                    單位名稱：<input name="organization">
                    <button type="submit">送出</button>

                </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
