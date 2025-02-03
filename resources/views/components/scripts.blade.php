@once
    <script>
        window.__sonar = {
            queue: [],
            processing: false,
            impressed: new Set(),
            hovered: new Set(),
            observer: null,
            queueTimeout: null,
        };
    </script>
    <script src="{{ asset('vendor/laravel-sonar/sonar.js') }}" defer></script>
@endonce 