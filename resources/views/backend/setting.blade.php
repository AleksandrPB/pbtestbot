@extends('backend.layouts.app')

@section('content')
    <div class="container">
        <form action="{{ route('admin.setting.store') }}" method="post">
            @csrf
            {{--First setting - URL webhook (server URL for telegram requests)--}}
            <div class="form-group">
                <label>URL Callback for Telegram Bot</label>
                <div class="input-group-btn">
                    <div class="input-group-btn">
                        <button type="button"
                                class="btn btn-default dropdown-toggle"
                                data-toogle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">Action
                                <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="document.getElementById('url_callback_bot') . value = '{{ url('') }}'">Insert URL</a></li>
                            <li><a href="#">Send URL</a></li>
                            <li><a href="#">Get Info</a></li>
                            <li><a href="#"></a></li>
                        </ul>
                    </div>
                {{--Field with settings--}}
                <input type="url"
                       class="form-control"
                       id="url_callback_bot"
                       name="url_callback_bot"
                       value="{{ $url_callback_bot ?? '' or '' }}">
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
@endsection
