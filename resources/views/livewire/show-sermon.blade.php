<div>
    <h1>{{ $sermon->title }}</h1>
    <div class="grid gap-4 not-prose grid-cols-1 md:grid-cols-2">
        <div>
            <div class="card bg-base-100 shadow-xl h-full">
                <div class="card-body">
                    <h2 class="card-title leading-snug">{{ $sermon->delivered_on->format("l F j, Y") }}</h2>

                    <div class="avatar inline-block" style="display:inline-block !important;">
                        <div class="w-12 rounded-full inline-block mr-3" style="display:inline-block !important;">
                            <img src="{{ Storage::url("public/elizabeth.jpeg") }}"/>
                        </div>
                        The Reverend Elizabeth Locher
                    </div>
                    <p class="text-sm">{{ $sermon->feast->name }}<br/>{{ $sermon->location->displayName }}</p>
                </div>
            </div>
        </div>

        <div>
            <div class="card bg-base-100 shadow-xl h-full">
                <div class="card-body">
                    <h2 class="card-title">Readings</h2>
                    @foreach ($sermon->readingsArray as $reading)
                        <p>{{ $reading }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="text-justify">{!! $sermon->sermon_markup  !!}</div>

</div>
