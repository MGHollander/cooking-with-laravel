@if(session()->has('status') || session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div class="container flash-messages">
        @if(session()->has('status'))
            <div class="flash-message flash-status">
                {{ session('status') }}
            </div>
        @endif

        @if(session()->has('success'))
            <div class="flash-message flash-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="flash-message flash-error">
                {{ session('error') }}
            </div>
        @endif

        @if(session()->has('warning'))
            <div class="flash-message flash-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if(session()->has('info'))
            <div class="flash-message flash-info">
                {{ session('info') }}
            </div>
        @endif
    </div>
@endif