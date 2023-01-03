<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/login", function (Request $request) {
    $request->session()->put("state", $state = Str::random(40));
    $query = http_build_query([
        "client_id" => "94ec76d772c0621bca7e",
        "redirect_uri" => "http://localhost:8000/callback",
        "response_type" => "code",
        "scope" => "openid email",
        "state" => $state
    ]);
    return redirect('http://localhost:3000/login/oauth/authorize?'.$query);
});

Route::get("/callback", function (Request $request) {
    $state = $request->session()->get("state");
    throw_unless(strlen($state) > 0 && $state == $request->state, InvalidArgumentException::class);
    $response = Http::asForm()->post("http://localhost:8000/api/login/oauth/access_token", [
        "grant_type" => "authorization_code",
        "client_id" => "94ec76d772c0621bca7e",
        "client_secret" => "8eab9054fafc4e2888f36123f2f8073a4559c898",
        "redirect_uri" => "http://localhost:8000/callback",
        "code" => $request->code
    ]);
    return $response->json();
});

Route::get("/authuser", function (Request $request) {
    $access_token = "eyJhbGciOiJSUzI1NiIsImtpZCI6ImNlcnQtYnVpbHQtaW4iLCJ0eXAiOiJKV1QifQ.eyJvd25lciI6IlRlbGtvbSBJbmRvbmVzaWEiLCJuYW1lIjoiQXptaSIsImNyZWF0ZWRUaW1lIjoiMjAyMi0wOS0wOVQxNzo0ODoyMSswNzowMCIsInVwZGF0ZWRUaW1lIjoiIiwiaWQiOiJhYTQ1ZTAzNy1kZTRlLTRjNmMtOTUzOS0yNDY5MjZkMGRhNWUiLCJ0eXBlIjoibm9ybWFsLXVzZXIiLCJwYXNzd29yZCI6IiIsInBhc3N3b3JkU2FsdCI6IiIsImRpc3BsYXlOYW1lIjoiYXptaSIsImZpcnN0TmFtZSI6IiIsImxhc3ROYW1lIjoiIiwiYXZhdGFyIjoiaHR0cHM6Ly9jZG4uY2FzYmluLm9yZy9pbWcvY2FzYmluLnN2ZyIsInBlcm1hbmVudEF2YXRhciI6IiIsImVtYWlsIjoidGVzdGluZ0BnbWFpbC5jb20iLCJlbWFpbFZlcmlmaWVkIjpmYWxzZSwicGhvbmUiOiI5OTY0NTUyNjA5NyIsImxvY2F0aW9uIjoiIiwiYWRkcmVzcyI6W10sImFmZmlsaWF0aW9uIjoiRXhhbXBsZSBJbmMuIiwidGl0bGUiOiIiLCJpZENhcmRUeXBlIjoiIiwiaWRDYXJkIjoiIiwiaG9tZXBhZ2UiOiIiLCJiaW8iOiIiLCJyZWdpb24iOiIiLCJsYW5ndWFnZSI6IiIsImdlbmRlciI6IiIsImJpcnRoZGF5IjoiIiwiZWR1Y2F0aW9uIjoiIiwic2NvcmUiOjAsImthcm1hIjowLCJyYW5raW5nIjoyLCJpc0RlZmF1bHRBdmF0YXIiOmZhbHNlLCJpc09ubGluZSI6ZmFsc2UsImlzQWRtaW4iOnRydWUsImlzR2xvYmFsQWRtaW4iOnRydWUsImlzRm9yYmlkZGVuIjpmYWxzZSwiaXNEZWxldGVkIjpmYWxzZSwic2lnbnVwQXBwbGljYXRpb24iOiJhcHAtYnVpbHQtaW4iLCJoYXNoIjoiIiwicHJlSGFzaCI6IiIsImNyZWF0ZWRJcCI6IiIsImxhc3RTaWduaW5UaW1lIjoiIiwibGFzdFNpZ25pbklwIjoiIiwiZ2l0aHViIjoiIiwiZ29vZ2xlIjoiIiwicXEiOiIiLCJ3ZWNoYXQiOiIiLCJmYWNlYm9vayI6IiIsImRpbmd0YWxrIjoiIiwid2VpYm8iOiIiLCJnaXRlZSI6IiIsImxpbmtlZGluIjoiIiwid2Vjb20iOiIiLCJsYXJrIjoiIiwiZ2l0bGFiIjoiIiwiYWRmcyI6IiIsImJhaWR1IjoiIiwiYWxpcGF5IjoiIiwiY2FzZG9vciI6IiIsImluZm9mbG93IjoiIiwiYXBwbGUiOiIiLCJhenVyZWFkIjoiIiwic2xhY2siOiIiLCJzdGVhbSI6IiIsImJpbGliaWxpIjoiIiwib2t0YSI6IiIsImRvdXlpbiI6IiIsImN1c3RvbSI6IiIsIndlYmF1dGhuQ3JlZGVudGlhbHMiOm51bGwsImxkYXAiOiIiLCJwcm9wZXJ0aWVzIjp7fSwicm9sZXMiOltdLCJwZXJtaXNzaW9ucyI6W10sImxhc3RTaWduaW5Xcm9uZ1RpbWUiOiIiLCJzaWduaW5Xcm9uZ1RpbWVzIjowLCJtYW5hZ2VkQWNjb3VudHMiOm51bGwsInRhZyI6InN0YWZmIiwic2NvcGUiOiJvcGVuaWQgcHJvZmlsZSBlbWFpbCIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCIsInN1YiI6ImFhNDVlMDM3LWRlNGUtNGM2Yy05NTM5LTI0NjkyNmQwZGE1ZSIsImF1ZCI6WyJkNWIxOGFkNWIyNTA3ZmU3Nzg3NSJdLCJleHAiOjE2NjMzNDA0MDQsIm5iZiI6MTY2MjczNTYwNCwiaWF0IjoxNjYyNzM1NjA0LCJqdGkiOiJhZG1pbi9lZGEwNDA2Yi0xYjI5LTQwOGMtYjc0MS05YjE5ZGVhNTg3YTEifQ.Lv-iaY28KvEeWe2Th7RzsK6QFvee9-cijDIjVzGFHjMk4e98k20hh2tSbjHZS2hYtuwvDAwQxGLwS0T2h7hW2cvgDwWzIZiQk8-RyVzV3ZFLwm6qvHcIsZH2IeXG_flIerzorvEhEDC28XLCGAGJelJc_r6-rjPwP1xesECaA_Dh3SlkbqF-iBeGgebFteUq2AdCRWO_Ht6z9ZNghMLVkx2upCFpk4H2fDp3KCIAICxq1WjwlihogBsHsqeqoncjM2P3AkoHQSCIWKj851l-IvIt0gnXZzvy0im3ADRsgjwditgpej8WBzX6oZ7bEp8oNrFSjPsvx347z4u2mK6wEV-9eTzvXraN5eLZWtaz0fua888phdIeNrRSQT_F8QQE6DBI8FIlnYcYPWB16peUBCYaqr8iA-2cR4Aa1xcUPhgMyX9DIfqKA6vvmcpQL9sHBXcoCMHcnOegFxMnzRA6hFP5pX3xEDQj3Ya4kCGMSJ61US-F1VbuM_VPTx5o8RNRkNWT175mnT3WhCZtZ_V1kZu0Sah0i54JlJfUnO_jSENRfNPcCpqSgeoGCQAFynqxnRM4ew6hQ_0iuw804L0PsxgnfBTVtYtyJi3rdq8zUCj9uJjXDqQgTnEbufEA13j8qAUTVJVGBNhyWtsF-uf0quOfjt9TsHmVwFBdbuMeNjE";
    $response = Http::withHeaders([
        "Accept" => "application/json",
        "Authorization" => "Bearer " . $access_token
    ])->get("http://localhost:8080/api/userinfo");
    return $response->json();
});
