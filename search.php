<?php
if(!file_exists("baseInfo.php") || !file_exists("config.php")){
    form("فایل های مورد نیاز یافت نشد");
    exit();
}

require "baseInfo.php";
require "config.php";
include "jdf.php";

// Helper function to extract numeric value from formatted string
function extractNumber($str) {
    if(is_numeric($str)) {
        return floatval($str);
    }
    preg_match('/(\d+\.?\d*)/', $str, $matches);
    return isset($matches[1]) ? floatval($matches[1]) : 0;
}


if(isset($_REQUEST['id'])){
    $config_link = $_REQUEST['id'];

    if(preg_match('/^vmess:\/\/(.*)/',$config_link,$match)){
        $jsonDecode = json_decode(base64_decode($match[1]),true);
        $connectionLink = $config_link;
        $marzbanText = $match[1];
        $config_link = $jsonDecode['id'];
    }elseif(preg_match('/^vless:\/\/(.*?)\@/',$config_link,$match)){
        $connectionLink = $config_link;
        $marzbanText = $config_link = $match[1];
    }elseif(preg_match('/^trojan:\/\/(.*?)\@/',$config_link,$match)){
        $connectionLink = $config_link;
        $marzbanText = $config_link = $match[1];
    }elseif(!preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $config_link)
        && !(preg_match('/^[a-zA-Z0-9]{5,15}/',$config_link))){
        form("متن وارد شده معتبر نمی باشد");
        exit();
    }
    $config_link = htmlspecialchars(stripslashes(trim($config_link)));

    $stmt = $connection->prepare("SELECT * FROM `server_config`");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();
    $found = false;
    $isMarzban = false;
    while($row = $serversList->fetch_assoc()){
        $serverId = $row['id'];
        $serverType = $row['type'];
        
        if($serverType == "marzban"){
            $usersList = getMarzbanJson($serverId)->users;
            if(strstr(json_encode($usersList, JSON_UNESCAPED_UNICODE), $marzbanText) && !empty($marzbanText)){
                $found = true;
                $isMarzban = true;
                
                foreach($usersList as $key => $config){
                    if(strstr(json_encode($config->links, JSON_UNESCAPED_UNICODE), $marzbanText)){
                	    $remark = $config->username;
                        $total = $config->data_limit!=0?sumerize($config->data_limit):"نامحدود";
                        $totalUsed = sumerize($config->used_traffic);
                        $state = $config->status == "active"?$buttonValues['active']:$buttonValues['deactive'];
                        $expiryTime = $config->expire != 0?jdate("Y-m-d H:i:s",$config->expire):"نامحدود";
                        $leftMb = $config->data_limit!=0?$config->data_limit - $config->used_traffic:"نامحدود";
                        
                        if(is_numeric($leftMb)){
                            if($leftMb<0) $leftMb = 0;
                            else $leftMb = sumerize($leftMb);
                        }
                        
                        $expiryDay = $config->expire != 0?
                            floor(
                                ($config->expire - time())/(60 * 60 * 24)
                                ):
                                "نامحدود";    
                        if(is_numeric($expiryDay)){
                            if($expiryDay<0) $expiryDay = 0;
                        }
                        break;
                    }
                }
                break;
            }
        }else{
            $response = getJson($serverId);
            if($response->success){
                $list = json_encode($response->obj);
    
                if(strpos($list, $config_link)){
                    $found = true;
                    $list = $response->obj;
                    if(!isset($list[0]->clientStats)){
                        foreach($list as $keys=>$packageInfo){
                            if(strpos($packageInfo->settings, $config_link)!=false){
                                $remark = $packageInfo->remark;
                                $upload = sumerize2($packageInfo->up);
                                $download = sumerize2($packageInfo->down);
                                $state = $packageInfo->enable == true?"فعال 🟢":"غیر فعال 🔴";
                                $totalUsed = sumerize2($packageInfo->up + $packageInfo->down);
                                $total = $packageInfo->total!=0?sumerize2($packageInfo->total):"نامحدود";
                                $expiryTime = $packageInfo->expiryTime != 0?jdate("Y-m-d H:i:s",substr($packageInfo->expiryTime,0,-3)):"نامحدود";
                                $leftMb = $packageInfo->total!=0?sumerize2($packageInfo->total - $packageInfo->up - $packageInfo->down):"نامحدود";
                                $expiryDay = $packageInfo->expiryTime != 0?
                                    floor(
                                        (substr($packageInfo->expiryTime,0,-3)-time())/(60 * 60 * 24))
                                    :
                                    "نامحدود";
                                if(is_numeric($expiryDay)){
                                    if($expiryDay<0) $expiryDay = 0;
                                }
                                break;
                            }
                        }
                    }
                    else{
                        $keys = -1;
                        $settings = array_column($list,'settings');
                        foreach($settings as $key => $value){
                            if(strpos($value, $config_link)!= false){
                                $keys = $key;
                                break;
                            }
                        }
                        if($keys == -1){
                            $found = false;
                            break;
                        }
                        $clientsSettings = json_decode($list[$keys]->settings,true)['clients'];
                        if(!is_array($clientsSettings)){
                            form("با عرض پوزش، متأسفانه مشکلی رخ داده است، لطفا مجدد اقدام کنید");
                            exit();
                        }
                        $settingsId = array_column($clientsSettings,'id');
                        $settingKey = array_search($config_link,$settingsId);
    
                        if(!isset($clientsSettings[$settingKey]['email'])){
                            $packageInfo = $list[$keys];
                            $remark = $packageInfo->remark;
                            $upload = sumerize2($packageInfo->up);
                            $download = sumerize2($packageInfo->down);
                            $state = $packageInfo->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $totalUsed = sumerize2($packageInfo->up + $packageInfo->down);
                            $total = $packageInfo->total!=0?sumerize2($packageInfo->total):"نامحدود";
                            $expiryTime = $packageInfo->expiryTime != 0?jdate("Y-m-d H:i:s",substr($packageInfo->expiryTime,0,-3)):"نامحدود";
                            $leftMb = $packageInfo->total!=0?sumerize2($packageInfo->total - $packageInfo->up - $packageInfo->down):"نامحدود";
                            if(is_numeric($leftMb)){
                                if($leftMb<0){
                                    $leftMb = 0;
                                }else{
                                    $leftMb = sumerize2($packageInfo->total - $packageInfo->up - $packageInfo->down);
                                }
                            }
    
    
                            $expiryDay = $packageInfo->expiryTime != 0?
                                floor(
                                    (substr($packageInfo->expiryTime,0,-3)-time())/(60 * 60 * 24)
                                ):
                                "نامحدود";
                            if(is_numeric($expiryDay)){
                                if($expiryDay<0) $expiryDay = 0;
                            }
                        }else{
                            $email = $clientsSettings[$settingKey]['email'];
                            $clientState = $list[$keys]->clientStats;
                            $emails = array_column($clientState,'email');
                            $emailKey = array_search($email,$emails);
                            if($clientState[$emailKey]->total != 0 || $clientState[$emailKey]->up != 0  ||  $clientState[$emailKey]->down != 0 || $clientState[$emailKey]->expiryTime != 0){
                                $upload = sumerize2($clientState[$emailKey]->up);
                                $download = sumerize2($clientState[$emailKey]->down);
                                $total = $clientState[$emailKey]->total==0 && $list[$keys]->total !=0?$list[$keys]->total:$clientState[$emailKey]->total;
                                $leftMb = $total!=0?($total - $clientState[$emailKey]->up - $clientState[$emailKey]->down):"نامحدود";
                                if(is_numeric($leftMb)){
                                    if($leftMb<0){
                                        $leftMb = 0;
                                    }else{
                                        $leftMb = sumerize2($total - $clientState[$emailKey]->up - $clientState[$emailKey]->down);
                                    }
                                }
                                $totalUsed = sumerize2($clientState[$emailKey]->up + $clientState[$emailKey]->down);
                                $total = $total!=0?sumerize2($total):"نامحدود";
                                $expTime = $clientState[$emailKey]->expiryTime == 0 && $list[$keys]->expiryTime?$list[$keys]->expiryTime:$clientState[$emailKey]->expiryTime;
                                $expiryTime = $expTime != 0?jdate("Y-m-d H:i:s",substr($expTime,0,-3)):"نامحدود";
                                $expiryDay = $expTime != 0?
                                    floor(
                                        ((substr($expTime,0,-3)-time())/(60 * 60 * 24))
                                    ):
                                    "نامحدود";
                                if(is_numeric($expiryDay)){
                                    if($expiryDay<0) $expiryDay = 0;
                                }
                                $state = $clientState[$emailKey]->enable == true?"فعال 🟢":"غیر فعال 🔴";
                                $remark = $email;
                            }
                            elseif($list[$keys]->total != 0 || $list[$keys]->up != 0  ||  $list[$keys]->down != 0 || $list[$keys]->expiryTime != 0){
                                $upload = sumerize2($list[$keys]->up);
                                $download = sumerize2($list[$keys]->down);
                                $leftMb = $list[$keys]->total!=0?($list[$keys]->total - $list[$keys]->up - $list[$keys]->down):"نامحدود";
                                if(is_numeric($leftMb)){
                                    if($leftMb<0){
                                        $leftMb = 0;
                                    }else{
                                        $leftMb = sumerize2($list[$keys]->total - $list[$keys]->up - $list[$keys]->down);
                                    }
                                }
                                $totalUsed = sumerize2($list[$keys]->up + $list[$keys]->down);
                                $total = $list[$keys]->total!=0?sumerize2($list[$keys]->total):"نامحدود";
                                $expiryTime = $list[$keys]->expiryTime != 0?jdate("Y-m-d H:i:s",substr($list[$keys]->expiryTime,0,-3)):"نامحدود";
                                $expiryDay = $list[$keys]->expiryTime != 0?
                                    floor(
                                        ((substr($list[$keys]->expiryTime,0,-3)-time())/(60 * 60 * 24))
                                    ):
                                    "نامحدود";
                                if(is_numeric($expiryDay)){
                                    if($expiryDay<0) $expiryDay = 0;
                                }
                                $state = $list[$keys]->enable == true?"فعال 🟢":"غیر فعال 🔴";
                                $remark = $list[$keys]->remark;
                            }
                        }
                    }
                    break;
                }
            }
        }
    }
    if(!$found){
        form("اطلاعات وارد شده اشتباه می باشد",$cancelKey);
    }else{
        showForm("configInfo");
    }
}
else{
    showForm("unknown");
}
?>
<?php
function showForm($type){
    global $remark, $isMarzban, $totalUsed, $state, $upload, $download, $total, $leftMb, $expiryTime, $expiryDay;
    
    // Calculate percentages for progress circles
    if ($type == "configInfo") {
        // Extract numeric values from formatted strings
        $totalNum = extractNumber($total);
        $totalUsedNum = extractNumber($totalUsed);
        $downloadNum = extractNumber($download);
        $uploadNum = extractNumber($upload);
        $leftMbNum = ($leftMb != "نامحدود") ? extractNumber($leftMb) : 0;
        
        if($isMarzban) {
            // For Marzban, calculate used percentage
            if($totalNum > 0 && $total != "نامحدود") {
                $usedPercent = min(100, max(0, ($totalUsedNum / $totalNum) * 100));
            } else {
                // If unlimited, show 0%
                $usedPercent = 0;
            }
            $downloadPercent = $usedPercent;
            $uploadPercent = 0;
        } else {
            // For x-ui, calculate percentages based on total
            if($totalNum > 0 && $total != "نامحدود") {
                $downloadPercent = min(100, max(0, ($downloadNum / $totalNum) * 100));
                $uploadPercent = min(100, max(0, ($uploadNum / $totalNum) * 100));
                $usedPercent = min(100, max(0, (($downloadNum + $uploadNum) / $totalNum) * 100));
            } else {
                // If unlimited, show estimated percentages
                $downloadPercent = min(50, $downloadNum); // Cap at 50% for visual purposes
                $uploadPercent = min(30, $uploadNum);
                $usedPercent = $downloadPercent + $uploadPercent;
            }
        }
        
        // Calculate remaining percentage
        if($totalNum > 0 && $total != "نامحدود" && $leftMbNum > 0) {
            $remainingPercent = min(100, max(0, ($leftMbNum / $totalNum) * 100));
        } else {
            $remainingPercent = max(0, 100 - $usedPercent);
        }
        
        // Ensure percentages are within valid range
        $downloadPercent = min(100, max(0, round($downloadPercent, 1)));
        $uploadPercent = min(100, max(0, round($uploadPercent, 1)));
        $usedPercent = min(100, max(0, round($usedPercent, 1)));
        $remainingPercent = min(100, max(0, round($remainingPercent, 1)));
        
        // Determine status color
        $statusClass = (strpos($state, 'فعال') !== false || strpos($state, '🟢') !== false) ? 'status-active' : 'status-inactive';
        $statusText = $state;
    }
    ?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php if($type=="unknown") echo "جستجوی اطلاعات کانفیگ";
            elseif ($type=="configInfo") echo "اطلاعات کانفیگ - " . htmlspecialchars($remark);
            ?></title>
        <link type="text/css" href="assets/webconf.css" rel="stylesheet" />
    </head>
    <body>
    <?php if ($type=="configInfo"){
        ?>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">📊 اطلاعات کانفیگ</h1>
                    <p class="card-subtitle"><?php echo htmlspecialchars($remark); ?></p>
                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div class="stat-label"><?php echo $isMarzban ? "حجم استفاده شده" : "حجم دانلود"; ?></div>
                        <div class="stat-value"><?php echo htmlspecialchars($isMarzban ? $totalUsed : $download); ?></div>
                        <div class="progress-wrapper mt-2">
                            <div class="progress-circle <?php echo $isMarzban ? 'used' : 'download'; ?>" style="--progress: <?php echo ($isMarzban ? $usedPercent : $downloadPercent) * 3.6; ?>deg;">
                                <span><?php echo number_format($isMarzban ? $usedPercent : $downloadPercent, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <?php if(!$isMarzban){ ?>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--warning-color);">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </div>
                        <div class="stat-label">حجم آپلود</div>
                        <div class="stat-value"><?php echo htmlspecialchars($upload); ?></div>
                        <div class="progress-wrapper mt-2">
                            <div class="progress-circle upload" style="--progress: <?php echo $uploadPercent * 3.6; ?>deg;">
                                <span><?php echo number_format($uploadPercent, 1); ?>%</span>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--success-color);">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="stat-label">حجم باقیمانده</div>
                        <div class="stat-value"><?php echo htmlspecialchars($leftMb); ?></div>
                        <div class="progress-wrapper mt-2">
                            <div class="progress-circle remaining" style="--progress: <?php echo $remainingPercent * 3.6; ?>deg;">
                                <span><?php echo number_format($remainingPercent, 1); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--info-color);">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <div class="stat-label">حجم کلی</div>
                        <div class="stat-value"><?php echo htmlspecialchars($total); ?></div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">⏰ تاریخ انقضا:</span>
                        <span class="info-value"><?php echo htmlspecialchars($expiryTime); ?></span>
                    </div>
                    <?php if(isset($expiryDay) && $expiryDay != "نامحدود"){ ?>
                    <div class="info-row">
                        <span class="info-label">📅 روزهای باقیمانده:</span>
                        <span class="info-value"><?php echo htmlspecialchars($expiryDay); ?> روز</span>
                    </div>
                    <?php } ?>
                </div>

                <div class="footer">
                    <p>ساخته شده با ❤️ در <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank">WizWiz</a></p>
                </div>
            </div>
        </div>

    <?php }
    elseif($type=="unknown"){ ?>
        <div class="container">
            <div class="form-container">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">🔍 جستجوی اطلاعات کانفیگ</h1>
                        <p class="card-subtitle">لطفا لینک اتصال یا UUID کانفیگ خود را وارد کنید</p>
                    </div>
                    <form action="search.php" method="get">
                        <div class="form-group">
                            <input 
                                class="form-input" 
                                type="text" 
                                id="id" 
                                name="id" 
                                placeholder="لینک اتصال یا UUID کانفیگ را وارد کنید..."
                                autocomplete="off" 
                                required 
                            >
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                🔎 جستجو
                            </button>
                        </div>
                    </form>
                    <div class="footer">
                        <p>ساخته شده با ❤️ در <a href="https://github.com/wizwizdev/wizwizxui-timebot" target="_blank">WizWiz</a></p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </body>
    </html>
    <?php
}
function form($msg, $error = true){
    ?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>خطا</title>
        <link type="text/css" href="assets/webconf.css" rel="stylesheet" />
    </head>
    <body>
        <div class="error-container">
            <div class="card" style="max-width: 500px;">
                <?php if ($error == true){ ?> 
                    <svg class="error-icon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                <?php } ?>
                <h2 class="card-title"><?php echo $error ? 'خطا' : 'اطلاعیه'; ?></h2>
                <p class="error-message"><?php echo htmlspecialchars($msg); ?></p>
                <div class="mt-3">
                    <a href="search.php" class="btn btn-primary">بازگشت به صفحه اصلی</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
