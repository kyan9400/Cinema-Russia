<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Избранное — Кино-Россия</title>
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
      background: rgba(239, 68, 68, 0.9);
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
      background: var(--error);
      transform: scale(1.1);
    }

    .favorite-btn.removed {
      background: rgba(156, 163, 175, 0.9);
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
      <h1>Избранное</h1>
      <p class="subtitle">Ваши любимые фильмы</p>
    </div>

    <div class="nav-links">
      <a href="/" class="nav-link">🏠 Главная</a>
      <a href="/movies" class="nav-link">🎬 Все фильмы</a>
      <a href="/favorites" class="nav-link active">❤️ Избранное</a>
    </div>

    <div class="status-bar">
      <div class="status-text" id="note">Загрузка...</div>
    </div>

    <div id="grid" class="grid"></div>
  </div>

  <script>
    let favorites = [];
    let guestId = getCookie('guest_id') || generateGuestId();

    function generateGuestId() {
      const id = 'guest_' + Math.random().toString(36).substr(2, 32);
      setCookie('guest_id', id, 365);
      return id;
    }

    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function setCookie(name, value, days) {
      const expires = new Date(Date.now() + days * 864e5).toUTCString();
      document.cookie = `${name}=${value}; expires=${expires}; path=/`;
    }

    function showLoading() {
      document.getElementById('grid').innerHTML = `
        <div class="loading">
          <div class="spinner"></div>
          Загрузка избранного...
        </div>
      `;
    }

    function showError(message) {
      document.getElementById('grid').innerHTML = `
        <div class="error-state">
          <h3>Ошибка</h3>
          <p>${message}</p>
        </div>
      `;
    }

    function showEmpty() {
      document.getElementById('grid').innerHTML = `
        <div class="empty-state">
          <h3>Избранное пусто</h3>
          <p>Добавьте фильмы в избранное, нажав на сердечко ♥</p>
        </div>
      `;
    }

    function movieCard(movie) {
      const year = movie.year || '';
      const rating = (movie.rating ?? '') || '—';
      const genres = Array.isArray(movie.genres) ? movie.genres.join(', ') : (movie.genres || '');
      const poster = movie.poster || '';
      const countries = Array.isArray(movie.countries) ? movie.countries.join(', ') : (movie.countries || '');
      
      return `
        <div class="card">
          <div class="poster-container">
            <img class="poster" src="${poster}" alt="${movie.name}" loading="lazy">
            <div class="rating-badge">${rating}</div>
            <button class="favorite-btn" onclick="removeFromFavorites('${movie.id}')" title="Удалить из избранного">
              ♥
            </button>
          </div>
          <div class="card-content">
            <h3 class="movie-title">${movie.name}</h3>
            <div class="movie-meta">${year ? year + ' • ' : ''}Рейтинг: ${rating}</div>
            <div class="genres">${genres}</div>
            <div class="card-footer">
              <span class="countries">${countries}</span>
              <div class="card-actions">
                <a class="details-btn" href="/movie/${movie.id}">Подробнее</a>
                <a class="kinopoisk-btn" href="https://www.kinopoisk.ru/film/${movie.id}/" target="_blank" rel="noopener" title="Открыть на Кинопоиске">Кинопоиск ↗</a>
              </div>
            </div>
          </div>
        </div>`;
    }

    function renderFavorites() {
      if (!favorites || favorites.length === 0) {
        showEmpty();
        document.getElementById('note').textContent = 'Избранное пусто';
        return;
      }
      
      document.getElementById('grid').innerHTML = favorites.map(movieCard).join('');
      document.getElementById('note').textContent = `Избранное — ${favorites.length} фильмов`;
    }

    async function loadFavorites() {
      try {
        showLoading();
        const response = await fetch('/api/favorites');
        const data = await response.json();
        
        if (response.ok) {
          favorites = data.favorites || [];
          renderFavorites();
        } else {
          showError(data.error || 'Ошибка загрузки избранного');
        }
      } catch (error) {
        showError('Ошибка соединения с сервером');
      }
    }

    async function removeFromFavorites(movieId) {
      try {
        const response = await fetch(`/api/favorites/${movieId}`, {
          method: 'DELETE'
        });
        
        if (response.ok) {
          // Remove from local array
          favorites = favorites.filter(movie => movie.id != movieId);
          renderFavorites();
        } else {
          alert('Ошибка при удалении из избранного');
        }
      } catch (error) {
        alert('Ошибка соединения с сервером');
      }
    }

    // Initialize
    loadFavorites();
  </script>
</body>
</html>
