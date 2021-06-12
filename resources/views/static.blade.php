@extends('user.layout.app')

@section('content')
<div class="row gray-section no-margin">
    <div class="container">
        <div class="content-block">
            <h2>{{ $page->title }}</h2>
            <div class="title-divider"></div>
            <p><?php echo html_entity_decode($page->description) ?></p>
        </div>
    </div>
</div>
@endsection