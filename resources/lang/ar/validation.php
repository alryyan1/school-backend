<?php
// resources/lang/ar/validation.php

return [
    'accepted'             => 'يجب قبول :attribute',
    'active_url'           => ':attribute ليس عنوان URL صالحًا',
    'after'                => ':attribute يجب أن يكون تاريخًا بعد :date',
    'after_or_equal'       => ':attribute يجب أن يكون تاريخًا بعد أو يساوي :date',
    'alpha'                => ':attribute يجب أن يحتوي على أحرف فقط',
    'alpha_dash'           => ':attribute يجب أن يحتوي على أحرف وأرقام وشرطات فقط',
    'alpha_num'            => ':attribute يجب أن يحتوي على أحرف وأرقام فقط',
    'array'                => ':attribute يجب أن يكون مصفوفة',
    'before'               => ':attribute يجب أن يكون تاريخًا قبل :date',
    'before_or_equal'      => ':attribute يجب أن يكون تاريخًا قبل أو يساوي :date',
    'between'              => [
        'numeric' => ':attribute يجب أن يكون بين :min و :max',
        'file'    => ':attribute يجب أن يكون بين :min و :max كيلوبايت',
        'string'  => ':attribute يجب أن يكون بين :min و :max حروف',
        'array'   => ':attribute يجب أن يحتوي بين :min و :max عناصر',
    ],
    'boolean'              => ':attribute يجب أن يكون صحيح أو خطأ',
    'confirmed'            => 'تأكيد :attribute غير متطابق',
    'date'                 => ':attribute ليس تاريخًا صالحًا',
    'date_equals'          => ':attribute يجب أن يكون تاريخًا يساوي :date',
    'date_format'          => ':attribute لا يتطابق مع الصيغة :format',
    'different'            => ':attribute و :other يجب أن يكونا مختلفين',
    'digits'               => ':attribute يجب أن يكون :digits أرقام',
    'digits_between'       => ':attribute يجب أن يكون بين :min و :max أرقام',
    'dimensions'           => ':attribute أبعاد الصورة غير صالحة',
    'distinct'             => ':attribute يحتوي على قيمة مكررة',
    'email'                => ':attribute يجب أن يكون بريدًا إلكترونيًا صالحًا',
    'ends_with'            => ':attribute يجب أن ينتهي بأحد القيم التالية: :values',
    'exists'               => ':attribute المحدد غير صالح',
    'file'                 => ':attribute يجب أن يكون ملفًا',
    'filled'               => ':attribute مطلوب',
    'gt'                   => [
        'numeric' => ':attribute يجب أن يكون أكبر من :value',
        'file'    => ':attribute يجب أن يكون أكبر من :value كيلوبايت',
        'string'  => ':attribute يجب أن يكون أكبر من :value حروف',
        'array'   => ':attribute يجب أن يحتوي على أكثر من :value عناصر',
    ],
    'gte'                  => [
        'numeric' => ':attribute يجب أن يكون أكبر من أو يساوي :value',
        'file'    => ':attribute يجب أن يكون أكبر من أو يساوي :value كيلوبايت',
        'string'  => ':attribute يجب أن يكون أكبر من أو يساوي :value حروف',
        'array'   => ':attribute يجب أن يحتوي على :value عناصر أو أكثر',
    ],
    'image'                => ':attribute يجب أن يكون صورة',
    'in'                   => ':attribute المحدد غير صالح',
    'in_array'             => ':attribute غير موجود في :other',
    'integer'              => ':attribute يجب أن يكون عددًا صحيحًا',
    'ip'                   => ':attribute يجب أن يكون عنوان IP صالحًا',
    'ipv4'                 => ':attribute يجب أن يكون عنوان IPv4 صالحًا',
    'ipv6'                 => ':attribute يجب أن يكون عنوان IPv6 صالحًا',
    'json'                 => ':attribute يجب أن يكون نصًا من نوع JSON',
    'lt'                   => [
        'numeric' => ':attribute يجب أن يكون أقل من :value',
        'file'    => ':attribute يجب أن يكون أقل من :value كيلوبايت',
        'string'  => ':attribute يجب أن يكون أقل من :value حروف',
        'array'   => ':attribute يجب أن يحتوي على أقل من :value عناصر',
    ],
    'lte'                  => [
        'numeric' => ':attribute يجب أن يكون أقل من أو يساوي :value',
        'file'    => ':attribute يجب أن يكون أقل من أو يساوي :value كيلوبايت',
        'string'  => ':attribute يجب أن يكون أقل من أو يساوي :value حروف',
        'array'   => ':attribute يجب أن يحتوي على :value عناصر أو أقل',
    ],
    'max'                  => [
        'numeric' => ':attribute لا يجب أن يكون أكبر من :max',
        'file'    => ':attribute لا يجب أن يكون أكبر من :max كيلوبايت',
        'string'  => ':attribute لا يجب أن يكون أكبر من :max حروف',
        'array'   => ':attribute لا يجب أن يحتوي على أكثر من :max عناصر',
    ],
    'mimes'                => ':attribute يجب أن يكون ملفًا من النوع: :values',
    'mimetypes'            => ':attribute يجب أن يكون ملفًا من النوع: :values',
    'min'                  => [
        'numeric' => ':attribute يجب أن يكون على الأقل :min',
        'file'    => ':attribute يجب أن يكون على الأقل :min كيلوبايت',
        'string'  => ':attribute يجب أن يكون على الأقل :min حروف',
        'array'   => ':attribute يجب أن يحتوي على الأقل :min عناصر',
    ],
    'not_in'               => ':attribute المحدد غير صالح',
    'not_regex'            => 'صيغة :attribute غير صالحة',
    'numeric'              => ':attribute يجب أن يكون رقمًا',
    'password'             => 'كلمة المرور غير صحيحة',
    'present'              => ':attribute يجب أن يكون موجودًا',
    'regex'                => 'صيغة :attribute غير صالحة',
    'required'             => 'حقل :attribute مطلوب',
    'required_if'          => ':attribute مطلوب عندما :other يساوي :value',
    'required_unless'      => ':attribute مطلوب إلا إذا :other موجود في :values',
    'required_with'        => ':attribute مطلوب عندما :values موجود',
    'required_with_all'    => ':attribute مطلوب عندما :values موجودة',
    'required_without'     => ':attribute مطلوب عندما :values غير موجود',
    'required_without_all' => ':attribute مطلوب عندما لا يوجد أي من :values',
    'same'                 => ':attribute و :other يجب أن يتطابقا',
    'size'                 => [
        'numeric' => ':attribute يجب أن يكون :size',
        'file'    => ':attribute يجب أن يكون :size كيلوبايت',
        'string'  => ':attribute يجب أن يكون :size حروف',
        'array'   => ':attribute يجب أن يحتوي على :size عناصر',
    ],
    'starts_with'          => ':attribute يجب أن يبدأ بأحد القيم التالية: :values',
    'string'               => ':attribute يجب أن يكون نصًا',
    'timezone'             => ':attribute يجب أن يكون منطقة زمنية صالحة',
    'unique'               => ':attribute مستخدم مسبقًا',
    'uploaded'             => 'فشل تحميل :attribute',
    'url'                  => 'صيغة :attribute غير صالحة',
    'uuid'                 => ':attribute يجب أن يكون UUID صالحًا',

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'role' => 'الدور',
        'phone' => 'رقم الهاتف',
        'gender' => 'الجنس',
        'address' => 'العنوان',
        'birth_date' => 'تاريخ الميلاد',
        'national_id' => 'رقم الهوية',
    ],
];
