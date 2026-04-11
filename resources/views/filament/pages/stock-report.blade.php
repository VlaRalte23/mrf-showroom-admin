<x-filament::page>

    <div class="w-full overflow-x-auto">

        <table style="width:100%; border-collapse: collapse; font-size: 0.875rem;">

            <thead>
                <tr style="background-color: #374151; color: white;">
                    <th style="padding: 12px 24px; text-align: left; border: 1px solid #4B5563;">
                        Tyre
                    </th>
                    @foreach ($showrooms as $showroom)
                        <th style="padding: 12px 24px; text-align: center; border: 1px solid #4B5563;">
                            {{ $showroom->name }}
                        </th>
                    @endforeach
                    <th style="padding: 12px 24px; text-align: center; border: 1px solid #4B5563;">
                        Total
                    </th>
                </tr>
            </thead>

            <tbody>

                @foreach ($stocks as $tyreId => $items)
                    @php
                        $stockByShowroom = [];

                        foreach ($items as $item) {
                            $stockByShowroom[$item->showroom_id] = $item->stock;
                        }

                        $total = array_sum($stockByShowroom);
                    @endphp

                    <tr style="border: 1px solid #4B5563;">
                        <td style="padding: 12px 24px; border: 1px solid #4B5563;">
                            {{ $items->first()->tyre_size }} {{ $items->first()->pattern }} -
                            ₹{{ number_format($items->first()->price, 2) }}
                        </td>
                        @foreach ($showrooms as $showroom)
                            <td style="padding: 12px 24px; text-align: center; border: 1px solid #4B5563;">
                                {{ $stockByShowroom[$showroom->id] ?? 0 }}
                            </td>
                        @endforeach
                        <td
                            style="padding: 12px 24px; text-align: center; font-weight: bold; border: 1px solid #4B5563;">
                            {{ $total }}
                        </td>
                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</x-filament::page>
