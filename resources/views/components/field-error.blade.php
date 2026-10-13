@props(['name'])

@error($name)
    <span style="color:red;font-size:12px;">{{ $message }}</span>
@enderror