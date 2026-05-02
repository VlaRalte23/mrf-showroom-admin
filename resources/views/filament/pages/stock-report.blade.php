<x-filament::page>

    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; align-items: end;">
        <div style="min-width: 260px; flex: 1;">
            <label style="display: block; margin-bottom: 6px; font-size: 0.875rem;">Search Tyre</label>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search by size, pattern, or category"
                style="width: 100%; border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px; font-size: 0.875rem;" />
        </div>

        @if ($categoryFilter !== '')
            <button wire:click="$set('categoryFilter', '')"
                style="padding: 10px 14px; border: 1px solid #D1D5DB; border-radius: 8px; background: #FFFFFF; font-size: 0.875rem; font-weight: 600;">
                Clear Category
            </button>
        @endif
    </div>

    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; margin-bottom: 16px;">
        <button wire:click="$set('categoryFilter', '')"
            style="text-align: left; border: 1px solid {{ $categoryFilter === '' ? '#111827' : '#D1D5DB' }}; border-radius: 12px; padding: 12px; background: {{ $categoryFilter === '' ? '#F3F4F6' : '#FFFFFF' }};">
            <div style="font-weight: 700; font-size: 0.875rem;">All Categories</div>
            <div style="margin-top: 4px; color: #4B5563; font-size: 0.8125rem;">
                {{ collect($stocks)->sum(fn($tyres) => $tyres->count()) }} tyre types
            </div>
        </button>

        @foreach ($this->categoryOptions as $category)
            <button wire:click="$set('categoryFilter', @js($category))"
                style="text-align: left; border: 1px solid {{ $categoryFilter === $category ? '#111827' : '#D1D5DB' }}; border-radius: 12px; padding: 12px; background: {{ $categoryFilter === $category ? '#F3F4F6' : '#FFFFFF' }};">
                <div style="font-weight: 700; font-size: 0.875rem;">{{ $category }}</div>
                <div style="margin-top: 4px; color: #4B5563; font-size: 0.8125rem;">
                    {{ $this->categoryCounts->get($category, 0) }} tyre types
                </div>
            </button>
        @endforeach
    </div>

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

                @forelse ($this->filteredStocks as $categoryName => $tyres)

                    <tr style="background-color: #F3F4F6; border: 1px solid #E5E7EB;">
                        <td colspan="{{ $showrooms->count() + 2 }}"
                            style="padding: 10px 24px; font-weight: 700; color: #111827; border: 1px solid #E5E7EB;">
                            Category: {{ $categoryName }}
                        </td>
                    </tr>

                    @foreach ($tyres as $tyreId => $items)
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

                @empty
                    <tr>
                        <td colspan="{{ $showrooms->count() + 2 }}"
                            style="padding: 12px 24px; text-align: center; border: 1px solid #4B5563;">
                            No stock rows found for the selected filters.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</x-filament::page>
