<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'notification_time' => ['sometimes', 'date_format:H:i:s'],
            'fcm_token' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
