<?php
class AiController {
    
    public function handleComment(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!Session::get('user_id') || Session::get('user_role') !== 'admin') {
            echo json_encode(['error' => 'غير مصرح لك بالوصول.']);
            return;
        }
        
        $comment = $_POST['comment'] ?? '';
        if (empty($comment)) {
            $data = json_decode(file_get_contents("php://input"), true);
            $comment = $data['comment'] ?? '';
        }
        $comment = trim($comment);
        
        if (empty($comment)) {
            echo json_encode(['error' => 'نص التعليق فارغ.']);
            return;
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
{\"reply\": \"أهلاً شكراً جزيلاً لثقتك في منتجاتنا، ونتمنى لك تجربة تسوق مميزة دائماً.\"}";
        
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
        
        $this->callGeminiApi($api_url, $payload);
    }

    public function generateProduct(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!Session::get('user_id') || Session::get('user_role') !== 'admin') {
            echo json_encode(['error' => 'غير مصرح لك بالوصول.']);
            return;
        }
        
        $prompt = $_POST['prompt'] ?? '';
        if (empty($prompt)) {
            $data = json_decode(file_get_contents("php://input"), true);
            $prompt = $data['prompt'] ?? '';
        }
        $prompt = trim($prompt);
        
        if (empty($prompt)) {
            echo json_encode(['error' => 'يرجى تقديم النص المطلوب.']);
            return;
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
        
        $this->callGeminiApi($api_url, $payload);
    }

    public function generateMessageReply(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!Session::get('user_id') || Session::get('user_role') !== 'admin') {
            echo json_encode(['error' => 'غير مصرح لك بالوصول.']);
            return;
        }
        
        $prompt = $_POST['prompt'] ?? '';
        if (empty($prompt)) {
            $data = json_decode(file_get_contents("php://input"), true);
            $prompt = $data['prompt'] ?? '';
        }
        $prompt = trim($prompt);
        
        if (empty($prompt)) {
            echo json_encode(['error' => 'رسالة العميل فارغة.']);
            return;
        }
        
        $api_key = 'AIzaSyBOQ7Ytdg4ruRpHA7cN37kttrpuIs9kvCo';
        $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;
        $system_instruction = "أنت مدير خدمة عملاء محترف في متجر إلكتروني. سأعطيك رسالة أو استفسار من عميل. 
مهمتك: صياغة رد احترافي، لبق، ومناسب لحل المشكلة أو الإجابة على الاستفسار. 
يجب أن يكون الرد باللغة العربية الواضحة.
يجب أن يكون ردك عبارة عن كود JSON نقي فقط هكذا:
{\"reply\": \"نص الرد الاحترافي هنا\"}";
        
        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
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
        
        $this->callGeminiApi($api_url, $payload);
    }

    public function handleChatbot(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        $prompt = $_POST['prompt'] ?? '';
        if (empty($prompt)) {
            $data = json_decode(file_get_contents("php://input"), true);
            $prompt = $data['prompt'] ?? '';
        }
        $prompt = trim($prompt);
        
        if (empty($prompt)) {
            echo json_encode(['error' => 'No prompt provided']);
            return;
        }

        $jsonPath = ROOT_DIR . '/chatbot_data.json';
        if (!file_exists($jsonPath)) {
            echo json_encode(['error' => 'Chatbot data missing']);
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        $intents = $data['intents'] ?? [];
        $lowerPrompt = mb_strtolower($prompt, 'UTF-8');
        
        $bestMatch = null;
        $maxMatches = 0;

        foreach ($intents as $intent) {
            $matches = 0;
            foreach ($intent['keywords'] as $keyword) {
                if (mb_strpos($lowerPrompt, mb_strtolower($keyword, 'UTF-8')) !== false) {
                    $matches++;
                }
            }
            if ($matches > $maxMatches) {
                $maxMatches = $matches;
                $bestMatch = $intent['response'];
            }
        }

        if ($bestMatch) {
            echo json_encode(['reply' => $bestMatch]);
        } else {
            echo json_encode(['reply' => 'عذراً، رسالتك تحتاج إلى تفصيل أكثر، سيقوم الدعم الفني بمراجعتها والرد عليك فوراً.']);
        }
    }

    private function callGeminiApi($api_url, $payload): void {
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
            return;
        }
        if ($http_code !== 200) {
            echo json_encode(['error' => 'خطأ من جوجل (كود ' . $http_code . '): ' . $response]);
            return;
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
                echo json_encode(['error' => 'تعذر فهم استجابة الذكاء الاصطناعي. الرد كان: ' . $generated_text]);
            }
        } else {
            echo json_encode(['error' => 'لم يتم إرجاع أي بيانات مفيدة من الذكاء الاصطناعي.']);
        }
    }
}