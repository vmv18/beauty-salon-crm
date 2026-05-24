@props(['message', 'dismissible' => true])

@if($message)
    <div class="container mt-4">
        <div id="warning-message" class="notification is-warning is-light">
            @if($dismissible)
                <button class="delete" onclick="this.parentElement.remove()"></button>
            @endif
            {{ $message }}
        </div>
    </div>
@endif
