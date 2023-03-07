<div>
    <h1 class="ui header">Sermons</h1>

    <div class="ui stackable three cards">
        @foreach ($sermons as $sermon)
            <div class="card link-card" wire:click="goToSermon({{ $sermon->id }})">
                <div class="content">
                    <div class="meta">{{ $sermon->delivered_on->format("F j, Y") }}</div>
                    <div class="header">{{ $sermon->title }}</div>
                    <div class="meta">{{ $sermon->feast->name }}</div>
                    <div class="description">
                        @foreach ($sermon->readings as $reading)

                            <small><strong>{{ $reading->passage }}:</strong> {{ $reading->headings }}&nbsp;&nbsp;
                            </small>

                        @endforeach
                    </div>
                </div>
                <div class="extra content">
                    <i class="church icon"></i>
                    {{ $sermon->location->displayName }}


                </div>
            </div>
        @endforeach
    </div>


    <div class="overflow-x-auto">
        <table class="table w-full">
            @foreach ($sermons as $sermon)
                <tr wire:click="goToSermon({{ $sermon->id }})" class="hover cursor-pointer">
                    <td class="border px-4 py-2">{{ $sermon->title }}</td>
                    <td class="border px-4 py-2">{{ $sermon->delivered_on->format("j M, Y")}}</td>
                    <td class="border px-4 py-2">{{ $sermon->feast->name }}</td>
                    <td class="border px-4 py-2">{{ $sermon->location->displayName }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
