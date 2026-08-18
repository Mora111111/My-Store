<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'غير مصرح لك بالوصول.']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$comment = isset($data['comment']) ? trim($data['comment']) : '';

if (empty($comment)) {
    echo json_encode(['error' => 'نص التعليق فارغ.']);
    exit();
}

$api_key = 'AIzaSyBOQ7Ytdg4ruRpHA7cN37kttrpuIs9kvCo'; 
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

$system_instruction = "أنت ممثل خدمة عملاء محترف ولبق جداً في متجر إلكتروني مصري. سأعطيك تعليقاً كتبه أحد العملاء على منتج.
مهمتك: كتابة رد احترافي ومناسب على هذا التعليق نيابة عن إدارة المتجر.
- إذا كان التعليق إيجابياً (مدح): اشكره بحرارة ورحب به.
- إذا كان سلبياً أو شكوى: اعتذر بلباقة شديدة، وأظهر تعاطفك، وقدم حلاً أو وعداً بحل المشكلة وتواصل الدعم الفني معه.
- إذا كان استفساراً عاماً: قدم رداً ترحيبياً يوضح أننا في خدمته دائماً.

يجب أن يكون ردك باللغة العربية الواضحة (يفضل بلهجة مصرية راقية ومحترفة).
يجب أن يكون ردك عبارة عن كود JSON نقي فقط، بدون أي نصوص إضافية، وبدون علامات الماركداون.
مثال للرد المطلوب:
{\"reply\": \"أهلاً    شكراً جزيلاً لثقتك في منتجاتنا، ونتمنى لك تجربة تسوق مميزة دائماً.\"}";

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => "تعليق العميل هو: " . $comment]
            ]
        ]
    ],
    "systemInstruction" => [
        "parts" => [
            ["text" => $system_instruction]
        ]
    ],
    "generationConfig" => [
        "responseMimeType" => "application/json"
    ]
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode(['error' => 'فشل الاتصال: ' . $curl_error]);
    exit();
}

if ($http_code !== 200) {
    echo json_encode(['error' => 'خطأ من جوجل: ' . $response]);
    exit();
}

$response_data = json_decode($response, true);

if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
    $generated_text = $response_data['candidates'][0]['content']['parts'][0]['text'];
    $generated_text = str_replace(['```json', '```'], '', $generated_text);
    $generated_text = trim($generated_text);

    $final_json = json_decode($generated_text, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        echo json_encode($final_json);
    } else {
        echo json_encode(['error' => 'تعذر فهم الرد.']);
    }
} else {
    echo json_encode(['error' => 'لم يتم إرجاع بيانات.']);
}