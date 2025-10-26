<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
    public function popular(Request $request): JsonResponse
    {
        $apiKey = env('KINOPOISK_API_KEY');
        if (!$apiKey || $apiKey === 'demo_key_for_testing') {
            return $this->getDemoMovies($request);
        }

        $page = (int) $request->query('page', 1);
        $perPage = 10;
        $allMovies = [];
        $maxApiPages = 10;

        for ($apiPage = 1; $apiPage <= $maxApiPages; $apiPage++) {
        $response = Http::withHeaders([
            'X-API-KEY'    => $apiKey,
            'Content-Type' => 'application/json',
        ])->get('https://kinopoiskapiunofficial.tech/api/v2.2/films/top', [
            'type' => 'TOP_100_POPULAR_FILMS',
                'page' => $apiPage,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error'  => 'Kinopoisk request failed',
                'status' => $response->status(),
                'body'   => $response->json(),
            ], 502);
        }

            $json = $response->json();
        $films = data_get($json, 'films', data_get($json, 'items', []));

            $movies = collect($films)->map(function ($f) {
            return [
                'id'        => $f['filmId'] ?? $f['kinopoiskId'] ?? null,
                'name'      => $f['nameRu'] ?? $f['nameEn'] ?? $f['nameOriginal'] ?? '',
                'year'      => $f['year'] ?? null,
                'poster'    => $f['posterUrlPreview'] ?? $f['posterUrl'] ?? null,
                'rating'    => $f['rating'] ?? $f['ratingKinopoisk'] ?? null,
                'genres'    => isset($f['genres']) ? collect($f['genres'])->pluck('genre')->all() : [],
                'countries' => isset($f['countries']) ? collect($f['countries'])->pluck('country')->all() : [],
            ];
        });
            
            $allMovies = array_merge($allMovies, $movies->toArray());
        }

        $totalMovies = count($allMovies);
        $totalPages = ceil($totalMovies / $perPage);
        $offset = ($page - 1) * $perPage;
        $pageMovies = array_slice($allMovies, $offset, $perPage);

        return response()->json([
            'page'   => $page,
            'count'  => count($pageMovies),
            'total'  => $totalMovies,
            'totalPages' => $totalPages,
            'movies' => $pageMovies,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $apiKey = env('KINOPOISK_API_KEY');
        if (!$apiKey || $apiKey === 'demo_key_for_testing') {
            return $this->getDemoMovieDetails($id);
        }

        $cacheKey = "movie_details_{$id}";

        return Cache::remember($cacheKey, 3600, function () use ($apiKey, $id) {
        $resp = Http::withHeaders([
            'X-API-KEY' => $apiKey,
        ])->get("https://kinopoiskapiunofficial.tech/api/v2.2/films/{$id}");

        if ($resp->failed()) {
            return response()->json(['error' => 'Kinopoisk request failed'], 502);
        }

        return response()->json($resp->json());
        });
    }

    public function search(Request $request): JsonResponse
    {
        $apiKey = env('KINOPOISK_API_KEY');
        if (!$apiKey || $apiKey === 'demo_key_for_testing') {
            return $this->getDemoSearchResults($request);
        }

        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['error' => 'Query is required (q=...)'], 422);
        }

        $page = (int) $request->query('page', 1);
        $cacheKey = "search_{$q}_{$page}";

        return Cache::remember($cacheKey, 1800, function () use ($apiKey, $q, $page) {
        $resp = Http::withHeaders([
            'X-API-KEY'    => $apiKey,
            'Content-Type' => 'application/json',
        ])->get('https://kinopoiskapiunofficial.tech/api/v2.1/films/search-by-keyword', [
            'keyword' => $q,
            'page'    => $page,
        ]);

        if ($resp->failed()) {
            return response()->json(['error' => 'Kinopoisk request failed'], 502);
        }

        $json  = $resp->json();
        $films = $json['films'] ?? [];

        $data = collect($films)->map(fn ($f) => [
            'id'     => $f['filmId'] ?? null,
            'name'   => $f['nameRu'] ?? $f['nameEn'] ?? $f['nameOriginal'] ?? '',
            'year'   => $f['year'] ?? null,
            'poster' => $f['posterUrlPreview'] ?? $f['posterUrl'] ?? null,
            'rating' => $f['rating'] ?? null,
        ]);

        return response()->json([
            'query'  => $q,
            'page'   => $page,
            'count'  => $data->count(),
            'movies' => $data->values(),
        ]);
        });
    }

    public function list(Request $request): JsonResponse
    {
        $apiKey = env('KINOPOISK_API_KEY');
        if (!$apiKey || $apiKey === 'demo_key_for_testing') {
            // Return demo data when API key is not set or is demo key
            return $this->getDemoMovies($request);
        }

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10; // Show 10 movies per page

        // Fetch multiple API pages to get a large pool of movies
        $allMovies = [];
        $maxApiPages = 10; // Fetch up to 10 API pages (200 movies total)
        
        for ($apiPage = 1; $apiPage <= $maxApiPages; $apiPage++) {
            $response = Http::withHeaders([
                'X-API-KEY'    => $apiKey,
                'Content-Type' => 'application/json',
            ])->get('https://kinopoiskapiunofficial.tech/api/v2.2/films/top', [
                'type' => 'TOP_100_POPULAR_FILMS',
                'page' => $apiPage,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error'  => 'Kinopoisk request failed',
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ], 502);
            }

            $json = $response->json();
            $films = data_get($json, 'films', data_get($json, 'items', []));
            
            $movies = collect($films)->map(function ($f) {
                return [
                    'id'        => $f['filmId'] ?? $f['kinopoiskId'] ?? null,
                    'name'      => $f['nameRu'] ?? $f['nameEn'] ?? $f['nameOriginal'] ?? '',
                    'year'      => $f['year'] ?? null,
                    'poster'    => $f['posterUrlPreview'] ?? $f['posterUrl'] ?? null,
                    'rating'    => $f['rating'] ?? $f['ratingKinopoisk'] ?? null,
                    'genres'    => isset($f['genres']) ? collect($f['genres'])->pluck('genre')->all() : [],
                    'countries' => isset($f['countries']) ? collect($f['countries'])->pluck('country')->all() : [],
                ];
            });
            
            $allMovies = array_merge($allMovies, $movies->toArray());
        }

        $totalMovies = count($allMovies);
        $totalPages = ceil($totalMovies / $perPage);
        $offset = ($page - 1) * $perPage;
        $pageMovies = array_slice($allMovies, $offset, $perPage);

        return response()->json([
            'page'   => $page,
            'count'  => count($pageMovies),
            'total'  => $totalMovies,
            'totalPages' => $totalPages,
            'movies' => $pageMovies,
        ]);
    }

    private function getDemoMovies(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $perPage = 10;

        $demoMovies = [
            ['id' => 1, 'name' => 'Терминатор 2: Судный день', 'year' => 1991, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 8.6, 'genres' => ['боевик', 'фантастика', 'триллер'], 'countries' => ['США', 'Франция']],
            ['id' => 2, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 3, 'name' => 'Крёстный отец', 'year' => 1972, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/325.jpg', 'rating' => 9.0, 'genres' => ['драма', 'криминал'], 'countries' => ['США']],
            ['id' => 4, 'name' => 'Тёмный рыцарь', 'year' => 2008, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'rating' => 8.5, 'genres' => ['боевик', 'криминал', 'драма'], 'countries' => ['США', 'Великобритания']],
            ['id' => 5, 'name' => 'Список Шиндлера', 'year' => 1993, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/329.jpg', 'rating' => 8.8, 'genres' => ['драма', 'биография', 'история'], 'countries' => ['США']],
            ['id' => 6, 'name' => 'Властелин колец: Возвращение короля', 'year' => 2003, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.7, 'genres' => ['фэнтези', 'приключения', 'драма'], 'countries' => ['США', 'Новая Зеландия']],
            ['id' => 7, 'name' => 'Хороший, плохой, злой', 'year' => 1966, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/328.jpg', 'rating' => 8.5, 'genres' => ['вестерн'], 'countries' => ['Италия', 'Испания', 'ФРГ']],
            ['id' => 8, 'name' => 'Форрест Гамп', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/448.jpg', 'rating' => 8.9, 'genres' => ['драма', 'мелодрама'], 'countries' => ['США']],
            ['id' => 9, 'name' => 'Начало', 'year' => 2010, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/447301.jpg', 'rating' => 8.7, 'genres' => ['фантастика', 'боевик', 'триллер'], 'countries' => ['США', 'Великобритания']],
            ['id' => 10, 'name' => 'Матрица', 'year' => 1999, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/301.jpg', 'rating' => 8.5, 'genres' => ['фантастика', 'боевик'], 'countries' => ['США', 'Австралия']],
            ['id' => 11, 'name' => 'Семь', 'year' => 1995, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/328.jpg', 'rating' => 8.4, 'genres' => ['криминал', 'триллер', 'детектив'], 'countries' => ['США']],
            ['id' => 12, 'name' => 'Спасти рядового Райана', 'year' => 1998, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/371.jpg', 'rating' => 8.2, 'genres' => ['военный', 'драма'], 'countries' => ['США']],
            ['id' => 13, 'name' => 'Интерстеллар', 'year' => 2014, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/435.jpg', 'rating' => 8.6, 'genres' => ['фантастика', 'драма', 'приключения'], 'countries' => ['США', 'Великобритания']],
            ['id' => 14, 'name' => 'Леон', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/329.jpg', 'rating' => 8.5, 'genres' => ['боевик', 'криминал', 'драма'], 'countries' => ['Франция']],
            ['id' => 15, 'name' => 'Бойцовский клуб', 'year' => 1999, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/361.jpg', 'rating' => 8.6, 'genres' => ['драма', 'триллер'], 'countries' => ['США', 'Германия']],
            ['id' => 16, 'name' => 'Аватар', 'year' => 2009, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/435.jpg', 'rating' => 7.8, 'genres' => ['фантастика', 'боевик', 'приключения'], 'countries' => ['США', 'Великобритания']],
            ['id' => 17, 'name' => 'Титаник', 'year' => 1997, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 8.4, 'genres' => ['драма', 'мелодрама'], 'countries' => ['США']],
            ['id' => 18, 'name' => 'Звёздные войны: Эпизод IV', 'year' => 1977, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['фантастика', 'боевик', 'приключения'], 'countries' => ['США']],
            ['id' => 19, 'name' => 'Парк Юрского периода', 'year' => 1993, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/325.jpg', 'rating' => 8.1, 'genres' => ['фантастика', 'приключения'], 'countries' => ['США']],
            ['id' => 20, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 21, 'name' => 'Властелин колец: Братство кольца', 'year' => 2001, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.6, 'genres' => ['фэнтези', 'приключения', 'драма'], 'countries' => ['США', 'Новая Зеландия']],
            ['id' => 22, 'name' => 'Властелин колец: Две крепости', 'year' => 2002, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.7, 'genres' => ['фэнтези', 'приключения', 'драма'], 'countries' => ['США', 'Новая Зеландия']],
            ['id' => 23, 'name' => 'Гладиатор', 'year' => 2000, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'rating' => 8.5, 'genres' => ['боевик', 'драма', 'история'], 'countries' => ['США', 'Великобритания']],
            ['id' => 24, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 25, 'name' => 'Спасти рядового Райана', 'year' => 1998, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/371.jpg', 'rating' => 8.2, 'genres' => ['военный', 'драма'], 'countries' => ['США']],
            ['id' => 26, 'name' => 'Терминатор', 'year' => 1984, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 8.0, 'genres' => ['боевик', 'фантастика', 'триллер'], 'countries' => ['США', 'Великобритания']],
            ['id' => 27, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 28, 'name' => 'Алиса в стране чудес', 'year' => 2010, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/435.jpg', 'rating' => 6.4, 'genres' => ['фэнтези', 'приключения', 'семейный'], 'countries' => ['США']],
            ['id' => 29, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 30, 'name' => 'Бегущий по лезвию', 'year' => 1982, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/301.jpg', 'rating' => 8.1, 'genres' => ['фантастика', 'триллер'], 'countries' => ['США']],
            ['id' => 31, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 32, 'name' => 'Чужие', 'year' => 1986, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 8.4, 'genres' => ['фантастика', 'боевик', 'ужасы'], 'countries' => ['США', 'Великобритания']],
            ['id' => 33, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 34, 'name' => 'Терминатор 3: Восстание машин', 'year' => 2003, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 6.3, 'genres' => ['боевик', 'фантастика', 'триллер'], 'countries' => ['США', 'Германия']],
            ['id' => 35, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 36, 'name' => 'Пираты Карибского моря', 'year' => 2003, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/435.jpg', 'rating' => 8.0, 'genres' => ['приключения', 'фэнтези', 'боевик'], 'countries' => ['США']],
            ['id' => 37, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 38, 'name' => 'Железный человек', 'year' => 2008, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'rating' => 7.9, 'genres' => ['фантастика', 'боевик'], 'countries' => ['США']],
            ['id' => 39, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 40, 'name' => 'Мстители', 'year' => 2012, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/435.jpg', 'rating' => 8.0, 'genres' => ['фантастика', 'боевик', 'приключения'], 'countries' => ['США']],
            ['id' => 41, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 42, 'name' => 'Трансформеры', 'year' => 2007, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/301.jpg', 'rating' => 6.0, 'genres' => ['фантастика', 'боевик'], 'countries' => ['США']],
            ['id' => 43, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 44, 'name' => 'Гарри Поттер и философский камень', 'year' => 2001, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.1, 'genres' => ['фэнтези', 'приключения', 'семейный'], 'countries' => ['США', 'Великобритания']],
            ['id' => 45, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 46, 'name' => 'Властелин колец: Возвращение короля', 'year' => 2003, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.7, 'genres' => ['фэнтези', 'приключения', 'драма'], 'countries' => ['США', 'Новая Зеландия']],
            ['id' => 47, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 48, 'name' => 'Тёмный рыцарь', 'year' => 2008, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'rating' => 8.5, 'genres' => ['боевик', 'криминал', 'драма'], 'countries' => ['США', 'Великобритания']],
            ['id' => 49, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 50, 'name' => 'Список Шиндлера', 'year' => 1993, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/329.jpg', 'rating' => 8.8, 'genres' => ['драма', 'биография', 'история'], 'countries' => ['США']]
        ];

        $totalMovies = count($demoMovies);
        $totalPages = ceil($totalMovies / $perPage);
        $offset = ($page - 1) * $perPage;
        $pageMovies = array_slice($demoMovies, $offset, $perPage);

        return response()->json([
            'page' => $page,
            'count' => count($pageMovies),
            'total' => $totalMovies,
            'totalPages' => $totalPages,
            'movies' => $pageMovies,
        ]);
    }

    private function getDemoMovieDetails(string $id): JsonResponse
    {
        $demoMovies = [
            ['id' => 1, 'nameRu' => 'Терминатор 2: Судный день', 'year' => 1991, 'posterUrlPreview' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'ratingKinopoisk' => 8.6, 'genres' => [['genre' => 'боевик'], ['genre' => 'фантастика'], ['genre' => 'триллер']], 'countries' => [['country' => 'США'], ['country' => 'Франция']], 'description' => 'Кибернетический организм из будущего прибывает в прошлое, чтобы убить Сару Коннор и предотвратить рождение её сына Джона.', 'filmLength' => 137],
            ['id' => 2, 'nameRu' => 'Криминальное чтиво', 'year' => 1994, 'posterUrlPreview' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'ratingKinopoisk' => 8.6, 'genres' => [['genre' => 'криминал'], ['genre' => 'драма']], 'countries' => [['country' => 'США']], 'description' => 'Двое бандитов Винсент Вега и Джулс Винфилд ведут философские беседы в перерывах между разборками и решением проблем с должниками криминального босса Марселласа Уоллеса.', 'filmLength' => 154],
            ['id' => 3, 'nameRu' => 'Крёстный отец', 'year' => 1972, 'posterUrlPreview' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/325.jpg', 'ratingKinopoisk' => 9.0, 'genres' => [['genre' => 'драма'], ['genre' => 'криминал']], 'countries' => [['country' => 'США']], 'description' => 'Стареющий патриарх криминальной династии передаёт контроль над своей подпольной империей неохотному сыну.', 'filmLength' => 175],
            ['id' => 4, 'nameRu' => 'Тёмный рыцарь', 'year' => 2008, 'posterUrlPreview' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'ratingKinopoisk' => 8.5, 'genres' => [['genre' => 'боевик'], ['genre' => 'криминал'], ['genre' => 'драма']], 'countries' => [['country' => 'США'], ['country' => 'Великобритания']], 'description' => 'Когда угроза, известная как Джокер, сеет хаос и беспорядок среди жителей Готэма, Бэтмен должен принять одно из величайших психологических и физических испытаний.', 'filmLength' => 152],
            ['id' => 5, 'nameRu' => 'Список Шиндлера', 'year' => 1993, 'posterUrlPreview' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/329.jpg', 'ratingKinopoisk' => 8.8, 'genres' => [['genre' => 'драма'], ['genre' => 'биография'], ['genre' => 'история']], 'countries' => [['country' => 'США']], 'description' => 'История Оскара Шиндлера, немецкого промышленника, который спас жизни более тысячи польских евреев во время Холокоста.', 'filmLength' => 195]
        ];

        foreach ($demoMovies as $movie) {
            if ($movie['id'] == $id) {
                return response()->json($movie);
            }
        }

        return response()->json(['error' => 'Movie not found'], 404);
    }

    private function getDemoSearchResults(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $page = (int) $request->query('page', 1);
        
        $allMovies = $this->getDemoMoviesData();
        $filteredMovies = array_filter($allMovies, function($movie) use ($q) {
            return stripos($movie['name'], $q) !== false;
        });
        
        $perPage = 10;
        $totalMovies = count($filteredMovies);
        $totalPages = ceil($totalMovies / $perPage);
        $offset = ($page - 1) * $perPage;
        $pageMovies = array_slice($filteredMovies, $offset, $perPage);

        return response()->json([
            'query' => $q,
            'page' => $page,
            'count' => count($pageMovies),
            'total' => $totalMovies,
            'totalPages' => $totalPages,
            'movies' => $pageMovies,
        ]);
    }

    private function getDemoMoviesData(): array
    {
        return [
            ['id' => 1, 'name' => 'Терминатор 2: Судный день', 'year' => 1991, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 8.6, 'genres' => ['боевик', 'фантастика', 'триллер'], 'countries' => ['США', 'Франция']],
            ['id' => 2, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США']],
            ['id' => 3, 'name' => 'Крёстный отец', 'year' => 1972, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/325.jpg', 'rating' => 9.0, 'genres' => ['драма', 'криминал'], 'countries' => ['США']],
            ['id' => 4, 'name' => 'Тёмный рыцарь', 'year' => 2008, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'rating' => 8.5, 'genres' => ['боевик', 'криминал', 'драма'], 'countries' => ['США', 'Великобритания']],
            ['id' => 5, 'name' => 'Список Шиндлера', 'year' => 1993, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/329.jpg', 'rating' => 8.8, 'genres' => ['драма', 'биография', 'история'], 'countries' => ['США']],
            ['id' => 6, 'name' => 'Властелин колец: Возвращение короля', 'year' => 2003, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.7, 'genres' => ['фэнтези', 'приключения', 'драма'], 'countries' => ['США', 'Новая Зеландия']],
            ['id' => 7, 'name' => 'Хороший, плохой, злой', 'year' => 1966, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/328.jpg', 'rating' => 8.5, 'genres' => ['вестерн'], 'countries' => ['Италия', 'Испания', 'ФРГ']],
            ['id' => 8, 'name' => 'Форрест Гамп', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/448.jpg', 'rating' => 8.9, 'genres' => ['драма', 'мелодрама'], 'countries' => ['США']],
            ['id' => 9, 'name' => 'Начало', 'year' => 2010, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/447301.jpg', 'rating' => 8.7, 'genres' => ['фантастика', 'боевик', 'триллер'], 'countries' => ['США', 'Великобритания']],
            ['id' => 10, 'name' => 'Матрица', 'year' => 1999, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/301.jpg', 'rating' => 8.5, 'genres' => ['фантастика', 'боевик'], 'countries' => ['США', 'Австралия']]
        ];
    }
}