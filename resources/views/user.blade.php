@extends('layouts.admin')

@section('title')
    User
@endsection

@section('content')
    <section class="row new-post">
        <div class="col-md-6 col-md-offset-3">
            <img src="/uploads/avatars/{{ Auth::user()->avatar }}" style="width:150px; height:150px; float:left; border-radius:50%; margin-right:25px;">
            <h2>{{ Auth::user()->name }}'s Profile</h2>
            <form action="/user" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" id="name">
                </div>
            </form>

            <div>
                <form enctype="multipart/form-data" action="/user" method="POST">
                    <label>Update Profile Image</label>
                    <input type="file" name="avatar">
            </div>
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    <button type="submit" class="btn btn-primary">Save Account</button>
                </form>

        </div>
    </section>
@endsection