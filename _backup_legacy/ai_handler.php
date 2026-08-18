<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'غير مصرح لك بالوصول إلى هذه الأداة.']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$prompt = isset($data['prompt']) ? trim($data['prompt']) : '';

if (empty($prompt)) {
    echo json_encode(['error' => 'يرجى تقديم وصف للمنتج.']);
    exit();
}

$api_key = 'AIzaSyBOQ7Ytdg4ruRpHA7cN37kttrpuIs9kvCo'; 
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

$system_instruction = "أنت مساعد ذكي ومسوق محترف لمدير متجر إلكتروني. سأعطيك وصفاً مختصراً لمنتج. 
مهمتك هي استخراج وتوليد البيانات التالية:
1. title: اسم جذاب واحترافي للمنتج.
2. price: سعر تقديري أو منطقي للمنتج بالجنيه المصري (أرقام فقط بدون فواصل).
3. category: اختر القسم الأنسب بدقة من هذه القائمة فقط: (هواتف, جهاز لوحي, لابتوب, ساعات ذكية, فلاشات, كاميرات, راوترات, اكسسوارات, مستعمل).
4. description: قم بكتابة وصف تسويقي تفصيلي وجذاب جداً للمنتج. اذكر مميزاته ولماذا يجب على العميل شراءه. 
*هام جداً في الوصف*: استخدم علامات HTML البسيطة لتنسيق الوصف (مثل استخدام <p> للفقرات، <ul> و <li> للقوائم النقطية للمميزات، و <strong> للكلمات المهمة).

يجب أن يكون ردك عبارة عن كود JSON نقي فقط، بدون أي نصوص إضافية، وبدون علامات الماركداون.
مثال للرد المطلوب:
{\"title\": \"لابتوب ديل \", \"price\": \"15000\", \"category\": \"لابتوب\", \"description\": \"<p>اكتشف القوة مع لابتوب ديل...</p><ul><li>معالج قوي</li><li>رام 16 جيجا</li></ul>\"}";

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => "وصف المنتج الذي أدخله المدير هو: " . $prompt]
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
    echo json_encode(['error' => 'فشل الاتصال بالذكاء الاصطناعي: ' . $curl_error]);
    exit();
}

if ($http_code !== 200) {
    echo json_encode(['error' => 'خطأ من جوجل (كود ' . $http_code . '): ' . $response]);
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
        echo json_encode(['error' => 'تعذر فهم استجابة الذكاء الاصطناعي (تنسيق غير صالح). الرد كان: ' . $generated_text]);
    }
    
} else {
    echo json_encode(['error' => 'لم يتم إرجاع أي بيانات مفيدة من الذكاء الاصطناعي.']);
}