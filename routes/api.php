use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RankingController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Não precisa estar logado)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Precisa do Token/Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Rota Nova (Essencial para o Vue.js persistir o login)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 2. Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // 3. Quiz
    Route::get('/quiz/start', [QuizController::class, 'start']);
    Route::post('/quiz/submit', [QuizController::class, 'submit']);

    // 4. Dashboard
    Route::get('/ranking', [RankingController::class, 'index']);
});