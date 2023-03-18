<div>
    <h1 div="ui header">{{ $sermon->title }}</h1>

    <div class="ui fluid raised card">
        <div class="content">
            <div class="ui grid two column">
                <div class="column">
                    <h3>{{ $sermon->delivered_on->format("l F j, Y") }}</h3>
                    <p>{{ $sermon->feast->name }}</p>
                    <p><a href="{{  Storage::url($sermon->file) }}">Download MS Word</a></p>
                    <p><a href="#" id="preview">Preview MS Word</a></p>
                    <div class="fullscreen ui modal">
                        <iframe
                            style="height: 90vh; width: 100%;"
                            src="https://docs.google.com/gview?url={{  URL::to(Storage::url($sermon->file))}}&embedded=true"></iframe>
                    </div>

                </div>
                <div class="column">
                    <p><img class="ui avatar image" src="{{ Storage::url("public/elizabeth.jpeg") }}"> <strong>The
                            Rev.
                            Elizabeth Locher</strong></p>
                    <p>{{ $sermon->location->displayName }}</p>

                </div>

            </div>
            {{--            <h3>Similar Readings</h3>--}}
            {{--            @foreach ($sermon->similarReadings() as $sermon)--}}
            {{--                <p><a href="{{ route('sermon', $sermon->id) }}">{{ $sermon->title }}</a></p>--}}
            {{--            @endforeach--}}

        </div>
        {{--        <div class="content">--}}
        {{--            @foreach ($sermon->readings as $reading)--}}
        {{--                <h4 class="m-0">{{ $reading->passage }}</h4>--}}
        {{--                <p class="m-0">{{ $reading->headings }}</p>--}}
        {{--            @endforeach--}}
        {{--        </div>--}}
    </div>


    <div class="ui styled accordion fluid">
        <div class="title ">
            <i class="right dropdown icon"></i>
            <h4 style="margin-top:0;">Readings</h4>

            @foreach ($sermon->readings as $reading)
                <p><strong>{{ $reading->passage }}:</strong> {{ $reading->headings }}</p>
            @endforeach

        </div>
        <div class="content ">
            @foreach ($sermon->readings as $reading)
                <p style="margin-top: 20px;">
                    <strong>{{ $reading->passage }}</strong><br><em>{{ $reading->headings }}</em></p>
                {!! $reading->noHeadings() !!}
            @endforeach
        </div>
    </div>

    {{--    <h5>Other sermons with this reading</h5>--}}
    {{--    @foreach ($reading->other_sermons as $sermon)--}}
    {{--        <p><a href="{{ route('sermon', $sermon->id) }}">{{ $sermon->title }}</a></p>--}}
    {{--    @endforeach--}}

    <h5>Similar sermons</h5>
    @foreach ($sermon->getSimilarSermons() as $similar_sermon)
        <p>
            <a href="{{ route('sermon', $similar_sermon->id) }}">{{ $similar_sermon->title }}
                | {{ $similar_sermon->location->display_name }}
                | {{ $similar_sermon->delivered_on->format("l F j,Y")}}</a>
            | {{ $similar_sermon->percent_match }}
        </p>
    @endforeach


    <div class="ui piled segment sermon">{!! $sermon->sermon_markup  !!}</div>

</div>

<script>
    $("#preview").click(function (e) {
        e.preventDefault();
        $('.fullscreen.ui.modal').modal('show');
    });
</script>
