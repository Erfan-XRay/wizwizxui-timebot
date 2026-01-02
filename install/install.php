<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if baseInfo.php exists (already installed)
if(file_exists("../baseInfo.php")){
if(isset($_REQUEST['updateBot'])){
	require "update.php";
	require "../baseInfo.php";
	
	$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);
	
	if($connection->connect_error){
            echo "<!DOCTYPE html>
            <html dir='rtl' lang='fa'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>خطا - WizWiz XUI TimeBot</title>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                    .container { background: white; border-radius: 15px; padding: 40px; max-width: 600px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
                    h1 { color: #e74c3c; text-align: center; margin-bottom: 20px; }
                    .error { background: #fee; border-right: 4px solid #e74c3c; padding: 15px; border-radius: 5px; color: #c0392b; }
                    .btn { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                    .btn:hover { background: #5568d3; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1>❌ خطا در اتصال به دیتابیس</h1>
                    <div class='error'>خطای دیتابیس: " . htmlspecialchars($connection->connect_error) . "</div>
                    <a href='install.php' class='btn'>بازگشت</a>
                </div>
            </body>
            </html>";
	    exit();
	}
    
    updateBot();
        echo "<!DOCTYPE html>
        <html dir='rtl' lang='fa'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>به‌روزرسانی موفق - WizWiz XUI TimeBot</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                .container { background: white; border-radius: 15px; padding: 40px; max-width: 600px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; }
                h1 { color: #27ae60; margin-bottom: 20px; }
                .success { background: #d4edda; border-right: 4px solid #27ae60; padding: 15px; border-radius: 5px; color: #155724; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>✅ به‌روزرسانی با موفقیت انجام شد!</h1>
                <div class='success'>دیتابیس به‌روزرسانی شد.</div>
            </div>
        </body>
        </html>";
        exit();
    }
    
    // Show update page
    echo "<!DOCTYPE html>
    <html dir='rtl' lang='fa'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>به‌روزرسانی - WizWiz XUI TimeBot</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; display: flex; align-items: center; justify-content: center; }
            .container { background: white; border-radius: 15px; padding: 40px; max-width: 600px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
            h1 { color: #333; text-align: center; margin-bottom: 10px; }
            .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
            .info { background: #e3f2fd; border-right: 4px solid #2196f3; padding: 15px; border-radius: 5px; margin-bottom: 20px; color: #1565c0; }
            .btn { display: block; width: 100%; padding: 15px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; text-align: center; margin-top: 20px; border: none; cursor: pointer; font-size: 16px; }
            .btn:hover { background: #5568d3; }
            .btn-danger { background: #e74c3c; }
            .btn-danger:hover { background: #c0392b; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>🔄 به‌روزرسانی دیتابیس</h1>
            <p class='subtitle'>WizWiz XUI TimeBot - Modified by ErfanXRay</p>
            <div class='info'>
                ⚠️ این عملیات دیتابیس را به‌روزرسانی می‌کند. لطفا قبل از ادامه از دیتابیس بک‌آپ بگیرید.
            </div>
            <form method='POST'>
                <input type='hidden' name='updateBot' value='1'>
                <button type='submit' class='btn'>به‌روزرسانی دیتابیس</button>
            </form>
        </div>
    </body>
    </html>";
    exit();
}

// Installation form
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($step == 1){
        $dbHost = $_POST['db_host'] ?? 'localhost';
        $dbUser = $_POST['db_user'] ?? 'root';
        $dbPass = $_POST['db_pass'] ?? '';
        $dbName = $_POST['db_name'] ?? 'wizwiz';
        $botToken = $_POST['bot_token'] ?? '';
        $botUrl = $_POST['bot_url'] ?? '';
        $adminId = $_POST['admin_id'] ?? '';
        
        // Validate inputs
        if(empty($botToken)) $errors[] = "توکن ربات الزامی است";
        if(empty($botUrl)) $errors[] = "آدرس ربات الزامی است";
        if(empty($adminId)) $errors[] = "آیدی ادمین الزامی است";
        if(empty($dbName)) $errors[] = "نام دیتابیس الزامی است";
        
        if(empty($errors)){
            // Test database connection
            $testConnection = new mysqli($dbHost, $dbUser, $dbPass);
            if($testConnection->connect_error){
                $errors[] = "خطا در اتصال به دیتابیس: " . $testConnection->connect_error;
            } else {
                // Create database if not exists
                $testConnection->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $testConnection->close();
                
                // Create baseInfo.php
                $baseInfoContent = "<?php\n";
                $baseInfoContent .= "error_reporting(0);\n";
                $baseInfoContent .= "\$dbUserName = '$dbUser';\n";
                $baseInfoContent .= "\$dbPassword = '$dbPass';\n";
                $baseInfoContent .= "\$dbName = '$dbName';\n";
                $baseInfoContent .= "\$botToken = '$botToken';\n";
                $baseInfoContent .= "\$botUrl = '$botUrl';\n";
                $baseInfoContent .= "\$admin = $adminId;\n";
                $baseInfoContent .= "?>";
                
                if(file_put_contents("../baseInfo.php", $baseInfoContent)){
                    // Create database tables
                    $connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
                    if(!$connection->connect_error){
                        $connection->set_charset("utf8mb4");
                        
                        // Execute createDB.php
                        $createDBFile = "../createDB.php";
                        if(file_exists($createDBFile)){
                            // Create temporary baseInfo.php for createDB.php
                            $tempBaseInfo = "<?php\n\$dbUserName = '$dbUser';\n\$dbPassword = '$dbPass';\n\$dbName = '$dbName';\n?>";
                            file_put_contents("../temp_baseInfo.php", $tempBaseInfo);
                            
                            // Save original baseInfo if exists
                            $originalBaseInfoExists = file_exists("../baseInfo.php");
                            if($originalBaseInfoExists){
                                rename("../baseInfo.php", "../baseInfo.php.backup");
                            }
                            
                            // Copy temp to baseInfo
                            copy("../temp_baseInfo.php", "../baseInfo.php");
                            
                            // Now include createDB.php
                            ob_start();
                            include $createDBFile;
                            ob_end_clean();
                            
                            // Restore original baseInfo if it existed
                            if($originalBaseInfoExists && file_exists("../baseInfo.php.backup")){
                                unlink("../baseInfo.php");
                                rename("../baseInfo.php.backup", "../baseInfo.php");
                            } else {
                                // Update baseInfo.php with bot info
                                $baseInfoContent = "<?php\n";
                                $baseInfoContent .= "error_reporting(0);\n";
                                $baseInfoContent .= "\$dbUserName = '$dbUser';\n";
                                $baseInfoContent .= "\$dbPassword = '$dbPass';\n";
                                $baseInfoContent .= "\$dbName = '$dbName';\n";
                                $baseInfoContent .= "\$botToken = '$botToken';\n";
                                $baseInfoContent .= "\$botUrl = '$botUrl';\n";
                                $baseInfoContent .= "\$admin = $adminId;\n";
                                $baseInfoContent .= "?>";
                                file_put_contents("../baseInfo.php", $baseInfoContent);
                            }
                            
                            // Clean up
                            @unlink("../temp_baseInfo.php");
                            
                            // Set webhook
                            if(!empty($botToken) && !empty($botUrl)){
                                $webhookUrl = rtrim($botUrl, '/') . '/bot.php';
                                $setWebhookUrl = "https://api.telegram.org/bot{$botToken}/setWebhook?url=" . urlencode($webhookUrl);
                                @file_get_contents($setWebhookUrl);
                                
                                // Send success message
                                $message = "✅ ربات WizWiz با موفقیت نصب شد!\n\n✨ This version has been modified and maintained by ErfanXRay\n🔗 GitHub: https://github.com/Erfan-XRay/wizwizxui-timebot\n📋 Supports: Sanaei XUI Panel Only";
                                $sendMessageUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
                                $postData = http_build_query([
                                    'chat_id' => $adminId,
                                    'text' => $message
                                ]);
                                $ch = curl_init($sendMessageUrl);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_exec($ch);
                                curl_close($ch);
                            }
                            
                            $success = true;
                        } else {
                            $errors[] = "فایل createDB.php یافت نشد!";
                        }
                        $connection->close();
                    } else {
                        $errors[] = "خطا در اتصال به دیتابیس: " . $connection->connect_error;
                    }
                } else {
                    $errors[] = "خطا در ایجاد فایل baseInfo.php. لطفا دسترسی نوشتن را بررسی کنید.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب WizWiz XUI TimeBot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .version-info {
            background: #e3f2fd;
            border-right: 4px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            color: #1565c0;
            font-size: 13px;
        }
        
        .version-info strong {
            display: block;
            margin-bottom: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .error {
            background: #fee;
            border-right: 4px solid #e74c3c;
            padding: 12px;
            border-radius: 5px;
            color: #c0392b;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .success {
            background: #d4edda;
            border-right: 4px solid #27ae60;
            padding: 15px;
            border-radius: 5px;
            color: #155724;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 500;
        }
        
        .btn:hover {
            background: #5568d3;
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }
        
        .step.active .step-number {
            background: #667eea;
            color: white;
        }
        
        .step.completed .step-number {
            background: #27ae60;
            color: white;
        }
        
        .step-title {
            font-size: 12px;
            color: #666;
        }
        
        .step.active .step-title {
            color: #667eea;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 نصب WizWiz XUI TimeBot</h1>
            <p class="subtitle">Modified and maintained by ErfanXRay</p>
            <div class="version-info">
                <strong>⚠️ توجه مهم:</strong>
                این نسخه فقط از پنل <strong>Sanaei XUI</strong> پشتیبانی می‌کند.
                لطفا مطمئن شوید که پنل Sanaei XUI شما به آخرین نسخه آپدیت شده است.
            </div>
        </div>
        
        <?php if($success): ?>
            <div class="success">
                <h2>✅ نصب با موفقیت انجام شد!</h2>
                <p style="margin-top: 10px;">دیتابیس و فایل‌های مورد نیاز ایجاد شدند.</p>
                <p style="margin-top: 10px; font-size: 12px;">
                    لطفا این پوشه install را حذف کنید و ربات را در تلگرام تست کنید.
                </p>
            </div>
        <?php else: ?>
            <div class="steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-title">اطلاعات دیتابیس</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-title">اطلاعات ربات</div>
                </div>
            </div>
            
            <?php if(!empty($errors)): ?>
                <div class="error">
                    <strong>خطاها:</strong><br>
                    <?php foreach($errors as $error): ?>
                        • <?php echo htmlspecialchars($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="install.php">
                <div class="form-group">
                    <label for="db_host">آدرس دیتابیس (Host):</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                    <span class="help-text">معمولاً localhost است</span>
                </div>
                
                <div class="form-group">
                    <label for="db_user">نام کاربری دیتابیس:</label>
                    <input type="text" id="db_user" name="db_user" value="root" required>
                </div>
                
                <div class="form-group">
                    <label for="db_pass">رمز عبور دیتابیس:</label>
                    <input type="password" id="db_pass" name="db_pass">
                    <span class="help-text">اگر رمز ندارید، خالی بگذارید</span>
                </div>
                
                <div class="form-group">
                    <label for="db_name">نام دیتابیس:</label>
                    <input type="text" id="db_name" name="db_name" value="wizwiz" required>
                    <span class="help-text">اگر وجود نداشته باشد، به صورت خودکار ایجاد می‌شود</span>
                </div>
                
                <div class="form-group">
                    <label for="bot_token">توکن ربات تلگرام:</label>
                    <input type="text" id="bot_token" name="bot_token" placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz" required>
                    <span class="help-text">از @BotFather دریافت کنید</span>
                </div>
                
                <div class="form-group">
                    <label for="bot_url">آدرس کامل ربات (با https):</label>
                    <input type="text" id="bot_url" name="bot_url" placeholder="https://yourdomain.com/wizwizxui-timebot/" required>
                    <span class="help-text">مثال: https://yourdomain.com/wizwizxui-timebot/</span>
                </div>
                
                <div class="form-group">
                    <label for="admin_id">آیدی عددی ادمین:</label>
                    <input type="number" id="admin_id" name="admin_id" placeholder="123456789" required>
                    <span class="help-text">از @userinfobot دریافت کنید</span>
                </div>
                
                <button type="submit" class="btn">نصب و ایجاد دیتابیس</button>
            </form>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #666;">
            <p>GitHub: <a href="https://github.com/Erfan-XRay/wizwizxui-timebot" target="_blank">Erfan-XRay/wizwizxui-timebot</a></p>
        </div>
    </div>
</body>
</html>
