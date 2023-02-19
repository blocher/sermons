<div>
    <header>
        <h1>Sermons</h1>
    </header>
    <main>
        <table>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Date
                </th>
                <th>
                    Location
                </th>
                <th>
                    Feast
                </th>
            </tr>
            @foreach ($sermons as $sermon)
                <tr>
                    <td>
                        {{ $sermon->id }}
                    </td>
                    <td>
                        @if ($sermon->delivered_on)
                            {{ $sermon->delivered_on }}
                        @else
                            ??
                        @endif
                    </td>
                    <td>
                        @if ($sermon->location)
                            {{ $sermon->location }}
                        @else
                            ??
                        @endif
                    </td>
                    <td>
                        @if ($sermon->feast)
                            {{ $sermon->feast }}
                        @else
                            ??
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        {% endfor %}
    </main>
</div>
