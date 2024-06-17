<div>
    @php
        
        $filePath = \Illuminate\Support\Facades\Storage::disk('public')->url($getState());
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
    @endphp

    @if(in_array($fileExtension, $imageExtensions))
        <img src="{{ $filePath }}" alt="Image" style="height: 100px;">
    @else
        <a href="{{ $filePath }}" target="_blank">
            <img src="{{ asset('path/to/icon-' . $fileExtension . '.png') }}" alt="{{ $fileExtension }} icon" style="height: 100px;">
        </a>
    @endif
</div>
