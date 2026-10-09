@extends('admin.layout')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Taxes</h1>
        <p class="mt-1 text-sm text-gray-600">Configure a simulated tax for educational use in the hotel operations system.</p>
    </div>

    <div class="border-l-4 border-amber-500 bg-amber-50 p-4 text-sm text-amber-950" role="note">
        Educational simulation only. The sample 12% rate is not confirmation of the hotel's actual tax obligations.
    </div>

    <form method="POST" action="{{ route('admin.taxes.update') }}" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="taxName" class="mb-1 block text-sm font-medium text-gray-700">Tax name</label>
                <input id="taxName" name="name" value="{{ old('name', $taxSetting->name) }}" maxlength="100" required class="w-full rounded-md border border-gray-300 px-3 py-2">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="taxRate" class="mb-1 block text-sm font-medium text-gray-700">Rate (%)</label>
                <input id="taxRate" name="rate" type="number" min="0" max="100" step="0.01" value="{{ old('rate', $taxSetting->rate) }}" required class="w-full rounded-md border border-gray-300 px-3 py-2">
                @error('rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <fieldset class="space-y-3">
            <legend class="text-sm font-semibold text-gray-900">Simulation options</legend>
            <label class="flex items-start gap-3 text-sm text-gray-700">
                <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $taxSetting->enabled)) class="mt-0.5 rounded border-gray-300">
                <span>Enable simulated tax on new reservations</span>
            </label>
            <label class="flex items-start gap-3 text-sm text-gray-700">
                <input type="checkbox" name="inclusive" value="1" @checked(old('inclusive', $taxSetting->inclusive)) class="mt-0.5 rounded border-gray-300">
                <span>Prices include tax (extract tax from selected taxable charges without increasing their listed prices)</span>
            </label>
        </fieldset>

        <fieldset>
            <legend class="text-sm font-semibold text-gray-900">Applicable charge categories</legend>
            <p class="mb-3 mt-1 text-sm text-gray-600">Only checked categories are included in the simulation.</p>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach($taxCategories as $value => $label)
                    <label class="flex items-center gap-3 text-sm text-gray-700">
                        <input type="checkbox" name="categories[]" value="{{ $value }}" @checked(in_array($value, old('categories', $taxSetting->categories ?? []), true)) class="rounded border-gray-300">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('categories')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            @error('categories.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </fieldset>

        <div class="flex justify-end border-t border-gray-100 pt-4">
            <button type="submit" class="rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">Save simulation settings</button>
        </div>
    </form>
</div>
@endsection