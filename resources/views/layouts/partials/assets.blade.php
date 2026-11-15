@unless (app()->environment('testing'))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endunless
