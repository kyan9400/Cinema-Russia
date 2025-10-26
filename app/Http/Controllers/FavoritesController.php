<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
    private function getGuestId(Request $request): string
    {
        $guestId = $request->cookie('guest_id');
        
        if (!$guestId) {
            $guestId = 'guest_' . Str::random(32);
        }
        
        return $guestId;
    }

    private function setGuestCookie(string $guestId): void
    {
        cookie('guest_id', $guestId, 60 * 24 * 30); // 30 days
    }

    public function index(Request $request): JsonResponse
    {
        $guestId = $this->getGuestId($request);
        
        try {
            $favoriteIds = Redis::smembers("favorites:{$guestId}");
        } catch (\Exception $e) {
            $favoriteIds = DB::table('favorites')
                ->where('guest_id', $guestId)
                ->pluck('movie_id')
                ->toArray();
        }
        
        if (empty($favoriteIds)) {
            return response()->json([
                'favorites' => [],
                'count' => 0
            ]);
        }

        $movies = [];
        foreach ($favoriteIds as $movieId) {
            $movieData = $this->getMovieDetails($movieId);
            if ($movieData) {
                $movies[] = $movieData;
            }
        }

        return response()->json([
            'favorites' => $movies,
            'count' => count($movies)
        ]);
    }

    public function store(Request $request, string $movieId): JsonResponse
    {
        $guestId = $this->getGuestId($request);
        
        $isFavorite = Redis::sismember("favorites:{$guestId}", $movieId);
        
        if ($isFavorite) {
            return response()->json(['message' => 'Movie already in favorites']);
        }
        
        $movieData = $this->getMovieDetails($movieId);
        
        if (!$movieData) {
            return response()->json(['error' => 'Movie not found'], 404);
        }

        Redis::sadd("favorites:{$guestId}", $movieId);
        
        $response = response()->json([
            'message' => 'Movie added to favorites',
            'movie' => $movieData
        ]);
        
        $response->cookie('guest_id', $guestId, 60 * 24 * 30);
        
        return $response;
    }

    public function destroy(Request $request, string $movieId): JsonResponse
    {
        $guestId = $this->getGuestId($request);
        
        $removed = Redis::srem("favorites:{$guestId}", $movieId);
        
        return response()->json([
            'message' => 'Movie removed from favorites',
            'removed' => $removed > 0
        ]);
    }

    public function check(Request $request, string $movieId): JsonResponse
    {
        $guestId = $this->getGuestId($request);
        $isFavorite = Redis::sismember("favorites:{$guestId}", $movieId);
        
        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }

    private function getMovieDetails(string $movieId): ?array
    {
        $apiKey = env('KINOPOISK_API_KEY');
        if (!$apiKey || $apiKey === 'demo_key_for_testing') {
            return $this->getDemoMovieDetails($movieId);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-KEY' => $apiKey,
            ])->get("https://kinopoiskapiunofficial.tech/api/v2.2/films/{$movieId}");

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();
            
            return [
                'id' => $data['kinopoiskId'] ?? $movieId,
                'name' => $data['nameRu'] ?? $data['nameEn'] ?? $data['nameOriginal'] ?? '',
                'year' => $data['year'] ?? null,
                'poster' => $data['posterUrlPreview'] ?? $data['posterUrl'] ?? null,
                'rating' => $data['ratingKinopoisk'] ?? $data['rating'] ?? null,
                'genres' => isset($data['genres']) ? collect($data['genres'])->pluck('genre')->all() : [],
                'countries' => isset($data['countries']) ? collect($data['countries'])->pluck('country')->all() : [],
                'description' => $data['description'] ?? '',
                'filmLength' => $data['filmLength'] ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getDemoMovieDetails(string $movieId): ?array
    {
        $demoMovies = [
            ['id' => 1, 'name' => 'Терминатор 2: Судный день', 'year' => 1991, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/2213.jpg', 'rating' => 8.6, 'genres' => ['боевик', 'фантастика', 'триллер'], 'countries' => ['США', 'Франция'], 'description' => 'Кибернетический организм из будущего прибывает в прошлое, чтобы убить Сару Коннор и предотвратить рождение её сына Джона.', 'filmLength' => 137],
            ['id' => 2, 'name' => 'Криминальное чтиво', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/342.jpg', 'rating' => 8.6, 'genres' => ['криминал', 'драма'], 'countries' => ['США'], 'description' => 'Двое бандитов Винсент Вега и Джулс Винфилд ведут философские беседы в перерывах между разборками и решением проблем с должниками криминального босса Марселласа Уоллеса.', 'filmLength' => 154],
            ['id' => 3, 'name' => 'Крёстный отец', 'year' => 1972, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/325.jpg', 'rating' => 9.0, 'genres' => ['драма', 'криминал'], 'countries' => ['США'], 'description' => 'Стареющий патриарх криминальной династии передаёт контроль над своей подпольной империей неохотному сыну.', 'filmLength' => 175],
            ['id' => 4, 'name' => 'Тёмный рыцарь', 'year' => 2008, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/111543.jpg', 'rating' => 8.5, 'genres' => ['боевик', 'криминал', 'драма'], 'countries' => ['США', 'Великобритания'], 'description' => 'Когда угроза, известная как Джокер, сеет хаос и беспорядок среди жителей Готэма, Бэтмен должен принять одно из величайших психологических и физических испытаний.', 'filmLength' => 152],
            ['id' => 5, 'name' => 'Список Шиндлера', 'year' => 1993, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/329.jpg', 'rating' => 8.8, 'genres' => ['драма', 'биография', 'история'], 'countries' => ['США'], 'description' => 'История Оскара Шиндлера, немецкого промышленника, который спас жизни более тысячи польских евреев во время Холокоста.', 'filmLength' => 195],
            ['id' => 6, 'name' => 'Властелин колец: Возвращение короля', 'year' => 2003, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/3498.jpg', 'rating' => 8.7, 'genres' => ['фэнтези', 'приключения', 'драма'], 'countries' => ['США', 'Новая Зеландия'], 'description' => 'Гэндальф и Арагорн возглавляют Мир Средиземья против армии Саурона, пытаясь отвлечь внимание от Фродо и Сэма, которые приближаются к горе Рок.', 'filmLength' => 201],
            ['id' => 7, 'name' => 'Хороший, плохой, злой', 'year' => 1966, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/328.jpg', 'rating' => 8.5, 'genres' => ['вестерн'], 'countries' => ['Италия', 'Испания', 'ФРГ'], 'description' => 'Трое мужчин ищут золото, спрятанное на кладбище во время Гражданской войны в США.', 'filmLength' => 178],
            ['id' => 8, 'name' => 'Форрест Гамп', 'year' => 1994, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/448.jpg', 'rating' => 8.9, 'genres' => ['драма', 'мелодрама'], 'countries' => ['США'], 'description' => 'История жизни Форреста Гампа, простого человека с добрым сердцем, который случайно становится свидетелем и участником ключевых исторических событий.', 'filmLength' => 142],
            ['id' => 9, 'name' => 'Начало', 'year' => 2010, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/447301.jpg', 'rating' => 8.7, 'genres' => ['фантастика', 'боевик', 'триллер'], 'countries' => ['США', 'Великобритания'], 'description' => 'Криминальный триллер о мире, где технологии позволяют проникать в сны людей.', 'filmLength' => 148],
            ['id' => 10, 'name' => 'Матрица', 'year' => 1999, 'poster' => 'https://kinopoiskapiunofficial.tech/images/posters/kp/301.jpg', 'rating' => 8.5, 'genres' => ['фантастика', 'боевик'], 'countries' => ['США', 'Австралия'], 'description' => 'Компьютерный хакер узнаёт от таинственных повстанцев истинную природу его реальности и свою роль в войне против её контролёров.', 'filmLength' => 136]
        ];

        foreach ($demoMovies as $movie) {
            if ($movie['id'] == $movieId) {
                return $movie;
            }
        }

        return null;
    }
}