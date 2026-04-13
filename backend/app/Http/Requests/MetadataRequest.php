<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MetadataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => 'required|url',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $url = $this->input('url');

                if (! $url) {
                    return;
                }

                $host = parse_url($url, PHP_URL_HOST);

                if (! $host) {
                    $validator->errors()->add('url', 'The URL must contain a valid host.');
                    return;
                }

                // Resolve hostname to IP(s) and check each against private ranges
                $ips = gethostbynamel($host);

                if ($ips === false) {
                    $validator->errors()->add('url', 'The URL host could not be resolved.');
                    return;
                }

                foreach ($ips as $ip) {
                    if ($this->isPrivateIp($ip)) {
                        $validator->errors()->add('url', 'The URL must not point to a private or internal network address.');
                        return;
                    }
                }
            },
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
