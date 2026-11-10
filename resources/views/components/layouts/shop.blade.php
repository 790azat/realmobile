@props(['title' => null, 'description' => null, 'canonical' => null, 'ogImage' => null])
@include('layouts.app', ['slot' => $slot, 'title' => $title, 'description' => $description, 'canonical' => $canonical, 'ogImage' => $ogImage])
