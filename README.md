# CorpChat - Enterprise Hybrid Messaging System

CorpChat هو نظام مراسلة مؤسسي متكامل يجمع بين المحادثات الداخلية الفورية وإدارة رسائل البريد الإلكتروني الخارجية للعملاء في منصة واحدة موحدة. تم بناء النظام باستخدام تقنيات حديثة وراقية تضمن الأداء العالي والمظهر العصري.

---

## الميزات الرئيسية (Key Features)

1. **محادثات داخلية فورية (Real-time Internal Chat)**:
   - بث مباشر فوري للرسائل باستخدام **Laravel Reverb (WebSockets)** دون الحاجة لإعادة تحميل الصفحة.
   - مؤشرات ذكية لحالة إرسال وقراءة الرسائل (Sending, Sent, Delivered).
   - واجهة مستخدم مبنية بأسلوب **Glassmorphism** الأنيق لتجربة بصرية فريدة.

2. **تكامل كامل للبريد الإلكتروني (Hostinger IMAP/SMTP Integration)**:
   - استقبال رسائل البريد الإلكتروني الخاصة بالعملاء ومزامنتها تلقائياً مع المحادثات عبر بروتوكول **IMAP** (باستخدام حزمة `webklex/laravel-imap`).
   - إرسال الردود إلى العملاء فورياً عبر بروتوكول **SMTP**.
   - تحديثات حالة تسليم البريد بالوقت الفعلي.

3. **لوحة تحكم إدارية كاملة (Filament Control Panel)**:
   - إدارة كاملة للموظفين والمدراء وصلاحياتهم (`UserResource`).
   - استعراض وإدارة المحادثات الجارية (`ConversationResource`).
   - إدارة وتعديل كامل لكافة الرسائل وحالاتها (`MessageResource`) والمجموعات.
   - ميزة تفاعلية لمشاهدة رسائل المحادثة داخل تفاصيلها (Relation Manager).

---

## متطلبات التشغيل (System Requirements)

- نظام التشغيل: Windows / Linux / macOS.
- تثبيت **Docker** و **Docker Compose**.

---

## إعداد وتشغيل المشروع (Setup & Execution)

### 1. إعداد البيئة وتكوين الاتصال:
قم بإنشاء ملف `.env` (أو تعديل الملف الحالي) وإعداد البيانات التالية:

```env
APP_NAME=CorpChat
APP_URL=http://localhost

# قاعدة البيانات (MySQL 8.0)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=corpchat
DB_USERNAME=corpchat
DB_PASSWORD=secret

# البث المباشر (Laravel Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=corpchat_id
REVERB_APP_KEY=corpchat_key
REVERB_APP_SECRET=corpchat_secret
REVERB_HOST=reverb-server
REVERB_PORT=8080
REVERB_SCHEME=http

# بريد إرسال العملاء (Hostinger SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@company.com
MAIL_PASSWORD=your_hostinger_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your_email@company.com

# بريد استقبال العملاء (Hostinger IMAP)
IMAP_HOST=imap.hostinger.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_USERNAME=your_email@company.com
IMAP_PASSWORD=your_hostinger_password
IMAP_DEFAULT_ACCOUNT=default
```

### 2. تشغيل النظام باستخدام Docker Compose:
قم بتشغيل الأمر التالي لبناء الحاويات وتشغيل الخدمات:
```bash
docker compose up -d --build
```

سيبدأ تشغيل الخدمات التالية تلقائياً:
- **`corpchat_web`**: خادم Nginx للموقع الرئيسي ولوحة التحكم (منفذ `80`).
- **`corpchat_app`**: تطبيق Laravel 12 (PHP 8.3 FPM).
- **`corpchat_db`**: قاعدة بيانات MySQL 8.0.
- **`corpchat_redis`**: ذاكرة التخزين المؤقت وتشغيل الطوابير (Queue).
- **`corpchat_reverb`**: خادم البث الفوري WebSockets (منفذ `8080`).
- **`corpchat_queue_worker`**: خادم معالجة الطوابير والمهام في الخلفية.

### 3. روابط الوصول (Access Links):

- **بوابة المحادثة الرئيسية (Employee Chat Portal)**:
  [http://localhost](http://localhost)
  *(تقوم بتسجيل دخولك تلقائياً كمدير للتجربة الفورية)*

- **لوحة تحكم المدير (Admin Dashboard)**:
  [http://localhost/admin](http://localhost/admin)

- **بيانات الدخول الافتراضية للمدير**:
  - **البريد الإلكتروني**: `admin@corpchat.test`
  - **كلمة المرور**: `password`

### 4. مزامنة البريد الإلكتروني الوارد (IMAP):
لتشغيل سحب الرسائل ومزامنتها من خادم البريد، قم بتشغيل الأمر التالي:
```bash
docker exec corpchat_app php artisan emails:sync
```

*(يمكنك وضع هذا الأمر مجدولاً في الـ Cron Job ليتم سحب الرسائل تلقائياً كل دقيقة).*

---

## هيكلية المشروع والتقنيات (Tech Stack)

- **Backend**: Laravel 12.x + PHP 8.3
- **Frontend**: Blade HTML5 + Vanilla JS (Reactivity via Laravel Echo) + Vanilla CSS3
- **Database**: MySQL 8.0
- **Caching & Queue**: Redis
- **Real-time WebSockets**: Laravel Reverb
- **Admin Framework**: Filament PHP v3
