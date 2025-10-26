<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>Кино-Россия - Детали фильма</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{color-scheme:dark light}
    body{margin:0;background:#0b1020;color:#e6edf3;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    .container{max-width:1200px;margin:0 auto;padding:32px 28px}
    a{color:#9bd2ff}
    .back{display:inline-block;margin-bottom:18px;padding:8px 12px;border:1px solid #2b3a55;border-radius:10px;color:#e6edf3;text-decoration:none}
    .layout{display:grid;grid-template-columns:320px 1fr;gap:32px}
    .poster{border-radius:16px;overflow:hidden;border:1px solid #1f2a44;background:#0a0f1a}
    .poster img{width:100%;display:block}
    h1{margin:0 0 12px;font-size:32px;line-height:1.2}
    .meta{display:flex;flex-wrap:wrap;gap:18px;color:#9fb0c6;margin-bottom:16px}
    .meta-item{display:flex;flex-direction:column;gap:4px}
    .meta-label{font-size:12px;color:#6b7280}
    .meta-value{font-weight:500;color:#e6edf3}
    .chip{display:inline-block;border:1px solid #334155;border-radius:999px;padding:6px 12px;margin:4px 8px 4px 0;font-size:13px;color:#cbd5e1}
    .lead{color:#cbd5e1;line-height:1.7;margin-top:20px;font-size:16px}
    .actions{margin-top:24px;display:flex;gap:12px;flex-wrap:wrap}
    .btn{display:inline-block;border:1px solid #334155;border-radius:10px;padding:10px 16px;color:#e6edf3;text-decoration:none;transition:all 0.2s}
    .btn:hover{background:#162036;border-color:#475569}
    .btn-primary{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;color:white}
    .btn-primary:hover{background:linear-gradient(135deg,#5a67d8 0%,#6b46c1 100%)}
    .cast{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:24px}
    .cast-item{background:#1a1f2e;border-radius:12px;padding:16px;border:1px solid #2b3a55}
    .cast-name{font-weight:600;margin-bottom:4px}
    .cast-role{color:#9fb0c6;font-size:14px}
    .loading{text-align:center;padding:40px;color:#9fb0c6}
    .error{background:#2d1b1b;border:1px solid #5c2626;border-radius:12px;padding:20px;color:#fca5a5}
    .rating{display:inline-flex;align-items:center;gap:8px;background:#1a1f2e;padding:8px 12px;border-radius:8px;border:1px solid #334155}
    .rating-value{font-size:18px;font-weight:600;color:#fbbf24}
    @media (max-width:768px){
      .layout{grid-template-columns:1fr;gap:24px}
      .poster{max-width:280px;margin:0 auto}
      .meta{flex-direction:column;gap:12px}
      .actions{flex-direction:column}
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="/movies" class="back">← Back</a>
    <div id="app"></div>
  </div>

  <script>
    let isFavorite = false;
    let movieId = null;

    async function parseJsonSafe(res){
      let txt = await res.text();
      txt = txt.replace(/^\uFEFF/, '').replace(/^\s*,\s*/, '');
      return JSON.parse(txt);
    }

    async function checkFavoriteStatus() {
      if (!movieId) return;
      try {
        const res = await fetch(`/api/favorites/check/${movieId}`);
        const data = await parseJsonSafe(res);
        isFavorite = data.is_favorite;
        updateFavoriteButton();
      } catch (e) {
        console.error('Error checking favorite status:', e);
      }
    }

    async function toggleFavorite() {
      if (!movieId) return;
      
      try {
        const method = isFavorite ? 'DELETE' : 'POST';
        const res = await fetch(`/api/favorites/${movieId}`, { method });
        const data = await parseJsonSafe(res);
        
        isFavorite = !isFavorite;
        updateFavoriteButton();
      } catch (e) {
        console.error('Error toggling favorite:', e);
      }
    }

    function updateFavoriteButton() {
      const btn = document.getElementById('favorite-btn');
      if (btn) {
        btn.textContent = isFavorite ? '❤️ В избранном' : '🤍 Добавить в избранное';
        btn.className = isFavorite ? 'btn btn-primary' : 'btn';
      }
    }

    async function boot(){
      const id = location.pathname.split('/').pop();
      movieId = id;
      
      document.getElementById('app').innerHTML = '<div class="loading">Загрузка...</div>';
      
      try {
        const res = await fetch(`/api/movies/${id}`);
        const m = await parseJsonSafe(res);

        const genres = (m.genres||[]).map(g=>g.genre).join(', ');
        const chips = (m.genres||[]).map(g=>`<span class="chip">${g.genre}</span>`).join('');
        
        const countries = (m.countries||[]).map(c=>c.country).join(', ');
        const rating = m.ratingKinopoisk || m.rating || '—';
        const year = m.year || '—';
        const length = m.filmLength ? `${m.filmLength} мин` : '—';
        const description = m.description || m.shortDescription || 'Описание недоступно';

        document.getElementById('app').innerHTML = `
          <div class="layout">
            <div class="poster">
              <img src="${m.posterUrlPreview||m.posterUrl||'/placeholder.jpg'}" alt="${m.nameRu||m.nameOriginal||'Фильм'}" onerror="this.src='/placeholder.jpg'">
            </div>
            <div>
              <h1>${m.nameRu||m.nameOriginal||'Фильм'}</h1>
              <div class="meta">
                <div class="meta-item">
                  <div class="meta-label">Год</div>
                  <div class="meta-value">${year}</div>
                </div>
                <div class="meta-item">
                  <div class="meta-label">Страна</div>
                  <div class="meta-value">${countries || '—'}</div>
                </div>
                <div class="meta-item">
                  <div class="meta-label">Рейтинг</div>
                  <div class="rating">
                    <span class="rating-value">${rating}</span>
                    <span>⭐</span>
                  </div>
                </div>
                <div class="meta-item">
                  <div class="meta-label">Длительность</div>
                  <div class="meta-value">${length}</div>
                </div>
              </div>
              <div>${chips}</div>
              <p class="lead">${description}</p>
              <div class="actions">
                <button id="favorite-btn" class="btn" onclick="toggleFavorite()">🤍 Добавить в избранное</button>
                <a class="btn" href="https://www.kinopoisk.ru/film/${movieId}/" target="_blank" rel="noopener">Открыть на Кинопоиске ↗</a>
              </div>
            </div>
          </div>`;

        await checkFavoriteStatus();
      } catch (e) {
        document.getElementById('app').innerHTML = `
          <div class="error">
            <h3>Ошибка загрузки</h3>
            <p>Не удалось загрузить информацию о фильме. Попробуйте обновить страницу.</p>
          </div>`;
      }
    }
    
    boot();
  </script>
</body>
</html>