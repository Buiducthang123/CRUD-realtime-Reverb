@extends('layouts.app');

@section('content')
    <form id="postForm">
        @csrf
        <label for="title">Tiêu đề:</label>
        <input type="text" id="title" name="title" style="width: 500px; padding: 5px;">

        <br>

        <label for="content">Nội dung:</label>
        <textarea id="content" name="content" style="width: 500px; height: 200px; padding: 5px;"></textarea>

        <br>

        <button type="submit" style="padding: 5px 10px; background-color: #000; color: #fff;">Thêm Post</button>
    </form>

@endsection
