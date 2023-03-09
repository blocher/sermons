<div>
    <h1 class="ui header">Sermons</h1>
    <div class="container">
        <div class="ui fluid action input" wire:loading.class="loading">
            <input type="text" placeholder="Search..." wire:keyup="search" wire:model="search_term">
            <div class="ui button" wire:click="search">Search</div>
        </div>
    </div>

    <div style="margin: 20px 0">
        <div wire:loading>
            <h4 class="ui horizontal divider header">
                <div class="ui loader active ">

                </div>
            </h4>
        </div>
        <div wire:loading.remove>
            <h4 class="ui horizontal divider header">{{ $this->label }} ({{ $this->count }})</h4>


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
        </div>
    </div>
</div>
