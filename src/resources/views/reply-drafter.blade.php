<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reply Drafter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { color-scheme: light dark; }
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 2rem auto; max-width: 820px; padding: 0 1rem; }
    .card { border: 1px solid rgba(0,0,0,0.1); border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem; }
    .row { display: grid; gap: .75rem; grid-template-columns: 1fr 200px; align-items: start; }
    .row .full { grid-column: 1 / -1; }
    textarea { width: 100%; height: 180px; }
    select, input[type="number"], textarea { padding: .5rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.2); }
    button { padding: .6rem 1rem; border-radius: 10px; border: 0; cursor: pointer; }
    .btn { background: #2563eb; color: white; }
    .errors { background: #fee2e2; color: #7f1d1d; padding: .75rem; border-radius: 10px; margin-bottom: 1rem; }
    pre { white-space: pre-wrap; word-wrap: break-word; }
    small.mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; opacity: .7; }
  </style>
</head>
<body>
  <h1>Reply Drafter</h1>
  <p class="card">
    Paste a customer message and choose a tone. The app will generate a concise, polite reply. <br>
    <small class="mono">No RAG yet; the model won’t assert company policy.</small>
  </p>

  @if ($errors->any())
    <div class="errors">
      <strong>There were issues:</strong>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form class="card" action="{{ route('reply.draft') }}" method="post">
    @csrf
    <div class="row">
      <div class="full">
        <label for="customer_email"><strong>Customer message</strong></label>
        <textarea id="customer_email" name="customer_email" required>{{ old('customer_email', $input['customer_email'] ?? '') }}</textarea>
      </div>

      <div>
        <label for="tone"><strong>Tone</strong></label>
        <select id="tone" name="tone" required>
          @php $toneOld = old('tone', $input['tone'] ?? 'friendly'); @endphp
          <option value="friendly" {{ $toneOld==='friendly' ? 'selected' : '' }}>Friendly</option>
          <option value="formal"   {{ $toneOld==='formal'   ? 'selected' : '' }}>Formal</option>
          <option value="neutral"  {{ $toneOld==='neutral'  ? 'selected' : '' }}>Neutral</option>
        </select>
      </div>

      <div>
        <label for="max_words"><strong>Max words</strong></label>
        <input id="max_words" type="number" name="max_words" min="10" max="300"
               value="{{ old('max_words', $input['max_words'] ?? 120) }}">
      </div>

      <div class="full" style="display:flex; gap:.5rem;">
        <button class="btn" type="submit">Draft reply</button>
        <a href="{{ route('reply.show') }}"><button type="button">Reset</button></a>
      </div>
    </div>
  </form>

  @if (!empty($reply))
    <div class="card">
      <h2>Drafted reply</h2>
      <pre>{{ $reply }}</pre>
    </div>
  @endif

  <p><small class="mono">Model: {{ env('OPENAI_MODEL', 'gpt-4o-mini') }}</small></p>
</body>
</html>
