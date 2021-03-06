@extends('layouts.default')
@section('title', Auth::user()->name.'的消息中心')

@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="text-center">
                    <h4>您好&nbsp;<strong>{{Auth::user()->name}}</strong>！</h4>
                    <h5 class="text-center">
                        您共有
                        新公共通知{{ Auth::user()->unread_public_notices() }}条，
                        新消息{{ Auth::user()->message_reminders }}条，
                        新跟帖{{ Auth::user()->post_reminders }}条，
                        新回复{{ Auth::user()->reply_reminders }}条，
                        新点评{{ Auth::user()->postcomment_reminders }}条，
                        新赞赏{{ Auth::user()->upvote_reminders }}条，
                        新系统消息{{ Auth::user()->system_reminders }}条，
                    </h5>
                    @include('messages._receive_stranger_messages_button')
                    @include('messages._receive_upvote_reminders_button')
                </div>
                <ul class="nav nav-tabs">
                    <li role="presentation" class = "active"><a href="{{ route('messages.unread') }}">未读</a></li>
                    <li role="presentation"><a href="{{ route('messages.index') }}">全部</a></li>
                    <li role="presentation"><a href="{{ route('messages.messagebox') }}">信箱</a></li>
                    <li role="presentation" class="pull-right"><a class="btn btn-success sosad-button" href="{{ route('messages.clear') }}">清理未读</a></a></li>
                </ul>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>公共通知：</h4>
                @include('messages._public_notices')
                @if($public_notices->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('messages.public_notices') }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>个人信息：</h4>
                @include('messages._messages')
                @if($messages->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('messages.messages') }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>未读主题跟帖：</h4>
                @include('messages._posts')
                @if($posts->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('messages.posts') }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>未读回帖讨论：</h4>
                @include('messages._replies')
                @if($replies->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('messages.replies') }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>未读帖子点评：</h4>
                @include('messages._postcomments')
                @if($posts->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('messages.postcomments') }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>未读赞赏：</h4>
                @include('messages._upvotes')
                @if($upvotes->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('messages.upvotes') }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>未读系统消息：</h4>
                @include('messages._system_reminders')
                @if($system_reminders->hasMorePages())
                <div class="text-center">
                    <a href="{{ route('questions.index', Auth::id()) }}">查看全部</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
