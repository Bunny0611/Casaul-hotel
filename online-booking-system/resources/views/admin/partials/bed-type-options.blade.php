@php($bedOptions = [
    '1 Single Bed',
    '2 Single Beds',
    '1 Double Bed',
    '2 Double Beds',
    '1 Double + 1 Single',
    '1 Queen Bed',
    '2 Queen Beds',
    '1 King Bed',
    '2 King Beds',
    '1 King + 1 Single',
])
<option value="">Select Bed Type</option>
@foreach($bedOptions as $bedOption)
    <option value="{{ $bedOption }}" @selected($selectedBedType === $bedOption)>{{ $bedOption }}</option>
@endforeach
<option value="__custom__" @selected($selectedBedType && !in_array($selectedBedType, $bedOptions, true))>Other / Custom</option>
