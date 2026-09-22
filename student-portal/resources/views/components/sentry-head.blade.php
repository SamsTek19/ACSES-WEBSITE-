{{-- Sentry Loader & Session Replay --}}
<script
  src="https://js-de.sentry-cdn.com/95f542f0c06626786f261cafb940c7ce.min.js"
  crossorigin="anonymous"
></script>
<script>
  Sentry.onLoad(function() {
    Sentry.init({
      integrations: [
        Sentry.replayIntegration(),
      ],
      // Session Replay rates
      replaysSessionSampleRate: {{ app()->environment('local') ? '1.0' : '0.1' }},
      replaysOnErrorSampleRate: 1.0,
    });
  });
</script>
