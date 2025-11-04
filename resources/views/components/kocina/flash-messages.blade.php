@if(session()->has('status') || session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="container flash-messages">
        @if(session()->has('status'))
            <div class="flash-message flash-status">
                {{ session('status') }}
            </div>
        @endif

        @if(session()->has('success'))
            <div class="flash-message flash-success">
                {!! strip_tags(session('success'), '<a><i>') !!}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="flash-message flash-error">
                {!! strip_tags(session('error'), '<a><i>') !!}
            </div>
        @endif

        @if(session()->has('warning'))
            <div class="flash-message flash-warning">
                {!! strip_tags(session('warning'), '<a><i>') !!}
            </div>
        @endif

        @if(session()->has('info'))
            <div class="flash-message flash-info">
                {!! strip_tags(session('info'), '<a><i>') !!}
            </div>
        @endif
    </div>
@endif