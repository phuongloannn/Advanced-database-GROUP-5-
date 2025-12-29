<?php 
include('functions/userfunctions.php');
include('includes/header.php');
?>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white">Trang chủ / </a>
            <a href="products.php" class="text-white">Sản phẩm</a>
        </h6>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Tất cả sản phẩm</h1>
                <hr>
                <div class="row">
                    <?php
                    $products = getAllActive("products");
                    if(mysqli_num_rows($products) > 0) {
                        foreach($products as $item) {
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card product_data">
                            <div class="card-body">
                                <img src="anh_xedap/<?= $item['image']; ?>" alt="<?= $item['name']; ?>" 
                                     class="w-100 mb-3" style="height: 200px; object-fit: cover;">
                                <h5 class="card-title text-center"><?= $item['name']; ?></h5>
                                <p class="card-text">
                                    <span class="float-start text-danger fw-bold">
                                        <?= number_format($item['selling_price'], 0, ',', '.') ?> VND
                                    </span>
                                    <s class="float-end text-secondary">
                                        <?= number_format($item['original_price'], 0, ',', '.') ?> VND
                                    </s>
                                </p>
                                <div class="clearfix mb-3"></div>
                                <input type="hidden" class="prod_id" value="<?= $item['id']; ?>">
                                <input type="hidden" class="prod_name" value="<?= $item['name']; ?>">
                                <input type="hidden" class="prod_price" value="<?= $item['selling_price']; ?>">
                                <input type="hidden" class="prod_image" value="<?= $item['image']; ?>">
                                <input type="hidden" class="input-qty" value="1">
                                <div class="d-grid gap-2">
                                    <?php if($item['qty'] > 0) { ?>
                                        <?php if(isset($_SESSION['auth_user'])) { ?>
                                            <button class="btn btn-primary addToCartBtn" value="<?= $item['id']; ?>">
                                                Mua ngay
                                            </button>
                                        <?php } else { ?>
                                            <a href="login.php" class="btn btn-primary">Đăng nhập để mua</a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <button class="btn btn-secondary" disabled>Hết hàng</button>
                                    <?php } ?>
                                    <a href="product-detail.php?slug=<?= $item['slug']; ?>" 
                                       class="btn btn-outline-primary">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<div class='text-center'>Không có sản phẩm nào</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    // Xóa đoạn script cũ
});
</script>

<script>
  // Configs
  let liveChatBaseUrl   = document.location.protocol + '//' + 'livechat.fpt.ai/v36/src';
  let LiveChatSocketUrl = 'livechat.fpt.ai:443';
  let FptAppCode        = '31c45b4e1c2755587f3714e243df6c9d';
  let FptAppName        = 'Bicycle Shop';
  // Define custom styles
  let CustomStyles = {
      // header
      headerBackground: 'linear-gradient(86.7deg, #3353a2ff 0.85%, #31b7b7ff 98.94%)',
      headerTextColor: '#ffffffff',
      headerLogoEnable: false,
      headerLogoLink: 'https://chatbot-tools.fpt.ai/livechat-builder/img/Icon-fpt-ai.png',
      headerText: 'Hỗ trợ trực tuyến',
      // main
      primaryColor: '#6d9ccbff',
      secondaryColor: '#ecececff',
      primaryTextColor: '#ffffffff',
      secondaryTextColor: '#000000DE',
      buttonColor: '#b4b4b4ff',
      buttonTextColor: '#ffffffff',
      bodyBackgroundEnable: true,
      bodyBackgroundLink: '#6d9ccbff',
      avatarBot: 'https://chatbot-tools.fpt.ai/livechat-builder/img/bot.png',
      sendMessagePlaceholder: 'Nhập tin nhắn',
      // float button
      floatButtonLogo: 'https://chatbot-tools.fpt.ai/livechat-builder/img/Icon-fpt-ai.png',
      floatButtonTooltip: 'FPT.AI xin chào',
      floatButtonTooltipEnable: false,
      // start screen
      customerLogo: 'https://chatbot-tools.fpt.ai/livechat-builder/img/bot.png',
      customerWelcomeText: 'Vui lòng nhập tên của bạn',
      customerButtonText: 'Bắt đầu',
      prefixEnable: false,
      prefixType: 'radio',
      prefixOptions: ["Anh","Chị"],
      prefixPlaceholder: 'Danh xưng',
      // custom css
      css: ''
  };
  // Get bot code from url if FptAppCode is empty
  if (!FptAppCode) {
      let appCodeFromHash = window.location.hash.substr(1);
      if (appCodeFromHash.length === 32) {
          FptAppCode = appCodeFromHash;
      }
  }
  // Set Configs
  let FptLiveChatConfigs = {
      appName: FptAppName,
      appCode: FptAppCode,
      themes: '',
      styles: CustomStyles
  };
  // Append Script
  let FptLiveChatScript  = document.createElement('script');
  FptLiveChatScript.id   = 'fpt_ai_livechat_script';
  FptLiveChatScript.src  = liveChatBaseUrl + '/static/fptai-livechat.js';
  document.body.appendChild(FptLiveChatScript);
  // Append Stylesheet
  let FptLiveChatStyles  = document.createElement('link');
  FptLiveChatStyles.id   = 'fpt_ai_livechat_script';
  FptLiveChatStyles.rel  = 'stylesheet';
  FptLiveChatStyles.href = liveChatBaseUrl + '/static/fptai-livechat.css';
  document.body.appendChild(FptLiveChatStyles);
  // Init
  FptLiveChatScript.onload = function () {
      fpt_ai_render_chatbox(FptLiveChatConfigs, liveChatBaseUrl, LiveChatSocketUrl);
  };
</script>
</body>
</html>