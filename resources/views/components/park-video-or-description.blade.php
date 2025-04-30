@props(['park', 'videoLoaded'])

@if($park->video_embed_code)
    <div class="video-frame" x-show="videoLoaded" x-transition>
        {!! $park->video_embed_code !!}
    </div>
    <button x-show="!videoLoaded"
            @click.stop="videoLoaded = true"
            class="mb-4 w-full bg-blue-600 text-white py-2 rounded-lg">
        Video laden
    </button>
@elseif($park->video_url)
    <div class="video-frame" x-show="videoLoaded" x-transition>
        @php $video = $park->video_url; @endphp
        @if(Str::contains($video, 'youtube'))
            <iframe src="https://www.youtube.com/embed/{{ Str::afterLast($video, 'v=') }}?autoplay=1&mute=1"
                    allow="autoplay; encrypted-media" allowfullscreen></iframe>
        @elseif(Str::contains($video, 'vimeo'))
            <iframe src="https://player.vimeo.com/video/{{ Str::afterLast($video, '/') }}?autoplay=1&muted=1"
                    allow="autoplay; fullscreen" allowfullscreen></iframe>
        @endif
    </div>
    <button x-show="!videoLoaded"
            @click.stop="videoLoaded = true"
            class="mb-4 w-full bg-blue-600 text-white py-2 rounded-lg">
        Video laden
    </button>
@else
    <p class="text-sm text-gray-300 mb-4">
        {!! \Str::limit($park->description, 300) !!}
    </p>
@endif
