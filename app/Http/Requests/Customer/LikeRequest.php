<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class LikeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ✅ استخدم $this->user() بدلاً من auth()
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'likeable_type' => 'required|string|in:App\\Models\\Product,App\\Models\\Post,App\\Models\\Comment',
            'likeable_id' => 'required|integer|exists:' . $this->getTableName($this->likeable_type) . ',id',
        ];
    }

    public function messages(): array
    {
        return [
            'likeable_type.in' => 'نوع العنصر غير صحيح',
            'likeable_id.exists' => 'العنصر المطلوب غير موجود',
        ];
    }

    private function getTableName($type): string
    {
        return match ($type) {
            'App\\Models\\Product' => 'products',
            'App\\Models\\Post' => 'posts',
            'App\\Models\\Comment' => 'comments',
            default => 'products',
        };
    }
}