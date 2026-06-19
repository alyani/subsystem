<x-form.card>
    <div class="text-center">
        <img src="{{ $user->avatarSID
            ? route('storage.download', ['type' => 'thumbnail', 'SID' => $user->avatarSID])
            : url('vendor/subsystem/images/user.png') }}" 
            class="rounded-circle img-fluid"
            style="width: 150px;">
        @php $name = $user->name . ' ' . $user->family; @endphp
        <h5 class="my-3">{{ trim($name) ?: st('User') }}</h5>
        <p class="text-muted mb-1">ID: #{{ $user->id }}</p>
        <p class="text-muted mb-4">{{st('Nickname')}}: {{ $user->nickname ?? '---' }}</p>
        <div class="d-flex justify-content-center mb-2">
            @php
                $userStatusClass = match($user->status->value) {
                    'active' => 'bg-success',
                    'waitingForSetProfile' => 'bg-warning',
                    'banned' => 'bg-danger',
                    default => ''
                }
            @endphp
            <span class="badge {{$userStatusClass}}">
                {{$user->status->getTranslate()}}
            </span>
        </div>
    </div>
</x-form.card>