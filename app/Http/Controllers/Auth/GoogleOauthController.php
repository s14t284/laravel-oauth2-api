<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserLoginException;
use App\Http\Controllers\Controller;
use App\Http\UseCases\OAuthUseCase;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleOauthController extends Controller implements OAuthControllerInterface
{
    /**
     * @var OAuthUseCase
     */
    private $oauthUseCase;

    public function __construct(OAuthUseCase $oauthUseCase)
    {
        $this->oauthUseCase = $oauthUseCase;
    }

    /**
     * @return JsonResponse
     */
    public function getRedirectUrl(): JsonResponse
    {
        // 以下でリダイレクト先URLを取得できるので、これを返却するのもあり
        // Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        $url = Socialite::driver('google')->redirect()->getTargetUrl();
        return new JsonResponse(['url' => $url], 200);
    }

    public function callback(): JsonResponse
    {
        try {
            $this->oauthUseCase->execute('google');
            return new JsonResponse(['message' => 'login success']);
        } catch (UserLoginException $ule) {
            return new JsonResponse(['message' => 'login failed with user login exception. cause: ' . $ule->getMessage()]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'login failed with unknown exception. cause: ' . $e->getMessage()]);
        }
    }
}
