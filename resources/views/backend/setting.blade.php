@extends('backend.layouts.app')

@section('content')
    <div class="container">
        @if(Session::has('status'))
            <div class="alert-info">
                <span>{{ Session::get('status') }}</span>
            </div>
        @endif
        <form action="{{ route('admin.setting.store') }}" method="post">
            @csrf
            {{--First setting - URL webhook (server URL for telegram requests)--}}
            <div class="form-group">
                <label>URL Callback for Telegram Bot</label>
                <div class="input-group">
                    <div class="input-group-btn">
                        <ul class="nav nav-pills">
                            <button type="button"
                                    class="btn btn-default dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                            >
                                Action
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="#"
                                       onclick="document.getElementById('url_callback_bot') . value = '{{ url('') }}'">Insert
                                        URL</a></li>
                                <li><a href="#"
                                       onclick="event.preventDefault(); document.getElementById('setwebhook').submit()">Send URL</a></li>
                                <li><a href="#"
                                       onclick="event.preventDefault(); document.getElementById('getwebhookinfo').submit()">Get Info</a></li>
                                <li><a href="#"></a></li>
                            </ul>
                        </ul>
                    </div>
                    {{--                                    Field with settings--}}
                    <input type="url"
                           class="form-control"
                           id="url_callback_bot"
                           name="url_callback_bot"
                           value="{{ $url_callback_bot ?? ''}}">
                </div>
            </div>
            {{--            <div class="form-group">--}}
            {{--                <label>Name of the site for main page</label>--}}
            {{--                <div class="input-group">--}}
            {{--                    <input type="text"--}}
            {{--                           class="form-control"--}}
            {{--                           name="site_name"--}}
            {{--                           value="{{ $site_name ?? ''}}">--}}
            {{--                </div>--}}
            {{--            </div>--}}
            <button class="btn btn-primary" type="submit">Save</button>
        </form>

        <form id="setwebhook" action="{{ route('admin.setting.setwebhook') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="url" value="{{ $url_callback_bot ?? '' }}">
        </form>

        <form id="getwebhookinfo" action="{{ route('admin.setting.getwebhookinfo') }}" method="POST" style="display: none;">
            @csrf
        </form>


    </div>
@endsection
