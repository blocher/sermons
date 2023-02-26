<div>
    <h1>Sermons</h1>
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
