<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Кино-Россия</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --bg-primary: #0a0e1a;
      --bg-secondary: #111827;
      --bg-tertiary: #1f2937;
      --bg-card: #0f172a;
      --text-primary: #f8fafc;
      --text-secondary: #cbd5e1;
      --text-muted: #94a3b8;
      --accent: #3b82f6;
      --accent-hover: #2563eb;
      --border: #374151;
      --border-light: #4b5563;
      --success: #10b981;
      --warning: #f59e0b;
      --error: #ef4444;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, var(--bg-primary) 0%, #0f172a 50%, #1e293b 100%);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem;
    }

    .header {
      text-align: center;
      margin-bottom: 3rem;
    }

    .header h1 {
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--accent), #8b5cf6, #ec4899);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
      letter-spacing: -0.02em;
    }

    .header .subtitle {
      font-size: 1.125rem;
      color: var(--text-secondary);
      font-weight: 400;
    }

    .nav-links {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .nav-link {
      background: var(--bg-tertiary);
      border: 2px solid var(--border);
      color: var(--text-primary);
      border-radius: 12px;
      padding: 0.875rem 1.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .nav-link:hover {
      background: var(--accent);
      border-color: var(--accent);
      transform: translateY(-1px);
      box-shadow: var(--shadow-lg);
    }

    .nav-link.active {
      background: var(--accent);
      border-color: var(--accent);
    }

    .pagination-top {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .toolbar {
      display: flex;
      gap: 1rem;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .search-container {
      position: relative;
      flex: 1;
      min-width: 300px;
    }

    .search-input {
      width: 100%;
      background: var(--bg-secondary);
      border: 2px solid var(--border);
      border-radius: 12px;
      color: var(--text-primary);
      padding: 0.875rem 1rem 0.875rem 3rem;
      font-size: 1rem;
      outline: none;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      width: 20px;
      height: 20px;
    }

    .btn {
      background: var(--bg-tertiary);
      border: 2px solid var(--border);
      color: var(--text-primary);
      border-radius: 12px;
      padding: 0.875rem 1.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn:hover {
      background: var(--accent);
      border-color: var(--accent);
      transform: translateY(-1px);
      box-shadow: var(--shadow-lg);
    }

    .btn.active {
      background: var(--accent);
      border-color: var(--accent);
    }

    .btn-secondary {
      background: transparent;
      border-color: var(--border-light);
    }

    .btn-secondary:hover {
      background: var(--bg-tertiary);
      border-color: var(--border-light);
    }

    .btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    .btn:disabled:hover {
      background: var(--bg-tertiary);
      border-color: var(--border);
      transform: none;
      box-shadow: none;
    }

    .status-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding: 1rem;
      background: var(--bg-secondary);
      border-radius: 12px;
      border: 1px solid var(--border);
    }

    .status-text {
      color: var(--text-secondary);
      font-size: 0.875rem;
      font-weight: 500;
    }

    .sort-controls {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }

    .sort-select {
      background: var(--bg-tertiary);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text-primary);
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      outline: none;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .card {
      background: var(--bg-card);
      border: 2px solid var(--border);
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s ease;
      position: relative;
      box-shadow: var(--shadow);
    }

    .card:hover {
      transform: translateY(-8px);
      border-color: var(--accent);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .poster-container {
      position: relative;
      aspect-ratio: 2/3;
      overflow: hidden;
    }

    .poster {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover .poster {
      transform: scale(1.05);
    }

    .rating-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: rgba(0, 0, 0, 0.8);
      color: var(--success);
      padding: 0.5rem 0.75rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.875rem;
      backdrop-filter: blur(8px);
    }

    .favorite-btn {
      position: absolute;
      top: 1rem;
      left: 1rem;
      background: rgba(156, 163, 175, 0.9);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
      backdrop-filter: blur(8px);
    }

    .favorite-btn:hover {
      background: rgba(239, 68, 68, 0.9);
      transform: scale(1.1);
    }

    .favorite-btn.active {
      background: rgba(239, 68, 68, 0.9);
    }

    .card-content {
      padding: 1.5rem;
    }

    .movie-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      line-height: 1.3;
    }

    .movie-meta {
      color: var(--text-muted);
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }

    .genres {
      color: var(--text-secondary);
      font-size: 0.875rem;
      margin-bottom: 1rem;
      line-height: 1.4;
    }

    .card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .countries {
      color: var(--text-muted);
      font-size: 0.75rem;
      flex: 1;
    }

    .details-btn {
      background: var(--accent);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.25rem;
      font-size: 0.875rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .details-btn:hover {
      background: var(--accent-hover);
      transform: translateY(-1px);
    }

    .card-actions {
      display: flex;
      gap: 0.5rem;
      margin-top: 0.75rem;
    }

    .kinopoisk-btn {
      background: var(--bg-secondary);
      color: var(--text-primary);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .kinopoisk-btn:hover {
      background: var(--bg-hover);
      border-color: var(--accent);
      transform: translateY(-1px);
    }

    .loading {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 400px;
      font-size: 1.125rem;
      color: var(--text-secondary);
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid var(--border);
      border-top: 4px solid var(--accent);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-right: 1rem;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--text-muted);
    }

    .empty-state h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: var(--text-secondary);
    }

    .empty-state p {
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .pagination {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 3rem;
    }

    .error-state {
      background: rgba(239, 68, 68, 0.1);
      border: 2px solid var(--error);
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      color: var(--error);
    }

    .error-state h3 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }

      .header h1 {
        font-size: 2rem;
      }

      .toolbar {
        flex-direction: column;
        align-items: stretch;
      }

      .search-container {
        min-width: auto;
      }

      .grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Кино-Россия</h1>
      <p class="subtitle">Просматривайте каталог фильмов. Сортировка по названию, рейтингу или году.</p>
    </div>

    <div class="nav-links">
      <a href="/" class="nav-link active">🏠 Главная</a>
      <a href="/movies" class="nav-link">🎬 Все фильмы</a>
      <a href="/favorites" class="nav-link">❤️ Избранное</a>
    </div>

    <div class="pagination-top">
      <button id="btnPrevTop" class="btn btn-secondary">← Предыдущая</button>
      <button id="btnNextTop" class="btn btn-secondary">Следующая →</button>
    </div>

    <div class="toolbar">
      <div class="search-container">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input id="q" class="search-input" type="text" placeholder="Поиск по названию..." />
      </div>
      <button id="btnSearch" class="btn">Поиск</button>
      <button id="btnPopular" class="btn btn-secondary">Популярные</button>
      <button id="btnAll" class="btn btn-secondary">Все фильмы</button>
    </div>

    <div class="status-bar">
      <div class="status-text" id="note"></div>
      <div class="sort-controls">
        <label for="sort">Сортировка:</label>
        <select id="sort" class="sort-select">
          <option value="title">Название А→Я</option>
          <option value="rating">Рейтинг ↓</option>
          <option value="year">Год ↓</option>
        </select>
      </div>
    </div>

    <div id="grid" class="grid"></div>

    <div class="pagination">
      <button id="btnPrev" class="btn btn-secondary">← Предыдущая</button>
      <button id="btnNext" class="btn btn-secondary">Следующая →</button>
    </div>
  </div>

  <script>
    // --- State ---
    let currentPage = 1;
    let lastMode = "popular"; // "popular" | "search" | "all"
    let lastQuery = "";
    let sortBy = "title";
    let allMovies = []; // Cache for client-side filtering
    let totalPages = 1;
    let totalMovies = 0;

    // --- Helpers ---
    function setNote(text) { 
      document.getElementById('note').textContent = text || ''; 
    }

    function showLoading() { 
      document.getElementById('grid').innerHTML = `
        <div class="loading">
          <div class="spinner"></div>
          Загрузка фильмов...
        </div>
      `; 
    }

    function showError(e) {
      const message = (e && e.message) ? e.message : (e?.status ? `Ошибка ${e.status}` : 'Ошибка');
      setNote(message);
      document.getElementById('grid').innerHTML = `
        <div class="error-state">
          <h3>Что-то пошло не так</h3>
          <p>${message}</p>
        </div>
      `;
    }

    function showEmpty() {
      document.getElementById('grid').innerHTML = `
        <div class="empty-state">
          <h3>Фильмы не найдены</h3>
          <p>Попробуйте изменить критерии поиска или фильтрации.</p>
        </div>
      `;
    }

    async function parseJsonSafe(res) {
      let txt = await res.text();
      txt = txt.replace(/^\uFEFF/, '').replace(/^\s*,\s*/, ''); // strip BOM/stray comma
      return JSON.parse(txt);
    }

    function movieCard(m) {
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
          <div class="card-content">
            <h3 class="movie-title">${m.name}</h3>
            <div class="movie-meta">${year ? year + ' • ' : ''}Рейтинг: ${rating}</div>
            <div class="genres">${genres}</div>
            <div class="card-footer">
              <span class="countries">${countries}</span>
              <div class="card-actions">
                <a class="details-btn" href="/movie/${m.id}">Подробнее</a>
                <a class="kinopoisk-btn" href="https://www.kinopoisk.ru/film/${m.id}/" target="_blank" rel="noopener" title="Открыть на Кинопоиске">Кинопоиск ↗</a>
              </div>
            </div>
              </div>
        </div>`;
    }

    function renderMovies(list, label) {
      if (!list || list.length === 0) {
        showEmpty();
        return;
      }
      document.getElementById('grid').innerHTML = list.map(movieCard).join('');
      setNote(label);
    }

    // --- API wrappers ---
    async function fetchPopular(page = 1) {
      showLoading();
      const res = await fetch(`/api/movies/popular?page=${page}`);
      const data = await parseJsonSafe(res);
      currentPage = data.page || page;
      totalMovies = data.total || data.count || 0;
      totalPages = data.totalPages || Math.ceil(totalMovies / 10) || 1; // 10 movies per page
      return data.movies || [];
    }

    async function fetchAll(page = 1) {
      showLoading();
      const res = await fetch(`/api/movies/list?page=${page}`);
      const data = await parseJsonSafe(res);
      currentPage = data.page || page;
      totalMovies = data.total || data.count || 0;
      totalPages = data.totalPages || Math.ceil(totalMovies / 10) || 1; // 10 movies per page
      allMovies = data.movies || []; // Cache for client-side filtering
      return allMovies;
    }

    async function fetchSearch(q, page = 1) {
      showLoading();
      const res = await fetch(`/api/movies/search?q=${encodeURIComponent(q)}&page=${page}`);
      const data = await parseJsonSafe(res);
      currentPage = data.page || page;
      totalMovies = data.total || data.count || 0;
      totalPages = Math.ceil(totalMovies / 20) || 1; // Search still uses 20 per page
      return data.movies || [];
    }

    function sortClient(list) {
      if (sortBy === 'title') {
        return list.slice().sort((a, b) => (a.name || '').localeCompare(b.name || '', undefined, { sensitivity: 'base' }));
      } else if (sortBy === 'rating') {
        return list.slice().sort((a, b) => (parseFloat(b.rating || 0) - parseFloat(a.rating || 0)));
      } else if (sortBy === 'year') {
        return list.slice().sort((a, b) => (parseInt(b.year || 0) - parseInt(a.year || 0)));
      }
      return list;
    }

    function updatePaginationButtons() {
      const prevButtons = document.querySelectorAll('#btnPrev, #btnPrevTop');
      const nextButtons = document.querySelectorAll('#btnNext, #btnNextTop');
      
      prevButtons.forEach(btn => {
        btn.disabled = currentPage <= 1;
      });
      
      nextButtons.forEach(btn => {
        btn.disabled = currentPage >= totalPages;
      });
    }

    // --- Screens ---
    async function showPopular() {
      try {
        lastMode = 'popular';
        const movies = await fetchPopular(currentPage);
        renderMovies(movies, `Популярные — ${movies.length} фильмов (страница ${currentPage} из ${totalPages})`);
        updatePaginationButtons();
      } catch (e) { showError(e); }
    }

    async function showAll() {
      try {
        lastMode = 'all';
        const movies = await fetchAll(currentPage);
        const sorted = sortClient(movies);
        renderMovies(sorted, `Все фильмы — ${sorted.length} фильмов (страница ${currentPage} из ${totalPages})`);
        updatePaginationButtons();
      } catch (e) { showError(e); }
    }

    async function doSearch() {
      const q = document.getElementById('q').value.trim();
      if (!q) { return showAll(); }
      try {
        lastMode = 'search';
        const movies = await fetchSearch(q, currentPage);
        const sorted = sortClient(movies);
        renderMovies(sorted, `Поиск: "${q}" — ${sorted.length} фильмов (страница ${currentPage} из ${totalPages})`);
        updatePaginationButtons();
      } catch (e) { showError(e); }
    }

    // --- Pagination handlers ---
    function goToPrevPage() {
      if (currentPage > 1) {
        currentPage--;
        if (lastMode === 'popular') showPopular();
        else if (lastMode === 'all') showAll();
        else doSearch();
      }
    }

    function goToNextPage() {
      if (currentPage < totalPages) {
        currentPage++;
        if (lastMode === 'popular') showPopular();
        else if (lastMode === 'all') showAll();
        else doSearch();
      }
    }

    // --- Event handlers ---
    document.querySelectorAll('#btnPrev, #btnPrevTop').forEach(btn => {
      btn.onclick = goToPrevPage;
    });

    document.querySelectorAll('#btnNext, #btnNextTop').forEach(btn => {
      btn.onclick = goToNextPage;
    });

    document.getElementById('btnPopular').onclick = () => {
      currentPage = 1;
      showPopular();
    };
    
    document.getElementById('btnAll').onclick = () => {
      currentPage = 1;
      showAll();
    };
    
    document.getElementById('btnSearch').onclick = () => {
      currentPage = 1;
      doSearch();
    };

    // Sort handler
    document.getElementById('sort').onchange = () => {
      sortBy = document.getElementById('sort').value;
      if (lastMode === 'all' && allMovies.length > 0) {
        const sorted = sortClient(allMovies);
        renderMovies(sorted, `Все фильмы — ${sorted.length} фильмов (страница ${currentPage} из ${totalPages})`);
      }
    };

    // Search on Enter
    document.getElementById('q').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        currentPage = 1;
        doSearch();
      }
    });

    // --- Favorites functionality ---
    let guestId = getCookie('guest_id') || generateGuestId();
    let favoriteMovies = new Set();

    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function setCookie(name, value, days) {
      const expires = new Date(Date.now() + days * 864e5).toUTCString();
      document.cookie = `${name}=${value}; expires=${expires}; path=/`;
    }

    function generateGuestId() {
      const id = 'guest_' + Math.random().toString(36).substr(2, 32);
      setCookie('guest_id', id, 365);
      return id;
    }

    async function toggleFavorite(movieId) {
      try {
        const isFavorite = favoriteMovies.has(movieId);
        
        if (isFavorite) {
          // Remove from favorites
          const response = await fetch(`/api/favorites/${movieId}`, {
            method: 'DELETE'
          });
          
          if (response.ok) {
            favoriteMovies.delete(movieId);
            updateFavoriteButton(movieId, false);
          }
        } else {
          // Add to favorites
          const response = await fetch(`/api/favorites/${movieId}`, {
            method: 'POST'
          });
          
          if (response.ok) {
            favoriteMovies.add(movieId);
            updateFavoriteButton(movieId, true);
          }
        }
      } catch (error) {
        console.error('Error toggling favorite:', error);
      }
    }

    function updateFavoriteButton(movieId, isFavorite) {
      const button = document.querySelector(`[onclick="toggleFavorite('${movieId}')"]`);
      if (button) {
        button.classList.toggle('active', isFavorite);
        button.title = isFavorite ? 'Удалить из избранного' : 'Добавить в избранное';
      }
    }

    async function loadFavoriteStatus() {
      try {
        const response = await fetch('/api/favorites');
        const data = await response.json();
        
        if (response.ok) {
          favoriteMovies = new Set(data.favorites.map(movie => movie.id.toString()));
          // Update all favorite buttons
          document.querySelectorAll('.favorite-btn').forEach(btn => {
            const movieId = btn.onclick.toString().match(/toggleFavorite\('([^']+)'\)/)?.[1];
            if (movieId) {
              updateFavoriteButton(movieId, favoriteMovies.has(movieId));
            }
          });
        }
      } catch (error) {
        console.error('Error loading favorites:', error);
      }
    }

    // Initialize
    showAll(); // start with all movies
    loadFavoriteStatus(); // load favorite status
  </script>
</body>
</html>