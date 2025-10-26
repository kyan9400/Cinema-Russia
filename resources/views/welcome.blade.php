<!doctype html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
  <title>Кино-Россия — Популярные фильмы</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
            <style>
    :root{color-scheme:dark light}
    body{margin:24px;background:#0f172a;color:#e2e8f0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
    h1{margin:0 0 6px}
    .sub{color:#94a3b8;margin:0 0 16px;font-size:13px}
    .toolbar{display:flex;gap:10px;align-items:center;margin-bottom:10px}
    input[type=text]{background:#0b1220;border:1px solid #334155;border-radius:10px;color:#e2e8f0;padding:8px 10px;width:320px;outline:none}
    button,.chip{background:#1f2937;border:1px solid #334155;color:#e2e8f0;border-radius:10px;padding:8px 12px;cursor:pointer}
    button:hover,.chip:hover{background:#2b3648}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
    .card{background:#0b1220;border:1px solid #1f2937;border-radius:14px;overflow:hidden;box-shadow:0 0 0 1px rgba(255,255,255,.02) inset}
    .card img{width:100%;height:360px;object-fit:cover;display:block;background:#0a0f1a}
    .p{padding:12px 14px}
    .title{font-weight:600;margin:0 0 6px;color:#f8fafc}
    .muted{color:#94a3b8;font-size:12px}
    .row{display:flex;justify-content:space-between;align-items:center;margin-top:8px;gap:8px}
    .btn{padding:6px 10px;text-decoration:none;border:1px solid #334155;border-radius:10px;color:#e2e8f0}
    .btn:hover{background:#1f2937}
    .note{margin:10px 0 16px;color:#94a3b8}
    .pager{display:flex;gap:8px;margin-top:16px}
    .empty{color:#94a3b8;padding:18px}
    .favorite-btn{background:#ef4444;border:none;color:white;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:16px;position:absolute;top:8px;right:8px;opacity:0.8}
    .favorite-btn:hover{opacity:1}
    .favorite-btn.active{background:#22c55e}
    .poster-container{position:relative}
    .rating-badge{position:absolute;top:8px;left:8px;background:rgba(0,0,0,0.8);color:white;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:bold}
    .card-actions{display:flex;gap:0.5rem;margin-top:0.75rem}
    .kinopoisk-btn{background:var(--bg-secondary);color:var(--text-primary);border:1px solid var(--border);border-radius:10px;padding:0.75rem 1rem;font-size:0.875rem;font-weight:500;text-decoration:none;transition:all 0.2s ease}
    .kinopoisk-btn:hover{background:var(--bg-hover);border-color:var(--accent);transform:translateY(-1px)}
    .nav{display:flex;gap:12px;margin-bottom:20px}
    .nav a{color:#94a3b8;text-decoration:none;padding:8px 12px;border-radius:8px}
    .nav a:hover{background:#1f2937;color:#e2e8f0}
    .nav a.active{background:#334155;color:#e2e8f0}
            </style>
    </head>
<body>
  <h1>Кино-Россия</h1>
  <p class="sub">Популярные фильмы и сериалы</p>

  <div class="nav">
    <a href="/" class="active">Популярные</a>
    <a href="/movies">Все фильмы A–Z</a>
    <a href="/favorites">Избранное</a>
  </div>

  <div class="toolbar">
    <input id="q" type="text" placeholder="Поиск фильмов..." />
    <button id="btnSearch">Поиск</button>
  </div>

  <div class="note" id="note"></div>
  <div id="grid" class="grid"></div>
  <div class="pager">
    <button id="btnPrev">← Предыдущая</button>
    <button id="btnNext">Следующая →</button>
                </div>

  <script>
    let currentPage = 1;
    let totalPages = 1;
    let lastQuery = "";

    function setNote(text){ document.getElementById('note').textContent = text || ''; }
    function gridLoading(){ document.getElementById('grid').innerHTML = '<div class="empty">Загрузка...</div>'; }
    function showError(e){
      const m = (e && e.message) ? e.message : (e?.status ? `Ошибка ${e.status}` : 'Ошибка');
      setNote(m);
      document.getElementById('grid').innerHTML = `<div class="empty">${m}</div>`;
    }
    async function parseJsonSafe(res){
      let txt = await res.text();
      txt = txt.replace(/^\uFEFF/, '').replace(/^\s*,\s*/, '');
      return JSON.parse(txt);
    }
    function movieCard(m){
      const year = m.year || '';
      const rating = (m.rating ?? '') || '—';
      const genres = Array.isArray(m.genres) ? m.genres.join(', ') : (m.genres || '');
      const poster = m.poster || '';
      const countries = Array.isArray(m.countries) ? m.countries.join(', ') : (m.countries || '');
      
      return `
        <div class="card">
          <div class="poster-container">
            <img class="poster" src="${poster}" alt="${m.name}" loading="lazy">
            <div class="rating-badge">${rating}</div>
            <button class="favorite-btn" onclick="toggleFavorite('${m.id}')" title="Добавить в избранное">
              ♥
            </button>
          </div>
          <div class="p">
            <div class="title">${m.name}</div>
            <div class="muted">${year ? year + ' • ' : ''}Рейтинг: ${rating}</div>
            <div class="muted">${genres}</div>
            <div class="row">
              <span class="muted" title="${countries}">${countries}</span>
              <div class="card-actions">
                <a class="btn" href="/movie/${m.id}">Подробнее</a>
                <a class="kinopoisk-btn" href="https://www.kinopoisk.ru/film/${m.id}/" target="_blank" rel="noopener" title="Открыть на Кинопоиске">Кинопоиск ↗</a>
              </div>
                </div>
        </div>
        </div>`;
    }
    function renderMovies(list, label){
      if (!list || list.length === 0){
        document.getElementById('grid').innerHTML = '<div class="empty">Фильмы не найдены.</div>';
        setNote(label);
        return;
      }
      document.getElementById('grid').innerHTML = list.map(movieCard).join('');
      setNote(label);
    }

    async function fetchPopular(page=1){
      gridLoading();
      const res = await fetch(`/api/movies/popular?page=${page}`);
      const data = await parseJsonSafe(res);
      currentPage = data.page || page;
      totalPages = data.totalPages || 1;
      return data.movies || [];
    }
    async function fetchSearch(q, page=1){
      gridLoading();
      const res = await fetch(`/api/movies/search?q=${encodeURIComponent(q)}&page=${page}`);
      const data = await parseJsonSafe(res);
      currentPage = data.page || page;
      totalPages = data.totalPages || 1;
      return data.movies || [];
    }

    async function showPopular(){
      try{
        lastQuery = "";
        const movies = await fetchPopular(currentPage);
        renderMovies(movies, `Популярные фильмы — страница ${currentPage} из ${totalPages}`);
      }catch(e){ showError(e); }
    }
    async function doSearch(){
      const q = document.getElementById('q').value.trim();
      if (!q){ return showPopular(); }
      try{
        lastQuery = q;
        currentPage = 1;
        const movies = await fetchSearch(q, 1);
        renderMovies(movies, `Поиск: "${q}" — ${movies.length} результатов`);
      }catch(e){ showError(e); }
    }

    async function toggleFavorite(movieId){
      try{
        const res = await fetch(`/api/favorites/check/${movieId}`);
        const data = await parseJsonSafe(res);
        
        if (data.is_favorite) {
          await fetch(`/api/favorites/${movieId}`, { method: 'DELETE' });
        } else {
          await fetch(`/api/favorites/${movieId}`, { method: 'POST' });
        }
        
        const btn = event.target;
        btn.classList.toggle('active');
        btn.title = data.is_favorite ? 'Добавить в избранное' : 'Удалить из избранного';
      }catch(e){
        console.error('Error toggling favorite:', e);
      }
    }

    document.getElementById('btnPrev').onclick = () => {
      if (currentPage > 1) {
        currentPage--;
        if (lastQuery) doSearch(); else showPopular();
      }
    };
    document.getElementById('btnNext').onclick = () => {
      if (currentPage < totalPages) {
        currentPage++;
        if (lastQuery) doSearch(); else showPopular();
      }
    };

    document.getElementById('btnSearch').onclick = doSearch;
    document.getElementById('q').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') doSearch();
    });

    showPopular();
  </script>
    </body>
</html>