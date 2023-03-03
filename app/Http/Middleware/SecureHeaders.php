<?php

namespace App\Http\Middleware;

use Closure;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('X-Frame-Options', 'ALLOW FROM https://example.com/');	
               $response->header('X-Frame-Options','deny'); // Anti clickjacking	
               $response->header('X-Frame-Options','deny'); // Anti clickjacking
              $response->header('X-XSS-Protection', '1'); // Anti cross site scripting (XSS)
              $response->header('X-Content-Type-Options', 'nosniff'); // Reduce exposure to drive-by dl attacks
              // $response->header('Content-Security-Policy', 'default-src \'self\''); // Reduce risk of XSS, clickjacking, and other stuff
              $response->header('Content-Security-Policy', 'script-src \'self\' data: https: \'unsafe-inline\' \'unsafe-eval\'; object-src *; style-src \'self\' data: https: https: \'unsafe-eval\' \'unsafe-inline\'; img-src * \'self\' data: https:; font-src \'self\' data: https: \'unsafe-eval\'; connect-src *');
              // Don't cache stuff (we'll be updating the page frequently)
              $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
              $response->header('Pragma', 'no-cache');
              $response->header('Access-Control-Max-Age', '28800');


        $headersToRemove = $response->headers->all();
        foreach ($headersToRemove as $name => $headerLines) {
             $response->headers->remove($name);
        }

        $headersToSend = array_merge(
                $this->setContentSecurityPolicy(),
                $this->setMiscellaneous(),
                $this->setStrictTransportSecurity(),
                $this->setPublicKeyPinning(),
                $this->setFeaturePolicy(),
                $this->setPublicKeyPinning()
            );

        foreach ($headersToSend as $key => $value) {
            $response->headers->set($key, $value, true);
        }
		//dd($response->headers);
        // return $next($response);
        return $response;
    }
    private function setContentSecurityPolicy(): array {
        $config = config('secure-headers.content-security-policy', []);
        $directives = [
            'default-src',
            'base-uri',
            'connect-src',
            'font-src',
            'form-action',
            'frame-ancestors',
            'frame-src',
            'img-src',
            'manifest-src',
            'media-src',
            'object-src',
            'plugin-types',
            'require-sri-for',
            'sandbox',
            'script-src',
            'style-src',
            'worker-src',
        ];      
        $headers = [];
        foreach ($directives as $directive) {
            if (isset($config[$directive])) {
                $headers[] = $this->processContentSecurityPolicyDirective($directive, $config[$directive]);
            }
        }

        if (! empty($config['block-all-mixed-content'])) {
            $headers[] = 'block-all-mixed-content';
        }
        if (! empty($config['upgrade-insecure-requests'])) {
            $headers[] = 'upgrade-insecure-requests';
        }
        if (! empty($config['report-uri'])) {
            $headers[] = sprintf('report-uri %s', $config['report-uri']);
        }
        $key = ! empty($config['report-only'])
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';


        return [$key => implode('; ', array_filter($headers, 'strlen'))];
    }
    private function processContentSecurityPolicyDirective($directive, $policies){
        // handle special directive first
        switch ($directive) {
            // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/plugin-types
            case 'plugin-types':
                return empty($policies) ? '' : sprintf('%s %s', $directive, implode(' ', $policies));
            // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/require-sri-for
            // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/sandbox
            case 'require-sri-for':
            case 'sandbox':
                return empty($policies) ? '' : sprintf('%s %s', $directive, $policies);
        }     

        // when policies is empty, we assume that user disallow this directive
        if (empty($policies)) {
            return sprintf("%s 'none'", $directive);
        }
        $ret = [$directive];
        // keyword source, https://www.w3.org/TR/CSP/#grammardef-keyword-source, https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/script-src
        foreach (['self', 'unsafe-inline', 'unsafe-eval', 'strict-dynamic', 'unsafe-hashed-attributes', 'report-sample'] as $keyword) {
            if (! empty($policies[$keyword])) {
                $ret[] = sprintf("'%s'", $keyword);
            }
        }
        // dd($ret);
        if (! empty($policies['allow'])) {
            foreach ($policies['allow'] as $url) {
                // removes illegal URL characters
                if (false !== ($url = filter_var($url, FILTER_SANITIZE_URL))) {
                    $ret[] = $url;
                }
            }
        }
        if (! empty($policies['hashes'])) {
            foreach ($policies['hashes'] as $algo => $hashes) {
                // skip not support algorithm, https://www.w3.org/TR/CSP/#grammardef-hash-source
                if (! in_array($algo, ['sha256', 'sha384', 'sha512'])) {
                    continue;
                }
                foreach ($hashes as $value) {
                    // skip invalid value
                    if (base64_encode(base64_decode($value, true)) !== $value) {
                        continue;
                    }
                    $ret[] = sprintf("'%s-%s'", $algo, $value);
                }
            }
        }
        if (! empty($policies['nonces'])) {
            foreach ($policies['nonces'] as $nonce) {
                // skip invalid value, https://www.w3.org/TR/CSP/#grammardef-nonce-source
                if (base64_encode(base64_decode($nonce, true)) !== $nonce) {
                    continue;
                }
                $ret[] = sprintf("'nonce-%s'", $nonce);
            }
        }
        if (! empty($policies['schemes'])) {
            foreach ($policies['schemes'] as $scheme) {
                $ret[] = sprintf('%s', $scheme);
            }
        }
        return implode(' ', $ret);           
    }
    private function setMiscellaneous():array {
        $config = config('secure-headers', []);
        return array_filter([
            'X-Content-Type-Options' => $config['x-content-type-options'],
            'X-Download-Options' => $config['x-download-options'],
            'X-Frame-Options' => $config['x-frame-options'],
            'X-Permitted-Cross-Domain-Policies' => $config['x-permitted-cross-domain-policies'],
            'X-XSS-Protection' => $config['x-xss-protection'],
            'Referrer-Policy' => $config['referrer-policy'],
            'Server' => $config['server'] ?? '',
        ]);
    }
    private function setStrictTransportSecurity(): array {
        $config = config('secure-headers', []);
        if (! $config['strict-transport-security']['enable']) {
            return [];
        }
        $hsts = "max-age={$config['strict-transport-security']['max-age']};";
        if ($config['strict-transport-security']['include-sub-domains']) {
            $hsts .= ' includeSubDomains;';
        }
        $hsts .= ' preload';
        return [
            'Strict-Transport-Security' => $hsts,
        ];
    }    
    /*private function setPublicKeyPinning():array {
        $config = config('secure-headers.public-key-pinning', []);
        $headers = [];
        foreach ($config['hashes'] as $hash) {
            $headers[] = sprintf('pin-sha256="%s"', $hash);
        }
        $headers[] = sprintf('max-age=%d', $config['max-age']);
        if ($config['include-sub-domains']) {
            $headers[] = 'includeSubDomains';
        }
        if (! empty($config['report-uri'])) {
            $headers[] = sprintf('report-uri="%s"', $config['report-uri']);
        }
        $key = $config['report-only']
            ? 'Public-Key-Pins-Report-Only'
            : 'Public-Key-Pins';
        return [$key => implode('; ', $headers)];        

    }*/
	private function setPublicKeyPinning():array {
        $config = config('secure-headers.public-key-pinning', []);
        if($config['enable']){
            $headers = [];
            foreach ($config['hashes'] as $hash) {
                $headers[] = sprintf('pin-sha256="%s"', $hash);
            }
            $headers[] = sprintf('max-age=%d', $config['max-age']);
            if ($config['include-sub-domains']) {
                $headers[] = 'includeSubDomains';
            }
            if (! empty($config['report-uri'])) {
                $headers[] = sprintf('report-uri="%s"', $config['report-uri']);
            }
            $key = $config['report-only']
                ? 'Public-Key-Pins-Report-Only'
                : 'Public-Key-Pins';
            return [$key => implode('; ', $headers)];
        }else{
            return [];
        }

    }
	
    private function setFeaturePolicy(): array {
        $config = config('secure-headers.feature-policy', []);
        $directives = [
            'accelerometer',
            'ambient-light-sensor',
            'autoplay',
            'camera',
            'encrypted-media',
            'fullscreen',
            'geolocation',
            'gyroscope',
            'magnetometer',
            'microphone',
            'midi',
            'payment',
            'picture-in-picture',
            'speaker',
            'sync-xhr',
            'usb',
            'var',
        ];

        foreach ($directives as $directive) {
            if (! isset($config[$directive]) || empty($config[$directive])) {
                continue;
            }
            $value = '';
            if ($config[$directive]['none']) {
                $value = "'none'";
            } elseif ($config[$directive]['*']) {
                $value = '*';
            } else {
                if ($config[$directive]['self']) {
                    $value = "'self'";
                }
                foreach ($config[$directive]['allow'] as $url) {
                    if (false !== ($url = filter_var($url, FILTER_SANITIZE_URL))) {
                        $value = sprintf('%s %s', $value, $url);
                    }
                }
            }
            if (strlen($value = trim($value)) > 0) {
                $headers[] = sprintf('%s %s', $directive, $value);
            }
        }
        if (! isset($headers)) {
            return [];
        }
        return ['Feature-Policy' => implode('; ', $headers)];
    }
}
