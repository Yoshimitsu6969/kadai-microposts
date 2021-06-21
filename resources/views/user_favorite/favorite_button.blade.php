@if (Auth::id() != $user->id)
    @if (Auth::user()->is_favorites($user->id))
        {{-- アンfavoriteーボタンのフォーム --}}
        {!! Form::open(['route' => ['user.unfavorite', $user->id], 'method' => 'delete']) !!}
            {!! Form::submit('Unfavorite', ['class' => "btn btn-danger btn-block"]) !!}
        {!! Form::close() !!}
    @else
        {{-- favoriteボタンのフォーム --}}
        {!! Form::open(['route' => ['user.favorite', $user->id]]) !!}
            {!! Form::submit('Favorite', ['class' => "btn btn-primary btn-block"]) !!}
        {!! Form::close() !!}
    @endif
@endif