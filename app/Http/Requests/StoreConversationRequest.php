<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:email,internal,internal_chat,external_email',
            'subject' => 'nullable|string|max:255',
            'client_email' => 'required_if:type,email|email',
            'client_name' => 'nullable|string|max:255',
            'recipient_id' => 'required_if:type,internal|exists:users,id',
            'external_contact_email' => 'required_if:type,external_email|email',
            'external_contact_name' => 'nullable|string|max:255',
            'body' => 'required|string|max:5000',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,zip,txt',
        ];
    }

    public function messages(): array
    {
        return [
            'client_email.required_if' => 'حقل البريد الإلكتروني للعميل مطلوب.',
            'client_email.email' => 'البريد الإلكتروني للعميل المدخل غير صالح.',
            'recipient_id.required_if' => 'يرجى اختيار الموظف المستلم.',
            'recipient_id.exists' => 'الموظف المحدد غير موجود.',
            'body.required' => 'حقل نص الرسالة مطلوب.',
            'body.max' => 'نص الرسالة يجب ألا يتجاوز 5000 حرف.',
            'attachment.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت.',
            'attachment.mimes' => 'نوع الملف المرفق غير مدعوم.',
        ];
    }
}
