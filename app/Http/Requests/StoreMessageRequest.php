<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:5000',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,zip,txt',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'حقل نص الرسالة مطلوب.',
            'body.max' => 'نص الرسالة يجب ألا يتجاوز 5000 حرف.',
            'attachment.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
            'attachment.mimes' => 'نوع الملف المرفق غير مدعوم.',
        ];
    }
}
