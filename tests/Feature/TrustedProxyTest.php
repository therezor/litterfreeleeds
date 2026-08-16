<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * In production the app sits behind Cloudflare and then nginx, so TLS is
 * terminated upstream and the original scheme arrives in a header.
 *
 * This is load-bearing rather than cosmetic. Email verification is a signed
 * URL, and Laravel signs and checks the signature over
 * `$request->getSchemeAndHttpHost()` — the very string asserted below. Stop
 * trusting the proxy and that string becomes "http://..." while the link in the
 * inbox says "https://...", so every volunteer following a valid link gets
 * "Invalid signature" and can never verify.
 *
 * It only misbehaves behind a proxy, which is why it is pinned here rather than
 * left to be discovered in production.
 */
class TrustedProxyTest extends TestCase
{
    public function test_the_forwarded_scheme_is_trusted(): void
    {
        // JSON rather than a word in the body: "secure" is a substring of
        // "insecure", so an assertSee for it passes either way round.
        Route::get('/__scheme-probe', fn (Request $request) => response()->json([
            'secure' => $request->isSecure(),
            'schemeAndHost' => $request->getSchemeAndHttpHost(),
        ]));

        $this->get('/__scheme-probe', ['X-Forwarded-Proto' => 'https'])
            ->assertOk()
            ->assertJson(['secure' => true, 'schemeAndHost' => 'https://localhost']);
    }
}
