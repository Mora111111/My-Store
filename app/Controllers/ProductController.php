<?php
class ProductController {
    public function index(): void {
        $productModel = new Product();
        $searchQuery = trim($_GET['search'] ?? '');
        if (!empty($searchQuery)) {$allProducts = $productModel->search($searchQuery);
        } else {
            $allProducts =$productModel->getAll();
        }
        $categories =$productModel->getCategories();
        $catMap = [];$catCounter = 1;
        foreach ($categories as $catName) {$catMap[$catName] = 'cat_' .$catCounter;
            $catCounter++;
        }
        
        $globalCouponModel = new Coupon();
        $activeCouponsRaw =$globalCouponModel->getActiveStrikethroughCoupons();
        
        // Sort percentage first, then fixed, descending value
        usort($activeCouponsRaw, function($a,$b) {
            if ($a['discount_type'] ===$b['discount_type']) return $b['discount_value'] <=>$a['discount_value'];
            return $a['discount_type'] === 'percentage' ? -1 : 1;
        });
        $activeCoupons =$activeCouponsRaw;
        
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/products.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
    public function show(): void {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            echo "Product not found";
            return;
        }
        $productModel = new Product();
        $product = $productModel->findById((int)$id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        $hasPurchased = false;
        if (Session::isLoggedIn()) {
            $orderModel = new Order();
            $hasPurchased = $orderModel->hasPurchasedProduct(Session::get('user_id'), $product['title']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
            if (!Session::isLoggedIn() || !$hasPurchased) {
                header("Location: /product?id=" . $id . "#comments-section");
                exit();
            }

            $customerName = trim($_POST['customer_name'] ?? '');
            $commentText = trim($_POST['comment_text'] ?? '');
            $userRating = intval($_POST['user_rating'] ?? 5);
            if ($userRating < 1 || $userRating > 5) $userRating = 5;

            if (!empty($customerName) && !empty($commentText)) {
                $positiveWords = ['ممتاز', 'روعة', 'جميل', 'حلو', 'تحفة', 'شكرا', 'عظيم', 'عجبني', 'سريع', 'اصلي', 'جيد', 'رائع', 'مذهل', 'خيالي', 'مضبوط', 'عاش', 'تسلم', 'مية مية', 'بيرفكت', 'اسطوري', 'جودة عالية', 'افضل', 'احسن', 'رهيب', 'بطل', 'تغليف ممتاز', 'محترمين', 'ثقة'];
                $negativeWords = ['سيء', 'وحش', 'مكسور', 'تأخير', 'بطيء', 'غالي', 'مقلد', 'مش شغال', 'زفت', 'نصابين', 'مش كويس', 'بايظ', 'تالف', 'زبالة', 'أسوأ', 'عطلان', 'نصب', 'كدب', 'رديء', 'مضروب', 'غلط', 'مخدوع', 'مفيش مصداقية', 'تجربة سيئة', 'لا انصح', 'عيب'];
                $inquiryWords = ['بكام', 'سعر', 'امتى', 'توصيل', 'ضمان', 'متوفر', 'الوان', 'مقاس', 'كم', 'استفسار', 'فين', 'تفاصيل', 'شحن', 'مصاريف', 'إمتى', 'كام', 'مكانكم', 'فرع', 'ازاي', 'ممكن', 'هل يوجد', 'لو سمحت', 'طريقة الدفع', 'تقسيط'];
                $positiveScore = 0; $negativeScore = 0; $inquiryScore = 0;
                $lowerComment = mb_strtolower($commentText, 'UTF-8');
                foreach ($positiveWords as $word) { if (mb_strpos($lowerComment, $word) !== false) $positiveScore++; }
                foreach ($negativeWords as $word) { if (mb_strpos($lowerComment, $word) !== false) $negativeScore++; }
                foreach ($inquiryWords as $word) { if (mb_strpos($lowerComment, $word) !== false) $inquiryScore++; }
                
                $positiveReplies = ["أهلاً يا {$customerName}، الشكر موصول لثقتك في متجرنا، ونتمنى دائماً تقديم الأفضل! 😊", "هذا التقييم يسعد إدارة المتجر جداً يا {$customerName}، شرفنا التعامل معك ونتمنى لك تجربة استخدام موفقة.", "يا مرحباً بك، كلمات الإشادة نعتز بها، شكراً لاختيارك تكنو ستور.", "تسلم يا {$customerName} على هذا الرأي الجميل، رضاك هو هدفنا الأول."];
                $negativeReplies = ["أهلاً يا {$customerName}، نعتذر بشدة عن هذه التجربة غير المرضية، سيتم التواصل معك من الدعم الفني فوراً لحل المشكلة.", "يؤسفنا جداً هذا الخطأ يا فندم، فريق المتابعة سيقوم بالاتصال بك حالا لحل الموضوع بشكل يرضيك تماماً.", "اعتذار بالغ عن أي تقصير. يرجى ترك الأمر لنا وسيتم حله أو استبدال المنتج في أسرع وقت ممكن."];
                $inquiryReplies = ["أهلاً يا {$customerName}، تم تحويل استفسارك لقسم المبيعات وسيتم الرد عليك بالتفاصيل حالا.", "مرحباً بك، استفساراتك تهمنا جداً، سيتم توضيح كل التفاصيل في رسالة خاصة قريباً.", "أهلاً بحضرتك، سيتم مراجعة طلبك والرد عليك بكل التفاصيل المطلوبة."];
                $neutralReplies = ["أهلاً يا {$customerName}، تم تسجيل تعليقك بنجاح وسيتم مراجعته من الإدارة.", "مرحباً بك، تفاعلك في تكنو ستور محل تقدير كبير لدينا.", "شكراً لمرورك وتعليقك، يسعدنا تواصلك الدائم."];
                
                if ($negativeScore > 0) { $aiReply = $negativeReplies[array_rand($negativeReplies)]; } 
                elseif ($inquiryScore > 0) { $aiReply = $inquiryReplies[array_rand($inquiryReplies)]; } 
                elseif ($positiveScore > 0) { $aiReply = $positiveReplies[array_rand($positiveReplies)]; } 
                else { $aiReply = $neutralReplies[array_rand($neutralReplies)]; }
                
                $commentModel = new Comment();
                $commentModel->createComment((int)$id, $customerName, $commentText, $aiReply, $userRating);
                $commentModel->updateProductAverageRating((int)$id);
            }
            header("Location: /product?id=" . $id . "#comments-section");
            exit();
        }

        $commentModel = new Comment();
        $comments = $commentModel->getByProductId((int)$id);
        $productUpdated = (new Product())->findById((int)$id);
        $rating = isset($productUpdated['rating']) ? (float)$productUpdated['rating'] : 0;
        
        $starsHtml = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($rating >= $i) { $starsHtml .= '<i class="fa-solid fa-star"></i>'; } 
            elseif ($rating >= $i - 0.5) { $starsHtml .= '<i class="fa-regular fa-star-half-stroke fa-flip-horizontal"></i>'; } 
            else { $starsHtml .= '<i class="fa-regular fa-star"></i>'; }
        }
        $priceParts = explode('.', number_format($productUpdated['price'], 2, '.', ''));
        $mainPrice = $priceParts[0]; $decimals = $priceParts[1];
        
        require_once APP_DIR . '/Views/layouts/header.php';
        require_once APP_DIR . '/Views/pages/product_details.php';
        require_once APP_DIR . '/Views/layouts/footer.php';
    }
}
