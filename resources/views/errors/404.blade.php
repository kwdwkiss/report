@extends('errors::layout')

@section('title', 'Page Not Found')

@section('message')
    您访问的页面不存在！将在1秒后跳转首页，或点击<a href="/">跳转</a>
    <script>
        setTimeout(function () {
            location = '/';
        }, 1000)
    </script>
@endsection
